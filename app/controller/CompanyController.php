<?php
namespace app\controller;

use app\config\Constants;
use app\config\Urls;
use app\data\Pagination;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\model\CompanyModel;
use app\model\GameModel;

class CompanyController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'detailsView' => [UserLevelsConstants::ALL],
		'listView' => [UserLevelsConstants::ALL]
	];
	
	public function detailsView(int $id) : void {
		$this->_context['COMPANY'] = CompanyModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['COMPANY'] === null);
		$this->_context['GAMES'] = CompanyModel::getGames($id);
		$this->_context['GAME_RELEASES'] = GameModel::selectLatestReleases(7);
	}
	
	public function listView(int $page) : void {
		$this->_context['META']['TITLE'] .= ' - ' . LANG_COMPANIES;
		$this->_context['COMPANIES'] = CompanyModel::getLimit(($page - 1) * Constants::ELEMENTS_PER_PAGE, Constants::ELEMENTS_PER_PAGE);
		$this->_context['COMPANIES_TOTAL'] = CompanyModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['COMPANIES'], $page, $this->_context['COMPANIES_TOTAL'], UrlsEnum::COMPANY_VIEW_LIST, Constants::ELEMENTS_PER_PAGE, []);
	}
}
