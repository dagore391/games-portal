<?php
namespace app\file;

use app\config\Constants;
use app\config\Urls;
use app\data\ReportTypesConstants;
use app\data\UrlsEnum;
use app\model\MemberModel;
use app\security\Auth;
use app\security\Cookie;

class TemplateHtml {
	public static function getUserLockOrUnlockButton(int $memberId) : void {
		$authUser = Auth::getAuthenticatedUser();
		if($authUser !== null && $authUser->id != $memberId) {
			if(!MemberModel::isLocked($authUser->id, $memberId)) {
				echo '<a class="button button-orange" href="' . Urls::getUrl(UrlsEnum::MEMBER_ACTION_LOCK, array($memberId)) . '" onclick="return confirm(\'' . LANG_CONFIRM_ACTION_MESSAGE . '\');">' . LANG_LOCK .'</a>';
			} else {
				echo '<a class="button button-orange" href="' . Urls::getUrl(UrlsEnum::MEMBER_ACTION_UNLOCK, array($memberId)) . '" onclick="return confirm(\'' . LANG_CONFIRM_ACTION_MESSAGE . '\');">' . LANG_UNLOCK . '</a>';
			}
		}
	}
	
	public static function getUserSendRequestButton(int $memberId) : void {
		$authUser = Auth::getAuthenticatedUser();
		if($authUser !== null && $authUser->id != $memberId) {
			if(!MemberModel::areRequestsBetweenMembers($authUser->id, $memberId)) {
				echo '<a class="button button-orange" href="' . Urls::getUrl(UrlsEnum::MEMBER_ACTION_SENDREQUEST, array($memberId)) . '" onclick="return confirm(\'' . LANG_REQUEST_CONFIRMATION_MESSAGE . '\');">' . LANG_REQUEST .'</a>';
			}
		}
	}
	
	public static function getUserReportPostButton(int $reportedMemberId, string $reportType, int $messageId) : void {
		$authUser = Auth::getAuthenticatedUser();
		if($authUser !== null && $authUser->id != $reportedMemberId && (strtoupper($reportType) == ReportTypesConstants::TYPE_COMMENT || strtoupper($reportType) == ReportTypesConstants::TYPE_POST)) {
			if(!MemberModel::checkReportedMessage($authUser->id, $reportedMemberId, $messageId, $reportType)) {
				echo '<a class="button button-red" href="' . Urls::getUrl(UrlsEnum::MEMBER_ACTION_REPORTMESSAGE, array($reportType, $messageId)) . '" onclick="return confirm(\'' . LANG_REPORT_CONFIRMATION_MESSAGE . '\');">' . LANG_REPORT .'</a>';
			} else {
				echo '<span class="button button-disabled">' . LANG_REPORTED .'</span>';
			}
		}
	}
	
	public static function showCookiesAlert() : void {
		if(!Cookie::exists(Constants::COOKIE_ACCEPT)) {
			echo '<div class="cookies-alert-box">
				<p>' . LANG_COOKIES_INFO_MESSAGE . ' <a class="cookies-alert-link" href="' . Urls::getUrl(UrlsEnum::INIT_VIEW_CUSTOM_PAGE, ['cookies']) . '" target="_blank">' . LANG_COOKIES . '</a>.</p>
				<a href="' . Urls::getUrl(UrlsEnum::MEMBER_ACTION_ACCEPT_COOKIES, []) . '" class="cookies-alert-agree">' . LANG_ACCEPT . '</a>
			</div>
			<script charset="utf-8" src="' . SecureImport::getFileBase64(CONTEXT['TEMPLATE_DIR']['ROOT'] . 'js/jquery.min.js') . '" type="application/javascript"></script>
			<script charset="utf-8" src="' . SecureImport::getFileBase64(CONTEXT['TEMPLATE_DIR']['ROOT'] . 'js/cookies-alert.js') . '" type="application/javascript"></script>';
		}
	}
}
