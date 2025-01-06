<?php
namespace app\controller;

use app\config\Constants;
use app\config\Urls;
use app\data\EntryCategoriesConstants;
use app\data\Input;
use app\data\Pagination;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\data\Validate;
use app\model\EntryCommentModel;
use app\model\EntryModel;
use app\security\Session;

class EntryController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'articlesView' => [UserLevelsConstants::ALL],
		'faqsView' => [UserLevelsConstants::ALL],
		'newsView' => [UserLevelsConstants::ALL],
		'previewsView' => [UserLevelsConstants::ALL],
		'reviewsView' => [UserLevelsConstants::ALL],
		'rumoursView' => [UserLevelsConstants::ALL],
		'showView' => [UserLevelsConstants::ALL],
		'newCommentAction' => [UserLevelsConstants::REGISTER]
	];
	
	public function newCommentAction(string $path, int $id): void {
		$entry = EntryModel::getById($id);
		// Si la entrada no existe, se redirige al usuario a la página de error.
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $entry === null || strtolower($entry->entry_category) != $path);
		// Se recupera el último comentario que ha realizado el usuario autenticado.
		$latestComment = EntryCommentModel::getLatestByUser($this->_context['CLIENT']['LOGIN_USER']->id);
		// Se comprueba que haya transcurrido el tiempo necesario para publicar un nuevo comentario.
		if ($latestComment !== null && (strtotime($latestComment->comment_published) * 1000 > time() * 1000 - Constants::ENTRY_MIN_TIME_TO_POST_IN_SECONDS * 1000)) {
			Session::put(Constants::SESSION_ERROR_MESSAGES, [
				sprintf('Para enviar un nuevo comentario tienes que esperar %s segundos desde el último que has publicado.', Constants::ENTRY_MIN_TIME_TO_POST_IN_SECONDS)
			]);
		} else {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'EntryController-showView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'content' => ['label' => LANG_CONTENT, 'max' => 65535, 'min' => 10, 'required' => true],
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$insert = EntryCommentModel::insert(
					Input::getPost('content'),
					$id,
					$this->_context['CLIENT']['LOGIN_USER']->id,
				);
				if(!$insert) {
					Input::cleanPost('content');
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, 'Tu comentario se ha publicado correctamente.');
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ENTRY_VIEW_SHOW, [$path, $id, !empty(Input::get('page')) ? Input::get('page') : 1]);
	}

	public function listView(int $page, string $type) : void {
		switch($type) {
			case EntryCategoriesConstants::NEWS:
				self::setEntryContextByType(LANG_NEWS, EntryCategoriesConstants::NEWS, $page, UrlsEnum::ENTRY_LIST_VIEW);
			break;
			case EntryCategoriesConstants::ARTICLE:
				self::setEntryContextByType(LANG_ARTICLES, EntryCategoriesConstants::ARTICLE, $page, UrlsEnum::ENTRY_LIST_VIEW);
			break;
			case EntryCategoriesConstants::FAQ:
				self::setEntryContextByType(LANG_FAQS, EntryCategoriesConstants::FAQ, $page, UrlsEnum::ENTRY_LIST_VIEW);
			break;
			case EntryCategoriesConstants::PREVIEW:
				self::setEntryContextByType(LANG_PREVIEWS, EntryCategoriesConstants::PREVIEW, $page, UrlsEnum::ENTRY_LIST_VIEW);
			break;
			case EntryCategoriesConstants::REVIEW:
				self::setEntryContextByType(LANG_REVIEWS, EntryCategoriesConstants::REVIEW, $page, UrlsEnum::ENTRY_LIST_VIEW);
			break;
			case EntryCategoriesConstants::RUMOUR:
				self::setEntryContextByType(LANG_RUMOURS, EntryCategoriesConstants::RUMOUR, $page, UrlsEnum::ENTRY_LIST_VIEW);
			break;
			default:
				Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
			break;
		}
	}
	private function setEntryContextByType(string $label, string $type, int $page, UrlsEnum $url) : void {
		$this->_context['META']['TITLE'] .= " - {$label}";
		$this->_context['TITLE'] = $label;
		$this->_context['ENTRIES'] = EntryModel::getLimitByCategory($type, ($page - 1) * Constants::ELEMENTS_PER_PAGE, Constants::ELEMENTS_PER_PAGE);
		// Si no se recuperan entradas, se redirige a la página de error 404.
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $page !== 1 && sizeof($this->_context['ENTRIES']) === 0);
		$this->_context['ENTRIES_TOTAL'] = EntryModel::getTotalByCategory($type);
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['ENTRIES'], $page, $this->_context['ENTRIES_TOTAL'], $url, Constants::ELEMENTS_PER_PAGE, [$type]);
	}
	
	public function showView(string $path, int $id, int $page) : void {
		$this->_context['ENTRY'] = EntryModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['ENTRY'] === null);
		switch(strtolower($this->_context['ENTRY']->entry_category)) {
			case EntryCategoriesConstants::NEWS:
				$this->_context['ENTRY_CATEGORY_NAME'] = LANG_ENTRYCAT_NEWS;
				$this->_context['ENTRYLIST_LINK_NAME'] = LANG_NEWS;
			break;
			case EntryCategoriesConstants::ARTICLE:
				$this->_context['ENTRY_CATEGORY_NAME'] = LANG_ENTRYCAT_ARTICLE;
				$this->_context['ENTRYLIST_LINK_NAME'] = LANG_ARTICLES;
			break;
			case EntryCategoriesConstants::FAQ:
				$this->_context['ENTRY_CATEGORY_NAME'] = LANG_ENTRYCAT_FAQ;
				$this->_context['ENTRYLIST_LINK_NAME'] = LANG_FAQS;
			break;
			case EntryCategoriesConstants::PREVIEW:
				$this->_context['ENTRY_CATEGORY_NAME'] = LANG_ENTRYCAT_PREVIEW;
				$this->_context['ENTRYLIST_LINK_NAME'] = LANG_PREVIEWS;
			break;
			case EntryCategoriesConstants::REVIEW:
				$this->_context['ENTRY_CATEGORY_NAME'] = LANG_ENTRYCAT_REVIEW;
				$this->_context['ENTRYLIST_LINK_NAME'] = LANG_REVIEWS;
			break;
			case EntryCategoriesConstants::RUMOUR:
				$this->_context['ENTRY_CATEGORY_NAME'] = LANG_ENTRYCAT_RUMOUR;
				$this->_context['ENTRYLIST_LINK_NAME'] = LANG_RUMOURS;
				break;
			default:
				Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
			break;
		}
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $path !== $this->_context['ENTRY']->entry_category);
		$this->_context['ENTRY_COMMENTS'] = EntryCommentModel::getEntryLimitComments($id, ($page - 1) * Constants::ELEMENTS_PER_PAGE, Constants::ELEMENTS_PER_PAGE);
		$this->_context['ENTRY_COMMENTS_TOTAL'] = EntryCommentModel::getTotalEntryComments($id);
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['ENTRY_COMMENTS'], $page, $this->_context['ENTRY_COMMENTS_TOTAL'], UrlsEnum::ENTRY_VIEW_SHOW, Constants::ELEMENTS_PER_PAGE, [ $id ]);
		$this->_context['FORMDATA'] = [
			'CONTENT' => empty(Session::get(Constants::SESSION_SUCCESS_MESSAGES)) ? Input::getPost('content') : ''
		];
	}
}
