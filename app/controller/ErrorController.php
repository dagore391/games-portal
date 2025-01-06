<?php
namespace app\controller;

use app\data\UserLevelsConstants;

class ErrorController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'eView' => [UserLevelsConstants::ALL]
	];
	
	public function eView(int $code) : void {
		switch($code) {
			case 403:
				$this->_context['META']['TITLE'] .= ' - Error 403';
				$this->_context['TITLE'] = 'Error 403';
				$this->_context['DESCRIPTION'] = LANG_FORBIDDEN;
			break;
			case 500:
				$this->_context['META']['TITLE'] .= ' - Error 500';
				$this->_context['TITLE'] = 'Error 500';
				$this->_context['DESCRIPTION'] = LANG_INTERNAL_ERROR;
			break;
			default:
				$this->_context['META']['TITLE'] .= ' - Error 404';
				$this->_context['TITLE'] = 'Error 404';
				$this->_context['DESCRIPTION'] = LANG_PAGE_NOT_FOUND;
			break;
		}
	}
}
