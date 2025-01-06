<?php
namespace app\controller;

use app\config\Constants;
use app\data\Pagination;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\model\PlatformModel;

class PlatformController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'listView' => [UserLevelsConstants::ALL]
	];
	
	public function listView(int $page) : void {
		$firstPlatform = ($page - 1) * Constants::ELEMENTS_PER_PAGE;
		$this->_context['PLATFORMS'] = PlatformModel::getLimit($firstPlatform, Constants::ELEMENTS_PER_PAGE);
		$this->_context['PLATFORMS_TOTAL'] = PlatformModel::getTotal();
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['PLATFORMS'], $page, $this->_context['PLATFORMS_TOTAL'], UrlsEnum::PLATFORM_VIEW_LIST, Constants::ELEMENTS_PER_PAGE, []);
	}
}
