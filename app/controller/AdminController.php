<?php
namespace app\controller;

use app\config\Constants;
use app\config\Urls;
use app\data\AccountGroupsConstants;
use app\data\AccountStatesConstants;
use app\data\EntryCategoriesConstants;
use app\data\InfoTypesConstants;
use app\data\Input;
use app\data\Pagination;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\data\Validate;
use app\model\CheatModel;
use app\model\CompanyModel;
use app\model\EntryModel;
use app\model\GameModel;
use app\model\InputDataModel;
use app\model\MemberModel;
use app\model\MetadataModel;
use app\model\MetagroupModel;
use app\model\PlatformModel;
use app\model\VisitorModel;
use app\security\Hash;
use app\security\Security;
use app\security\Session;

class AdminController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'indexView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER, UserLevelsConstants::WRITER],
		'cheatsAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'cheatsAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'cheatsEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'cheatsEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'cheatsListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'companiesAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'companiesAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'companiesEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'companiesEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'companiesListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'entriesAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER, UserLevelsConstants::WRITER],
		'entriesAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER, UserLevelsConstants::WRITER],
		'entriesEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER, UserLevelsConstants::WRITER],
		'entriesEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER, UserLevelsConstants::WRITER],
		'entriesListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER, UserLevelsConstants::WRITER],
		'gamesAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'gamesAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'gamesEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'gamesEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'gamesListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'membersAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'membersAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'membersEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'membersEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'membersListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metadatasAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metadatasAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metadatasEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metadatasEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metadatasListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metagroupsAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metagroupsAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metagroupsEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metagroupsEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'metagroupsListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'platformsAddAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'platformsAddView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'platformsEditAction' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'platformsEditView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER],
		'platformsListView' => [UserLevelsConstants::ADMIN, UserLevelsConstants::MANAGER]
	];
	
	private $_infoTypes = [
		InfoTypesConstants::TYPE_TEXT => LANG_TEXT,
		InfoTypesConstants::TYPE_IMAGE_PNG => LANG_IMAGE_PNG
	];
	
	private $_infoTypesValues = [InfoTypesConstants::TYPE_TEXT, InfoTypesConstants::TYPE_IMAGE_PNG];
	
	private $_accountGroups = [
		AccountGroupsConstants::USER => LANG_ROLE_USER,
		AccountGroupsConstants::WRITER => LANG_ROLE_WRITER,
		AccountGroupsConstants::MODERATOR => LANG_ROLE_MODERATOR,
		AccountGroupsConstants::MANAGER => LANG_ROLE_MANAGER
	];
	
	private $_accountGroupsValues = [
		AccountGroupsConstants::USER,
		AccountGroupsConstants::WRITER,
		AccountGroupsConstants::MODERATOR,
		AccountGroupsConstants::MANAGER
	];
	
	private $_accountStates = [
		AccountStatesConstants::ACTIVED => LANG_STATE_ACTIVED,
		AccountStatesConstants::BANNED => LANG_STATE_BANNED,
		AccountStatesConstants::DEACTIVATED => LANG_STATE_DEACTIVATED,
		AccountStatesConstants::LOCKED => LANG_STATE_LOCKED
	];
	
	private $_accountStatesValues = [
		AccountStatesConstants::ACTIVED,
		AccountStatesConstants::BANNED,
		AccountStatesConstants::DEACTIVATED,
		AccountStatesConstants::LOCKED
	];
	
	private $_entryCategories = [
		EntryCategoriesConstants::ARTICLE => LANG_ENTRYCAT_ARTICLE,
		EntryCategoriesConstants::FAQ => LANG_ENTRYCAT_FAQ,
		EntryCategoriesConstants::NEWS => LANG_ENTRYCAT_NEWS,
		EntryCategoriesConstants::PREVIEW => LANG_ENTRYCAT_PREVIEW,
		EntryCategoriesConstants::REVIEW => LANG_ENTRYCAT_REVIEW,
		EntryCategoriesConstants::RUMOUR => LANG_ENTRYCAT_RUMOUR
	];
	
	private $_entryCategoriesValues = [
		EntryCategoriesConstants::ARTICLE,
		EntryCategoriesConstants::FAQ,
		EntryCategoriesConstants::NEWS,
		EntryCategoriesConstants::PREVIEW,
		EntryCategoriesConstants::REVIEW,
		EntryCategoriesConstants::RUMOUR
	];
	
	public function indexView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL;
		$this->_context['STATS']['ONLINE_USERS'] = MemberModel::getTotalOnline();
		$this->_context['STATS']['REGISTERED_USERS'] = MemberModel::getTotal();
		$this->_context['STATS']['UNIQUE_VISITS'] = VisitorModel::getTotalUniqueVisits();
		$this->_context['STATS']['VISITS'] = VisitorModel::getTotalVisits();
		
	}
	
	/* CHEATS */
	
	public function cheatsAddAction() : void {
		$gamesIds = [];
		foreach(GameModel::selectAll() as $row) {
			array_push($gamesIds, $row->game_id);
		}
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-cheatsAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'title' => ['label' => LANG_TITLE, 'max' => 128, 'required' => true],
				'description' => [ 'label' => LANG_DESCRIPTION, 'max' => 65535, 'required' => true],
				'game' => ['label' => LANG_GAME, 'pattern' => 'integer', 'required' => true, 'values' => $gamesIds]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$insert = CheatModel::insert(
					Input::getPost('title'),
					Input::getPost('description'),
					$this->_context['CLIENT']['LOGIN_USER']->id,
					Input::getPost('game')
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('title')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTCHEAT, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDCHEAT);
	}
	
	public function cheatsAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_CHEAT . ')';
		$this->_context['GAMES'] = GameModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'TITLE' => Input::getPost('title'),
			'DESCRIPTION' => Input::getPost('description'),
			'GAME' => Input::getPost('game')
		];
	}

	public function cheatsEditAction(int $id) : void {
		$this->_context['CHEAT'] = CheatModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['CHEAT'] === null);
		$this->_context['GAMES'] = GameModel::selectAll();
		$gamesIds = [];
		foreach(GameModel::selectAll() as $row) {
			array_push($gamesIds, $row->game_id);
		}
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-cheatsEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'title' => ['label' => LANG_TITLE, 'max' => 128, 'required' => true],
				'description' => [ 'label' => LANG_DESCRIPTION, 'max' => 65535, 'required' => true],
				'game' => ['label' => LANG_GAME, 'pattern' => 'integer', 'required' => true, 'values' => $gamesIds]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$update = CheatModel::update(
					$id,
					Input::getPost('title'),
					Input::getPost('description'),
					$this->_context['CHEAT']->member_id,
					Input::getPost('game')
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('title')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITCHEAT, [$id]);
	}
	
	public function cheatsEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_CHEAT . ')';
		$this->_context['CHEAT'] = CheatModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['CHEAT'] === null);
		$this->_context['GAMES'] = GameModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'TITLE' => Input::getPostWithDefaultValue('title', $this->_context['CHEAT']->cheat_title),
			'DESCRIPTION' => Input::getPostWithDefaultValue('description', $this->_context['CHEAT']->cheat_description),
			'GAME' => Input::getPostWithDefaultValue('game', $this->_context['CHEAT']->cheat_game)
		];
	}
	
	public function cheatsListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_CHEATS . ')';
		$this->_context['CHEATS'] = CheatModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['CHEATS_TOTAL'] = CheatModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['CHEATS'], $page, $this->_context['CHEATS_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTCHEAT, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}
	
	/* COMPANIES */
	
	public function companiesAddAction() : void {
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-companiesAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 100, 'required' => true]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$insert = CompanyModel::insert(
					Input::getPost('name')
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('name')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTCOMPANY, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDCOMPANY);
	}
	
	public function companiesAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_COMPANY . ')';
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'NAME' => Input::getPost('name')
		];
	}

	public function companiesEditAction(int $id) : void {
		$company = CompanyModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $company === null);
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-companiesEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 100, 'required' => true]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$update = CompanyModel::update(
					$id,
					Input::getPost('name')
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('name')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITCOMPANY, [$id]);
	}
	
	public function companiesEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_COMPANY . ')';
		$this->_context['COMPANY'] = CompanyModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['COMPANY'] === null);
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'NAME' => Input::getPostWithDefaultValue('name', $this->_context['COMPANY']->company_name)
		];
	}
	
	public function companiesListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_COMPANIES . ')';
		$this->_context['COMPANIES'] = CompanyModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['COMPANIES_TOTAL'] = CompanyModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['COMPANIES'], $page, $this->_context['COMPANIES_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTCOMPANY, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}
	
	/* ENTRIES */
	
	public function entriesAddAction() : void {
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-entriesAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'title' => ['label' => LANG_TITLE, 'max' => 200, 'required' => true],
				'category' => ['label' => LANG_CATEGORY, 'required' => true, 'values' => $this->_entryCategoriesValues],
				'resume' => ['label' => LANG_RESUME, 'max' => 500, 'required' => true],
				'content' => ['label' => LANG_CONTENT, 'max' => 65535, 'required' => true],
				'game' => ['label' => LANG_GAME, 'pattern' => 'integer'],
				'platform' => ['label' => LANG_PLATFORM, 'pattern' => 'integer'],
				'score' => ['label' => LANG_SCORE, 'pattern' => 'integer']
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else if(!empty(Input::getPost('game')) && GameModel::getById(Input::getPost('game')) === null) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_GAME_NOT_EXISTS]);
			} else if(!empty(Input::getPost('platform')) && PlatformModel::getById(Input::getPost('platform')) === null) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_PLATFORM_NOT_EXISTS]);
			} else {
				$insert = EntryModel::insert(
					Input::getPost('title'),
					Input::getPost('category'),
					Input::getPost('resume'),
					Input::getPost('content'),
					$this->_context['CLIENT']['LOGIN_USER']->id,
					Input::getPostWithDefaultValue('game', null),
					Input::getPostWithDefaultValue('platform', null),
					Input::getPostWithDefaultValue('score', null)
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('title')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTENTRY, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDENTRY);
	}
	
	public function entriesAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_ENTRY . ')';
		$this->_context['CATEGORIES'] = $this->_entryCategories;
		$this->_context['GAMES'] = GameModel::selectAll();
		$this->_context['PLATFORMS'] = PlatformModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'CATEGORY' => Input::getPost('category'),
			'TITLE' => Input::getPost('title'),
			'RESUME' => Input::getPost('resume'),
			'CONTENT' => Input::getPost('content'),
			'GAME' => Input::getPost('game'),
			'PLATFORM' => Input::getPost('platform'),
			'SCORE' => Input::getPost('score')
		];
	}

	public function entriesEditAction(int $id) : void {
		$entry = EntryModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $entry === null);
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-entriesEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'title' => ['label' => LANG_TITLE, 'max' => 200, 'required' => true],
				'category' => ['label' => LANG_CATEGORY, 'required' => true, 'values' => $this->_entryCategoriesValues],
				'resume' => ['label' => LANG_RESUME, 'max' => 500, 'required' => true],
				'content' => ['label' => LANG_CONTENT, 'max' => 65535, 'required' => true],
				'game' => ['label' => LANG_GAME, 'pattern' => 'integer'],
				'platform' => ['label' => LANG_PLATFORM, 'pattern' => 'integer'],
				'score' => ['label' => LANG_SCORE, 'pattern' => 'integer']
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else if(!empty(Input::getPost('game')) && GameModel::getById(Input::getPost('game')) === null) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_GAME_NOT_EXISTS]);
			} else if(!empty(Input::getPost('platform')) && PlatformModel::getById(Input::getPost('platform')) === null) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_PLATFORM_NOT_EXISTS]);
			} else {
				$update = EntryModel::update(
					$id,
					Input::getPost('title'),
					Input::getPost('category'),
					Input::getPost('resume'),
					Input::getPost('content'),
					$this->_context['CLIENT']['LOGIN_USER']->id,
					Input::getPostWithDefaultValue('game', null),
					Input::getPostWithDefaultValue('platform', null),
					Input::getPostWithDefaultValue('score', null)
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('title')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITENTRY, [$id]);
	}
	
	public function entriesEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_COMPANY . ')';
		$this->_context['ENTRY'] = EntryModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['ENTRY'] === null);
		$this->_context['CATEGORIES'] = $this->_entryCategories;
		$this->_context['GAMES'] = GameModel::selectAll();
		$this->_context['PLATFORMS'] = PlatformModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'CATEGORY' => Input::getPostWithDefaultValue('category', $this->_context['ENTRY']->entry_category),
			'TITLE' => Input::getPostWithDefaultValue('title', $this->_context['ENTRY']->entry_title),
			'RESUME' => Input::getPostWithDefaultValue('resume', $this->_context['ENTRY']->entry_resume),
			'CONTENT' => Input::getPostWithDefaultValue('content', $this->_context['ENTRY']->entry_content),
			'GAME' => Input::getPostWithDefaultValue('game', $this->_context['ENTRY']->game_id),
			'PLATFORM' => Input::getPostWithDefaultValue('platform', $this->_context['ENTRY']->platform_id),
			'SCORE' => Input::getPostWithDefaultValue('score', $this->_context['ENTRY']->entry_score)
		];
	}
	
	public function entriesListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_ENTRIES . ')';
		$this->_context['ENTRIES'] = EntryModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['ENTRIES_TOTAL'] = EntryModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['ENTRIES'], $page, $this->_context['ENTRIES_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTENTRY, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}
	
	/* GAMES */

	public function gamesAddAction() : void {
		$companiesIds = [];
		foreach(InputDataModel::getCompanies() as $row) {
			array_push($companiesIds, $row['value']);
		}
		$metadataIds = [];
		foreach(InputDataModel::getMetadata() as $row) {
			array_push($metadataIds, $row['value']);
		}
		$platformIds = [];
		foreach(PlatformModel::selectAll() as $row) {
			array_push($platformIds, $row->platform_id);
		}
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-gamesAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 128, 'required' => true],
				'resume' => [ 'label' => LANG_DESCRIPTION, 'max' => 65535],
				'platform' => ['label' => LANG_PLATFORM, 'pattern' => 'integer', 'required' => true, 'values' => $platformIds],
				'metadata' => ['label' => LANG_METADATA, 'values' => $metadataIds],
				'developers' => ['label' => LANG_DEVELOPER, 'values' => $companiesIds],
				'publishers' => ['label' => LANG_PUBLISHER, 'values' => $companiesIds],
				'group' => ['label' => LANG_GROUP, 'pattern' => 'integer'],
				'release-year' => ['label' => LANG_YEAR, 'pattern' => 'integer', 'size' => 4],
				'release-month' => ['label' => LANG_MONTH, 'pattern' => 'month_of_year'],
				'release-day' => ['label' => LANG_DAY, 'pattern' => 'day_of_month'],
				'release-price' => ['label' => LANG_RELEASE_PRICE, 'max' => 16]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$insert = GameModel::insert(
					Input::getPostWithDefaultValue('group', null),
					Input::getPost('platform'),
					Input::getPost('name'),
					Input::getPostWithDefaultValue('resume', null),
					Input::getPostWithDefaultValue('release-year', null),
					Input::getPostWithDefaultValue('release-month', null),
					Input::getPostWithDefaultValue('release-day', null),
					Input::getPostWithDefaultValue('release-price', null),
					!empty($_FILES['cover']) ? $_FILES['cover'] : [],
					Input::getPostWithDefaultValue('metadata', []),
					Input::getPostWithDefaultValue('developers', []),
					Input::getPostWithDefaultValue('publishers', [])
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('name')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTGAME, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDGAME);
	}
	
	public function gamesAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_GAME . ')';
		$this->_context['COMPANIES'] = InputDataModel::getCompanies();
		$this->_context['METADATA'] = InputDataModel::getMetadata();
		$this->_context['PLATFORMS'] = PlatformModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'GROUP' => Input::getPost('group'),
			'NAME' => Input::getPost('name'),
			'RESUME' => Input::getPost('resume'),
			'PLATFORM' => Input::getPost('platform'),
			'RELEASE_YEAR' => Input::getPost('release-year'),
			'RELEASE_MONTH' => Input::getPost('release-month'),
			'RELEASE_DAY' => Input::getPost('release-day'),
			'RELEASE_PRICE' => Input::getPost('release-price'),
			'DEVELOPERS' => Input::getPostWithDefaultValue('developers', []),
			'PUBLISHERS' => Input::getPostWithDefaultValue('publishers', []),
			'METADATA' => Input::getPostWithDefaultValue('metadata', [])
		];
	}

	public function gamesEditAction(int $id) : void {
		$this->_context['GAME'] = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['GAME'] === null);
		$this->_context['COMPANIES'] = InputDataModel::getCompanies();
		$this->_context['METADATA'] = InputDataModel::getMetadata();
		$this->_context['PLATFORMS'] = PlatformModel::selectAll();
		$companiesIds = [];
		foreach($this->_context['COMPANIES'] as $row) {
			array_push($companiesIds, $row->value);
		}
		$metadataIds = [];
		foreach($this->_context['METADATA'] as $row) {
			array_push($metadataIds, $row->value);
		}
		$platformIds = [];
		foreach(PlatformModel::selectAll() as $row) {
			array_push($platformIds, $row->platform_id);
		}
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-gamesEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 128, 'required' => true],
				'resume' => ['label' => LANG_DESCRIPTION, 'max' => 65535],
				'platform' => ['label' => LANG_PLATFORM, 'pattern' => 'integer', 'required' => true, 'values' => $platformIds],
				'metadata' => ['label' => LANG_METADATA, 'values' => $metadataIds],
				'developers' => ['label' => LANG_DEVELOPER, 'values' => $companiesIds],
				'publishers' => ['label' => LANG_PUBLISHER, 'values' => $companiesIds],
				'group' => ['label' => LANG_GROUP, 'pattern' => 'integer'],
				'release-year' => ['label' => LANG_YEAR, 'pattern' => 'integer', 'size' => 4],
				'release-month' => ['label' => LANG_MONTH, 'pattern' => 'month_of_year'],
				'release-day' => ['label' => LANG_DAY, 'pattern' => 'day_of_month'],
				'release-price' => ['label' => LANG_RELEASE_PRICE, 'max' => 16]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$update = GameModel::update(
					$id,
					Input::getPostWithDefaultValue('group', null),
					Input::getPost('platform'),
					Input::getPost('name'),
					Input::getPostWithDefaultValue('resume', null),
					Input::getPostWithDefaultValue('release-year', null),
					Input::getPostWithDefaultValue('release-month', null),
					Input::getPostWithDefaultValue('release-day', null),
					Input::getPostWithDefaultValue('release-price', null),
					!empty($_FILES['cover']) ? $_FILES['cover'] : [],
					Input::getPostWithDefaultValue('metadata', []),
					Input::getPostWithDefaultValue('developers', []),
					Input::getPostWithDefaultValue('publishers', [])
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('name')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITGAME, [$id]);
	}
	
	public function gamesEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_GAMES . ')';
		$this->_context['GAME'] = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['GAME'] === null);
		$this->_context['COMPANIES'] = InputDataModel::getCompanies();
		$this->_context['METADATA'] = InputDataModel::getMetadata();
		$this->_context['PLATFORMS'] = PlatformModel::selectAll();
		$developersList = [];
		$publishersList = [];
		$metadataList = [];
		$companiesList = GameModel::getCompanies($id);
		foreach($companiesList as $row) {
			if($row->company_category === 'DEVELOPER') {
				array_push($developersList, $row->company_id);
			} else if($row->company_category === 'PUBLISHER') {
				array_push($publishersList, $row->company_id);
			}
		}
		$metadata = GameModel::getMetadata($id);
		foreach($metadata as $row) {
			array_push($metadataList, $row->metadata_id);
		}
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'GROUP' => Input::getPostWithDefaultValue('group', $this->_context['GAME']->game_gamegroup),
			'NAME' => Input::getPostWithDefaultValue('name', $this->_context['GAME']->game_name),
			'RESUME' => Input::getPostWithDefaultValue('resume', $this->_context['GAME']->game_resume),
			'PLATFORM' => $this->_context['GAME']->platform_id,
			'RELEASE_YEAR' => Input::getPostWithDefaultValue('release-year', $this->_context['GAME']->game_release_year),
			'RELEASE_MONTH' => Input::getPostWithDefaultValue('release-month', $this->_context['GAME']->game_release_month),
			'RELEASE_DAY' => Input::getPostWithDefaultValue('release-day', $this->_context['GAME']->game_release_day),
			'RELEASE_PRICE' => Input::getPostWithDefaultValue('release-price', $this->_context['GAME']->game_release_price),
			'DEVELOPERS' => Input::getPostWithDefaultValue('developers', $developersList),
			'PUBLISHERS' => Input::getPostWithDefaultValue('publishers', $publishersList),
			'METADATA' => Input::getPostWithDefaultValue('metadata', $metadataList)
		];
	}
	
	public function gamesListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_GAMES . ')';
		$this->_context['GAMES'] = GameModel::getFilteredLimit('all', 'all', ($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['GAMES_TOTAL'] = GameModel::getTotalFilteredGames('all', 'all');
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['GAMES'], $page, $this->_context['GAMES_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTGAME, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}

	/* USERS */
	
	public function membersAddAction() : void {
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-membersAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'username' => ['label' => LANG_USERNAME, 'required' => true, 'pattern' => 'username'],
				'email' => ['label' => LANG_EMAIL, 'required' => true, 'max' => 100, 'min' => 4],
				'inbox_max' => ['label' => LANG_INBOX_SIZE, 'required' => true, 'pattern' => 'integer'],
				'account_group' => ['label' => LANG_GROUP, 'required' => true, 'values' => $this->_accountGroupsValues],
				'account_state' => ['label' => LANG_STATE, 'required' => true, 'values' => $this->_accountStatesValues]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				// Se genera la contraseña y el salt.
				$generatedPassword = Security::generateRandomString(16);
				$generatedSalt = Security::generateRandomString(16);
				// Se da de alta el registro en la base de datos
				$insert = MemberModel::insert(
					Input::getPost('username'),
					Hash::make($generatedPassword, $generatedSalt),
					$generatedSalt,
					Input::getPost('email'),
					Input::getPost('inbox_max'),
					Input::getPost('account_state'),
					Input::getPost('account_group'),
					isset($_FILES['avatar']) ? $_FILES['avatar'] : [],
				);
				if(sizeof($insert) === 0) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('username')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTMEMBER, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, $insert);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDMEMBER);
	}
	
	public function membersAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_USER . ')';
		$this->_context['ACCOUNT_GROUPS'] = $this->_accountGroups;
		$this->_context['ACCOUNT_STATES'] = $this->_accountStates;
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'USERNAME' => Input::getPost('username'),
			'EMAIL' => Input::getPost('email'),
			'INBOX_MAX' => Input::getPost('inbox_max'),
			'ACCOUNT_STATE' => strtoupper(Input::getPost('account_state')),
			'ACCOUNT_GROUP' => strtoupper(Input::getPost('account_group'))
		];
	}

	public function membersEditAction(int $id) : void {
		$member = MemberModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $member === null || $member->account_group == AccountGroupsConstants::ADMIN);
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-membersEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'email' => ['label' => LANG_EMAIL, 'required' => true, 'max' => 100, 'min' => 4],
				'inbox_max' => ['label' => LANG_INBOX_SIZE, 'required' => true, 'pattern' => 'integer'],
				'account_group' => ['label' => LANG_GROUP, 'required' => true, 'values' => $this->_accountGroupsValues],
				'account_state' => ['label' => LANG_STATE, 'required' => true, 'values' => $this->_accountStatesValues]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$update = MemberModel::update(
					$member->username,
					Input::getPost('email'),
					Input::getPost('inbox_max'),
					strtoupper(Input::getPost('account_group')),
					strtoupper(Input::getPost('account_state')),
					isset($_FILES['avatar']) ? $_FILES['avatar'] : [],
				);
				if(sizeof($update) === 0) {
					// TODO: enviar email y, si no se envía, noticar con warning que se ha creado el usuario pero no se ha enviado el mail.
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, $member->username));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, $update);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITMEMBER, [$id]);
	}
	
	public function membersEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_USER . ')';
		$this->_context['MEMBER'] = MemberModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['MEMBER'] === null);
		$this->_context['ACCOUNT_GROUPS'] = $this->_accountGroups;
		$this->_context['ACCOUNT_STATES'] = $this->_accountStates;
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'EMAIL' => Input::getPostWithDefaultValue('email', $this->_context['MEMBER']->email),
			'INBOX_MAX' => Input::getPostWithDefaultValue('inbox_max', $this->_context['MEMBER']->inbox_max),
			'ACCOUNT_STATE' => Input::getPostWithDefaultValue('account_state', $this->_context['MEMBER']->account_state),
			'ACCOUNT_GROUP' => Input::getPostWithDefaultValue('account_group', $this->_context['MEMBER']->account_group)
		];
	}
	
	public function membersListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_USERS . ')';
		$this->_context['MEMBERS'] = MemberModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['MEMBERS_TOTAL'] = MemberModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['MEMBERS'], $page, $this->_context['MEMBERS_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTMEMBER, Constants::ADMIN_ELEMENTS_PER_PAGE, []);

	}
	
	/* METADATAS */

	public function metadatasAddAction() : void {
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-metadatasAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'value' => ['label' => LANG_VALUE, 'max' => 200, 'required' => true],
				'group' => ['label' => LANG_GROUP, 'pattern' => 'integer', 'required' => true]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else if(!empty(Input::getPost('group')) && MetagroupModel::getById(Input::getPost('group')) === null) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_METAGROUP_NOT_EXISTS]);
			} else {
				$insert = MetadataModel::insert(
					Input::getPost('value'),
					Input::getPost('group')
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('value')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTMETADATA, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDMETADATA);
	}
	
	public function metadatasAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_METADATA . ')';
		$this->_context['METAGROUPS'] = MetagroupModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'VALUE' => Input::getPost('value'),
			'GROUP' => Input::getPost('group')
		];
	}

	public function metadatasEditAction(int $id) : void {
		$metadata = MetadataModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $metadata === null);
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-metadatasEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'value' => ['label' => LANG_VALUE, 'max' => 200, 'required' => true],
				'group' => ['label' => LANG_GROUP, 'pattern' => 'integer', 'required' => true]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else if(!empty(Input::getPost('group')) && MetagroupModel::getById(Input::getPost('group')) === null) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_METAGROUP_NOT_EXISTS]);
			} else {
				$update = MetadataModel::update(
					$id,
					Input::getPost('value'),
					Input::getPost('group')
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('value')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITMETADATA, [$id]);
	}
	
	public function metadatasEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_METADATA . ')';
		$this->_context['METADATA'] = MetadataModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['METADATA'] === null);
		$this->_context['METAGROUPS'] = MetagroupModel::selectAll();
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'VALUE' => Input::getPostWithDefaultValue('value', $this->_context['METADATA']->metadata_value),
			'GROUP' => Input::getPostWithDefaultValue('group', $this->_context['METADATA']->metagroup_id)
		];
	}
	
	public function metadatasListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_METADATAS . ')';
		$this->_context['METADATAS'] = MetadataModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['METADATAS_TOTAL'] = MetadataModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['METADATAS'], $page, $this->_context['METADATAS_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTMETADATA, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}
	
	/* METAGROUPS */
	
	public function metagroupsAddAction() : void {
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-metagroupsAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 100, 'required' => true],
				'tag' => ['tag' => LANG_TAG, 'max' => 100, 'required' => true],
				'infotype' => ['label' => LANG_TYPE, 'required' => true, 'values' => $this->_infoTypesValues],
				'relevance' => ['label' => LANG_RELEVANCE, 'pattern' => 'integer']
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$insert = MetagroupModel::insert(
					Input::getPost('name'),
					Input::getPost('tag'),
					Input::getPost('infotype'),
					Input::getPost('relevance')
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('name')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTMETAGROUP, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDMETAGROUP);
	}
	
	public function metagroupsAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_METAGROUP . ')';
		$this->_context['INFO_TYPES'] = $this->_infoTypes;
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'NAME' => Input::getPost('name'),
			'TAG' => Input::getPost('tag'),
			'RELEVANCE' => Input::getPost('relevance'),
			'INFOTYPE' => Input::getPost('infotype')
		];
	}

	public function metagroupsEditAction(int $id) : void {
		$metagroup = MetagroupModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $metagroup === null);
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-metagroupsEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 100, 'required' => true],
				'tag' => ['tag' => LANG_TAG, 'max' => 100, 'required' => true],
				'infotype' => ['label' => LANG_TYPE, 'required' => true, 'values' => $this->_infoTypesValues],
				'relevance' => ['label' => LANG_RELEVANCE, 'pattern' => 'integer']
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$update = MetagroupModel::update(
					$id,
					Input::getPost('name'),
					Input::getPost('tag'),
					Input::getPost('infotype'),
					Input::getPost('relevance')
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('name')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITMETAGROUP, [$id]);
	}
	
	public function metagroupsEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_METAGROUP . ')';
		$this->_context['METAGROUP'] = MetagroupModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['METAGROUP'] === null);
		$this->_context['INFO_TYPES'] = $this->_infoTypes;
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'NAME' => Input::getPostWithDefaultValue('name', $this->_context['METAGROUP']->metagroup_name),
			'TAG' => Input::getPostWithDefaultValue('tag', $this->_context['METAGROUP']->metagroup_tag),
			'RELEVANCE' => Input::getPostWithDefaultValue('relevance', $this->_context['METAGROUP']->metagroup_relevance),
			'INFOTYPE' => Input::getPostWithDefaultValue('infotype', $this->_context['METAGROUP']->metagroup_infotype)
		];
	}
	
	public function metagroupsListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_METAGROUPS . ')';
		$this->_context['METAGROUPS'] = MetagroupModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['METAGROUPS_TOTAL'] = MetagroupModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['METAGROUPS'], $page, $this->_context['METAGROUPS_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTMETAGROUP, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}

	/* PLATFORMS */
	
	public function platformsAddAction() : void {
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-platformsAddView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 50, 'required' => true],
				'tag' => ['tag' => LANG_TAG, 'max' => 3, 'required' => true],
				'release_date' => ['label' => LANG_RELEASE_DATE, 'required' => true, 'pattern' => 'date'],
				'colour' => ['label' => LANG_COLOUR, 'max' => 20]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$insert = PlatformModel::insert(
					Input::getPost('name'),
					Input::getPost('tag'),
					Input::getPost('release_date'),
					Input::getPost('colour'),
					Input::getPost('featured') == 1 ? 1 : 0
				);
				if(!$insert) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_CREATED_SUCCESSFULLY, Input::getPost('name')));
					// Redirección.
					Urls::redirectTo(UrlsEnum::ADMIN_VIEW_LISTPLATFORM, [1]);
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_ADDPLATFORM);
	}
	
	public function platformsAddView() : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_NEW_PLATFORM . ')';
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'NAME' => Input::getPost('name'),
			'TAG' => Input::getPost('tag'),
			'RELEASE_DATE' => Input::getPost('release_date'),
			'COLOUR' => Input::getPost('colour'),
			'FEATURED' => Input::getPost('featured')
		];
	}

	public function platformsEditAction(int $id) : void {
		$platform = PlatformModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $platform === null);
		if(Input::exists('post')) {
			// Se validan los campos del formulario.
			$validation = new Validate($this->_context['CLIENT']['IP'], 'AdminController-platformsEditView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'name' => ['label' => LANG_NAME, 'max' => 50, 'required' => true],
				'tag' => ['tag' => LANG_TAG, 'max' => 3, 'required' => true],
				'release_date' => ['label' => LANG_RELEASE_DATE, 'required' => true, 'pattern' => 'date'],
				'colour' => ['label' => LANG_COLOUR, 'max' => 20]
			], true, false);
			if(!$validation->passed()) {
				Session::put(Constants::SESSION_ERROR_MESSAGES, $validation->errors());
			} else {
				$update = PlatformModel::update(
					$id,
					Input::getPost('name'),
					Input::getPost('tag'),
					Input::getPost('release_date'),
					Input::getPost('colour'),
					Input::getPost('featured') == 1 ? 1 : 0
				);
				if(!$update) {
					Session::addElement(Constants::SESSION_SUCCESS_MESSAGES, sprintf(LANG_UPDATED_SUCCESSFULLY, Input::getPost('name')));
				} else {
					Session::put(Constants::SESSION_ERROR_MESSAGES, [LANG_OPERATION_ERROR]);
				}
			}
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::ADMIN_VIEW_EDITPLATFORM, [$id]);
	}
	
	public function platformsEditView(int $id) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_EDIT_PLATFORM . ')';
		$this->_context['PLATFORM'] = PlatformModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['PLATFORM'] === null);
		// Se almacen los valores de los campos enviados en el formulario para no perderlos en caso de que algo salga mal.
		$this->_context['FORMDATA'] = [
			'NAME' => Input::getPostWithDefaultValue('name', $this->_context['PLATFORM']->platform_name),
			'TAG' => Input::getPostWithDefaultValue('tag', $this->_context['PLATFORM']->platform_tag),
			'RELEASE_DATE' => Input::getPostWithDefaultValue('release_date', $this->_context['PLATFORM']->platform_release_date),
			'COLOUR' => Input::getPostWithDefaultValue('colour', $this->_context['PLATFORM']->platform_colour),
			'FEATURED' => Input::getPostWithDefaultValue('featured', $this->_context['PLATFORM']->platform_featured)
		];
	}
	
	public function platformsListView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_ADMIN_PANEL . ' (' . LANG_PLATFORMS . ')';
		$this->_context['PLATFORMS'] = PlatformModel::getLimit(($page - 1) * Constants::ADMIN_ELEMENTS_PER_PAGE, Constants::ADMIN_ELEMENTS_PER_PAGE);
		$this->_context['PLATFORMS_TOTAL'] = PlatformModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['PLATFORMS'], $page, $this->_context['PLATFORMS_TOTAL'], UrlsEnum::ADMIN_VIEW_LISTPLATFORM, Constants::ADMIN_ELEMENTS_PER_PAGE, []);
	}
}
