<?php
namespace app\controller;

use app\data\Input;
use app\data\EntryCategoriesConstants;
use app\data\UserLevelsConstants;
use app\data\Validate;
use app\model\GameModel;

class InitController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'indexView' => [UserLevelsConstants::ALL],
		'cpageView' => [UserLevelsConstants::ALL],
		'searchView' => [UserLevelsConstants::ALL]
	];
	
	public function indexView() : void {
		$this->_context['GENRES'] = \app\model\MetadataModel::getByGroup(1);
		$this->_context['PLATFORMS'] = \app\model\PlatformModel::selectAll();
		$this->_context['TOPGAMES_LIST'] = GameModel::getTopGames(5);
		$this->_context['LATEST_ARTICLES'] = \app\model\EntryModel::getLimitByCategory(EntryCategoriesConstants::ARTICLE, 0, 5);
		$this->_context['LATEST_NEWS'] = \app\model\EntryModel::getLimitByCategory(EntryCategoriesConstants::NEWS, 0, 7);
		$this->_context['LATEST_POSTS'] = \app\model\ForumModel::getTheLatestPostForEachTopics(5);
		$this->_context['LATEST_PREVIEWS'] = \app\model\EntryModel::getLimitByCategory(EntryCategoriesConstants::PREVIEW, 0, 5);
		$this->_context['LATEST_REVIEWS'] = \app\model\EntryModel::getLimitByCategory(EntryCategoriesConstants::REVIEW, 0, 5);
		$this->_context['LATEST_RUMOURS'] = \app\model\EntryModel::getLimitByCategory(EntryCategoriesConstants::RUMOUR, 0, 5);
	}
	
	public function cpageView(string $path) : void {
		$this->_context['CPAGE'] = \app\model\PageModel::getByPath($path);
	}
	
	public function searchView() : void {
		$this->_context['SEARCH_VALUE'] = '';
		$this->_context['RESULTS'] = [];
		$this->_context['RESULTS']['TOTAL'] = 0;
		if(Input::exists('post')) {
			$validation = new Validate($this->_context['CLIENT']['IP'], 'InitController-searchView', $this->_context['CLIENT']['BROWSER']);
			$validation->check($_POST, [
				'hsearch-text' => ['required' => true, 'max' => 50, 'min' => 1]
			], false);
			if($validation->passed()) {
				$this->_context['SEARCH_VALUE'] = Input::getSanitizePost('hsearch-text');
				$this->_context['RESULTS']['GAMES'] = GameModel::getLikeSearch(Input::getSanitizePost('hsearch-text'), 10);
				$this->_context['RESULTS']['TOTAL'] += sizeof($this->_context['RESULTS']['GAMES']);
			}
		}
	}
}
