<?php
namespace app\controller;

use app\config\Constants;
use app\config\Paths;
use app\config\Urls;
use app\data\Input;
use app\data\Pagination;
use app\data\UrlsEnum;
use app\data\UserLevelsConstants;
use app\file\SecureImport;
use app\model\CheatModel;
use app\model\GameModel;
use app\model\MetadataModel;
use app\model\PlatformModel;
use app\security\Auth;

class GameController extends \app\Controller {
	protected $_methodAccessPermissions = [
		'listView' => [UserLevelsConstants::ALL],
		'detailsView' => [UserLevelsConstants::ALL],
		'topView' => [UserLevelsConstants::ALL],
		'addToListAction' => [UserLevelsConstants::REGISTER],
		'removeRateAction' => [UserLevelsConstants::REGISTER],
		'rateAction' => [UserLevelsConstants::REGISTER],
		'removeFromListAction' => [UserLevelsConstants::REGISTER]
	];
	
	public function detailsView(string $platform, int $id, string $section) : void {
		$this->_context['GAME'] = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $this->_context['GAME'] === null || $this->_context['GAME']->platform_tag != $platform);
		$this->_context['GAME_ACHIEVEMENTS'] = GameModel::selectGameAchievement($id);
		$this->_context['META']['TITLE'] .= ' - ' . $this->_context['GAME']->game_name . ' (' . $this->_context['GAME']->platform_name . ')';
		$this->_context['USERS_SCORE'] = GameModel::getUsersAverageScore($id);
		$this->_context['WEB_SCORE'] = GameModel::getWebAverageScore($id);
		$this->_context['RATING_POSITION'] = GameModel::getGameRatingPosition($id);
		$userId = $this->_context['CLIENT']['LOGIN_USER'] !== null ? $this->_context['CLIENT']['LOGIN_USER']->id : -1;
		$this->_context['USER_RATED'] = Auth::isAuthenticated() ? GameModel::getUserRated($id, $userId) : null;
		$this->_context['USER_ISINCOLLECTION'] = Auth::isAuthenticated() ? GameModel::isInMemberGameList($id, $userId, 'COLLECTION') : false;
		$this->_context['USER_ISINFAVORITES'] = Auth::isAuthenticated() ? GameModel::isInMemberGameList($id, $userId, 'FAVORITE') : false;
		$this->_context['USER_ISINWISHLIST'] = Auth::isAuthenticated() ? GameModel::isInMemberGameList($id, $userId, 'WISHLIST') : false;
		$this->_context['GAME_PLATFORMS'] = GameModel::selectGamesByGroup($this->_context['GAME']->game_gamegroup != null ? $this->_context['GAME']->game_gamegroup : -1);
		$this->_context['SIMILAR_GAMES'] = GameModel::selectSimilarGames($this->_context['GAME']->game_id, $this->_context['GAME']->platform_id, 5);
		if($section === 'info') {
			$this->_context['GAME_SCREENSHOTS'] = [];
			$directory = Paths::USCREENSHOT . strtolower($this->_context['GAME']->platform_tag) . DIRECTORY_SEPARATOR . $this->_context['GAME']->game_id . DIRECTORY_SEPARATOR;
			$screenCount = 1;
			if(file_exists($directory) && is_dir($directory)) {
				foreach(scandir($directory) as $screenshot) {
					if(!is_dir($screenshot) && (exif_imagetype($directory . $screenshot) == IMAGETYPE_JPEG || exif_imagetype($directory . $screenshot) == IMAGETYPE_PNG || exif_imagetype($directory . $screenshot) == IMAGETYPE_GIF)){
						array_push($this->_context['GAME_SCREENSHOTS'], SecureImport::getImageBase64($directory. $screenshot));
						$screenCount++;
					}
					if($screenCount > 3) { break; }
				}
			}
		} else if($section === 'entries') {
			$this->_context['GAME_ENTRIES'] = GameModel::selectGameEntries($id);
		} else if($section === 'faqs') {
			$this->_context['GAME_FAQS'] = GameModel::selectGameEntriesByCategory($id, 'FAQ');
		} else if($section === 'cheats') {
			$this->_context['GAME_CHEATS'] = CheatModel::getByGame($id);
		} else if($section === 'achievements') {
		} else if($section === 'images') {
			$this->_context['GAME_SCREENSHOTS'] = [];
			$directory = Paths::USCREENSHOT . strtolower($this->_context['GAME']->platform_tag) . DIRECTORY_SEPARATOR . $this->_context['GAME']->game_id . DIRECTORY_SEPARATOR;
			if(file_exists($directory) && is_dir($directory)) {
				foreach(scandir($directory) as $screenshot) {
					if(!is_dir($screenshot) && (exif_imagetype($directory . $screenshot) == IMAGETYPE_JPEG || exif_imagetype($directory . $screenshot) == IMAGETYPE_PNG || exif_imagetype($directory . $screenshot) == IMAGETYPE_GIF)){
						array_push($this->_context['GAME_SCREENSHOTS'], SecureImport::getImageBase64($directory. $screenshot));
					}
				}
			}
		} else {
			Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
		}
		// Menú.
		$this->_context['GAMEMENU'] = [];
		array_push($this->_context['GAMEMENU'], ['name' => LANG_INFORMATION, 'tag' => 'info']);
		if(isset($this->_context['GAME_ENTRIES']) && sizeof($this->_context['GAME_ENTRIES']) > 0) {
			array_push($this->_context['GAMEMENU'], ['name' => LANG_ENTRIES, 'tag' => 'entries']);
		}
		if(isset($this->_context['GAME_FAQS']) && sizeof($this->_context['GAME_FAQS']) > 0) {
			array_push($this->_context['GAMEMENU'], ['name' => LANG_FAQS, 'tag' => 'faqs']);
		}
		if(isset($this->_context['GAME_CHEATS']) && sizeof($this->_context['GAME_CHEATS']) > 0) {
			array_push($this->_context['GAMEMENU'], ['name' => LANG_CHEATS, 'tag' => 'cheats']);
		}
		if(isset($this->_context['GAME_ACHIEVEMENTS']) && sizeof($this->_context['GAME_ACHIEVEMENTS']) > 0) {
			array_push($this->_context['GAMEMENU'], ['name' => LANG_ACHIEVEMENTS, 'tag' => 'achievements']);
		}
		if(isset($this->_context['GAME_SCREENSHOTS']) && sizeof($this->_context['GAME_SCREENSHOTS']) > 0) {
			array_push($this->_context['GAMEMENU'], ['name' => LANG_IMAGES, 'tag' => 'images']);
		}
	}
	
	public function listView(string $platform, string $genre, int $page) : void {
		Urls::conditionalRedirectionTo(UrlsEnum::GAME_VIEW_LIST, [Input::getPost('platform'), Input::getPost('genre'), 1], Input::getPost('genre') != '' && Input::getPost('platform') != '' && (Input::getPost('genre') != $genre || Input::getPost('platform') != $platform));
		$this->_context['META']['TITLE'] .= ' - ' . LANG_GAMES;
		$this->_context['PLATFORM'] = PlatformModel::getPlatformByTag($platform);
		$firstGame = ($page - 1) * Constants::ELEMENTS_PER_PAGE;
		$this->_context['GAMES'] = GameModel::getFilteredLimit($platform, $genre, $firstGame, Constants::ELEMENTS_PER_PAGE);
		$this->_context['GAMES_TOTAL'] = GameModel::getTotalFilteredGames($platform, $genre);
		$this->_context['PAGINATION'] = Pagination::htmlPagination($this->_context['GAMES'], $page, (int) $this->_context['GAMES_TOTAL'], UrlsEnum::GAME_VIEW_LIST, Constants::ELEMENTS_PER_PAGE, [$platform, $genre]);
		$this->_context['GENRES'] = MetadataModel::getByGroup(1);
		$this->_context['PLATFORMS'] = PlatformModel::selectAll();
	}
	
	public function topView(string $top) : void {
		switch($top) {
			case 'medias':
				$this->_context['META']['TITLE'] .= ' - ' . LANG_TOP_GAMES_MEDIAS;
				$this->_context['TITLE'] = LANG_TOP_GAMES_MEDIAS;
			break;
			case 'users':
				$this->_context['META']['TITLE'] .= ' - ' . LANG_TOP_GAMES_USERS;
				$this->_context['TITLE'] = LANG_TOP_GAMES_USERS;
			break;
			case 'web':
				$this->_context['META']['TITLE'] .= ' - ' . LANG_TOP_GAMES_WEB;
				$this->_context['TITLE'] = LANG_TOP_GAMES_WEB;
			break;
			default:
				Urls::redirectTo(UrlsEnum::ERROR_VIEW_404, []);
			break;
		}
		$this->_context['GAMES'] = GameModel::getTopGames(50, $top);
	}
	
	public function addToListAction(string $platform, int $id, string $list) : void {
		$game = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $game === null || $game->platform_tag != $platform);
		if(Auth::isAuthenticated()) {
			GameModel::addToMemberGameList($id, $this->_context['CLIENT']['LOGIN_USER']->id, strtoupper($list));
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::GAME_VIEW_SHOW, [$platform, $id, 'info']);
	}
	
	public function removeFromListAction(string $platform, int $id, string $list) : void {
		$game = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $game === null || $game->platform_tag != $platform);
		if(Auth::isAuthenticated()) {
			GameModel::removeFromMemberGameList($id, $this->_context['CLIENT']['LOGIN_USER']->id, strtoupper($list));
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::GAME_VIEW_SHOW, [$platform, $id, 'info']);
	}
	
	public function rateAction(string $platform, int $id) : void {
		$game = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $game === null || $game->platform_tag != $platform);
		$validatedInputValue = filter_input(INPUT_POST, 'game-rate', FILTER_VALIDATE_INT);
		if(Auth::isAuthenticated() && is_int($validatedInputValue)) {
			GameModel::rate($id, $this->_context['CLIENT']['LOGIN_USER']->id, $validatedInputValue);
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::GAME_VIEW_SHOW, [$platform, $id, 'info']);
	}
	
	public function removeRateAction(string $platform, int $id) : void {
		$game = GameModel::getById($id);
		Urls::conditionalRedirectionTo(UrlsEnum::ERROR_VIEW_404, [], $game === null || $game->platform_tag != $platform);
		if(Auth::isAuthenticated()) {
			GameModel::removeRate($id, $this->_context['CLIENT']['LOGIN_USER']->id);
		}
		// Redirección.
		Urls::redirectTo(UrlsEnum::GAME_VIEW_SHOW, [$platform, $id, 'info']);
	}
}
