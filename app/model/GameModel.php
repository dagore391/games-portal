<?php
namespace app\model;

use app\config\Paths;
use app\data\Database;
use app\data\Dates;
use app\data\EntryCategoriesConstants;
use app\file\SecureImport;
use app\file\UploadFile;
use app\security\Logger;

class GameModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `game`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}
	
	public static function insert(?int $gameGroup, int $platform, string $name, ?string $resume = null, ?int $releaseYear = null, ?int $releaseMonth = null, ?int $releaseDay = null, ?string $releasePrice = null, ?array $pic = [], ?array $metadata = [], ?array $developers = [], ?array $publishers = []) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el juego en la base de datos.
			$db->query(
				'INSERT INTO `game` (`id`, `gamegroup`, `platform`, `name`, `resume`, `release_year`, `release_month`, `release_day`, `release_price`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);',
				[$insertId, $gameGroup, $platform, $name, $resume, $releaseYear, $releaseMonth, $releaseDay, $releasePrice],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
			);
			// Se asocian los metadatas al videojuego.
			foreach($metadata as $value) {
				$db->query(
					'INSERT INTO `game_metadata` (`game`, `metadata`) VALUES (?, ?);',
					[$insertId, $value],
					[\PDO::PARAM_INT, \PDO::PARAM_INT]
				);
			}
			// Se asocian los desarrolladores al videojuego.
			foreach($developers as $value) {
				$db->query(
					'INSERT INTO `game_company` (`game`, `company`, `category`) VALUES (?, ?, \'DEVELOPER\');',
					[$insertId, $value],
					[\PDO::PARAM_INT, \PDO::PARAM_INT]
				);
			}
			// Se asocian los editores al videojuego.
			foreach($publishers as $value) {
				$db->query(
					'INSERT INTO `game_company` (`game`, `company`, `category`) VALUES (?, ?, \'PUBLISHER\');',
					[$insertId, $value],
					[\PDO::PARAM_INT, \PDO::PARAM_INT]
				);
			}
			$platformObject = PlatformModel::getById($platform);
			// Se sube el fichero si se ha seleccionado.
			if(!empty($pic['tmp_name']) && !$db->isError()) {
				$errors = UploadFile::upload($_FILES['cover'], Paths::UGAMECOVER . $platformObject->platform_tag, $insertId, ['image/jpeg'], 262144, true);
			}
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(sizeof($errors) === 0 && !$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_DDBB_INSERT_ERROR);
			}
		} catch(\PDOException $exception) {
			if(sizeof($errors) === 0) {
				$errors = [$exception->getMessage()];
			}
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
		return $errors;
	}
	
	public static function update(int $id, ?int $gameGroup, int $platform, string $name, ?string $resume = null, ?int $releaseYear = null, ?int $releaseMonth = null, ?int $releaseDay = null, ?string $releasePrice = null, ?array $pic = [], ?array $metadata = [], ?array $developers = [], ?array $publishers = []) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del videojuego.
			$db->query(
				'UPDATE `game` SET `gamegroup` = ?, `platform` = ?,`name` = ?, `resume` = ?, `release_year` = ?, `release_month` = ?, `release_day` = ?, `release_price` = ? WHERE `id` = ?;',
				[$gameGroup, $platform, $name, $resume, $releaseYear, $releaseMonth, $releaseDay, $releasePrice, $id],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_INT]
			);
			// Se desvinculan todos los metadatos asociados al videojuego.
			$db->query('DELETE FROM `game_metadata` WHERE `game` = ?;', [$id], [\PDO::PARAM_INT]);
			// Se asocian los metadatas al videojuego.
			foreach($metadata as $value) {
				$db->query(
					'INSERT INTO `game_metadata` (`game`, `metadata`) VALUES (?, ?);',
					[$id, $value],
					[\PDO::PARAM_INT, \PDO::PARAM_INT]
				);
			}
			// Se desvinculan todas las compañías asociadas al videojuego.
			$db->query('DELETE FROM `game_company` WHERE `game` = ?;', [$id], [\PDO::PARAM_INT]);
			// Se asocian los desarrolladores al videojuego.
			foreach($developers as $value) {
				$db->query(
					'INSERT INTO `game_company` (`game`, `company`, `category`) VALUES (?, ?, \'DEVELOPER\');',
					[$id, $value],
					[\PDO::PARAM_INT, \PDO::PARAM_INT]
				);
			}
			// Se asocian los editores al videojuego.
			foreach($publishers as $value) {
				$db->query(
					'INSERT INTO `game_company` (`game`, `company`, `category`) VALUES (?, ?, \'PUBLISHER\');',
					[$id, $value],
					[\PDO::PARAM_INT, \PDO::PARAM_INT]
				);
			}
			$platformObject = PlatformModel::getById($platform);
			// Se sube el fichero si se ha seleccionado.
			if(isset($pic['tmp_name']) && $pic['tmp_name'] != '' && !$db->isError()) {
				$errors = UploadFile::upload($_FILES['cover'], Paths::UGAMECOVER . $platformObject->platform_tag, $id, ['image/jpeg'], 262144, true);
			}
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(sizeof($errors) === 0 && !$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_DDBB_UPDATE_ERROR);
			}
		} catch(\PDOException $exception) {
			if(sizeof($errors) === 0) {
				$errors = [$exception->getMessage()];
			}
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
		return $errors;
	}
	
	public static function getById(int $id) : ?object {
		$gameInfoObj = Database::getInstance()->query(
			"SELECT
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`game`.`gamegroup` AS `game_gamegroup`,
				`game`.`release_day` AS `game_release_day`,
				`game`.`release_month` AS `game_release_month`,
				`game`.`release_year` AS `game_release_year`,
				`game`.`release_price` AS `game_release_price`,
				`game`.`resume` AS `game_resume`,
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				(
				SELECT
					GROUP_CONCAT(CONCAT('<span class=\"border1-gray flex font-10 gradient-gray2 margin1 padding3\">', `company`.`name`, '</span>') ORDER BY `company`.`name` ASC SEPARATOR '')
				FROM
					`company` 
				LEFT JOIN
					`game_company`
				ON
					`game_company`.`company` = `company`.`id`
				WHERE
					`game_company`.`game` = `game_id`
				AND
					`game_company`.`category` = 'DEVELOPER'
				) AS `game_developers`,
				(
				SELECT
					GROUP_CONCAT(CONCAT('<span class=\"border1-gray flex font-10 gradient-gray2 margin1 padding3\">', `company`.`name`, '</span>') ORDER BY `company`.`name` ASC SEPARATOR '')
				FROM
					`company` 
				LEFT JOIN
					`game_company`
				ON
					`game_company`.`company` = `company`.`id`
				WHERE
					`game_company`.`game` = `game_id`
				AND
					`game_company`.`category` = 'PUBLISHER'
				) AS `game_publishers`
			FROM
				`game`
			RIGHT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			WHERE
				`game`.`id` = ?
			LIMIT 1;",
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize(['game_resume', 'game_developers', 'game_publishers']);
		// Si se ha recuperado algún juego, se recupera la información anexa al mismo.
		if($gameInfoObj != null) {
			// Se prepara la fecha del juego.
			$gameInfoObj->game_release_date = Dates::concatYearMonthDay($gameInfoObj->game_release_year, $gameInfoObj->game_release_month, $gameInfoObj->game_release_day);
			// Se recupera la carátula del juego.
			$gameInfoObj->game_cover = SecureImport::fixFileResourcePath(
				Paths::UGAMECOVER . $gameInfoObj->platform_tag . DIRECTORY_SEPARATOR . $gameInfoObj->game_id . '.jpg',
				Paths::SITE_TEMPLATE_IMG . 'no-image.png'
			);
			// Se recuperan los metadatos.
			$gameMetadataTemp = Database::getInstance()->query(
				'SELECT
					`metadata`.`id` AS `metadata_id`,
					`metadata`.`value` AS `metadata_value`,
					`metagroup`.`id` AS `metagroup_id`,
					`metagroup`.`infotype` AS `metagroup_infotype`,
					`metagroup`.`name` AS `metagroup_name`,
					`metagroup`.`relevance` AS `metagroup_relevance`,
					`metagroup`.`tag` AS `metagroup_tag`
				FROM
					`metadata`
				RIGHT JOIN
					`metagroup`
				ON
					`metadata`.`group` = `metagroup`.`id`
				RIGHT JOIN
					`game_metadata`
				ON
					`metadata`.`id` = `game_metadata`.`metadata`
				WHERE
					`game_metadata`.`game` = ?
				ORDER BY
					`metagroup`.`name` ASC, `metadata`.`value` ASC;',
				[$id],
				[\PDO::PARAM_INT]
			)->resultsSanitize();
			$gameMetadataList = [];
			foreach($gameMetadataTemp as $result) {
				if($result->metagroup_infotype === 'PNG') {
					$result->metadata_picture = SecureImport::fixFileResourcePath(
						Paths::UMETADATA . $result->metagroup_tag . DIRECTORY_SEPARATOR . $result->metadata_value . '.png',
						Paths::SITE_TEMPLATE_IMG . 'no-image.png'
					);
				}
				array_push($gameMetadataList, $result);
			}
			$gameInfoObj->game_metadata = $gameMetadataList;
		}
		return $gameInfoObj;
	}
	
	public static function getCompanies(int $id) : array {
		return Database::getInstance()->query(
			'SELECT
				`company`.`id` as `company_id`,
				`company`.`name` as `company_name`,
				`game_company`.`category` as `company_category`
			FROM
				`company` 
			LEFT JOIN
				`game_company`
			ON
				`game_company`.`company` = `company`.`id`
			WHERE
				`game_company`.`game` = ?;',
			[$id],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getMetadata(int $id) : array {
		return Database::getInstance()->query(
			'SELECT
				`metadata`.`id` AS `metadata_id`,
				`metadata`.`value` AS `metadata_value`,
				`metagroup`.`id` AS `metagroup_id`,
				`metagroup`.`infotype` AS `metagroup_infotype`,
				`metagroup`.`name` AS `metagroup_name`,
				`metagroup`.`relevance` AS `metagroup_relevance`,
				`metagroup`.`tag` AS `metagroup_tag`
			FROM
				`metadata`
			RIGHT JOIN
				`metagroup`
			ON
				`metadata`.`group` = `metagroup`.`id`
			RIGHT JOIN
				`game_metadata`
			ON
				`metadata`.`id` = `game_metadata`.`metadata`
			WHERE
				`game_metadata`.`game` = ?
			ORDER BY
				`metagroup`.`name` ASC, `metadata`.`value` ASC;',
			[$id],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function selectCountMemberGameList(int $userId, string $listName) : int {
		return Database::getInstance()->query(
			'SELECT `game` FROM `member_game` WHERE `member` = ? AND `category` = ?;',
			[$userId, $listName],
			[\PDO::PARAM_INT, \PDO::PARAM_STR]
		)->count();
	}
	
	public static function isInMemberGameList(int $gameId, int $userId, string $listName) : ?bool {
		return Database::getInstance()->query(
			'SELECT `game` FROM `member_game` WHERE `game` = ? AND `member` = ? AND `category` = ? LIMIT 1;',
			[$gameId, $userId, $listName],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
		)->first() != null;
	}

	public static function addToMemberGameList(int $gameId, int $userId, string $listName) : void {
		$db = Database::getInstance();
		try {
			// Se efectúa la eliminación previa de los registros de la lista de juegos del usuario.
			if($listName === 'COLLECTION') {
				$db->getInstance()->query(
					'DELETE FROM `member_game` WHERE `member` = ? AND `game` = ? AND `category` = ?;',
					[$userId, $gameId, 'WISHLIST'],
					[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
				);
			} else if($listName === 'WISHLIST') {
				$db->getInstance()->query(
					'DELETE FROM `member_game` WHERE `member` = ? AND `game` = ? AND `category` = ?;',
					[$userId, $gameId, 'COLLECTION'],
					[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
				);
			}
			// Se da de alta el nuevo juego en la lista correspondiente del usuario.
			$db->getInstance()->query(
				'INSERT INTO `member_game` (`member`, `game`, `category`) VALUES (?, ?, ?);',
				[$userId, $gameId, $listName],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_REQUEST_COULD_NOT_BE_PROCESSED);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
	}

	public static function removeFromMemberGameList(int $gameId, int $userId, string $listName) : void {
		$db = Database::getInstance();
		try {
			// Se elimina el registro de la lista de juegos indicada del usuario.
			$db->getInstance()->query(
				"DELETE FROM `member_game` WHERE `member` = ? AND `game` = ? AND `category` = ?;",
				[$userId, $gameId, $listName],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_REQUEST_COULD_NOT_BE_PROCESSED);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
	}

	public static function rate(int $gameId, int $userId, int $gameScore) : void {
		if($gameScore <= 10 && $gameScore >= 1) {
			$db = Database::getInstance();
			try {
				// Se inserta la valoración del usuario para el videojuego indicado.
				$db->getInstance()->query(
					"INSERT INTO `game_score` (`member`, `game`, `score`) VALUES (?, ?, ?);",
					[$userId, $gameId, $gameScore],
					[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
				);
				// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
				if(!$db->isError()) {
					$db->commit();
				} else {
					throw new \PDOException(LANG_REQUEST_COULD_NOT_BE_PROCESSED);
				}
			} catch(\PDOException $exception) {
				$db->rollback();
				Logger::logError($exception->getMessage(), $exception);
			}
		}
	}

	public static function removeRate(int $gameId, int $userId) : void {
		$db = Database::getInstance();
		try {
			// Se elimina la valoración del usuario para el videojuego indicado.
			$db->getInstance()->query(
				"DELETE FROM `game_score` WHERE `member` = ? AND `game` = ?;",
				[$userId, $gameId],
				[\PDO::PARAM_INT, \PDO::PARAM_INT]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_REQUEST_COULD_NOT_BE_PROCESSED);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
	}
	
	public static function getMediaAverageScore(int $id) : ?object {
		$gameScoreObj = Database::getInstance()->query(
			"SELECT ROUND(AVG(`score`), 1) AS `score`, COUNT(`score`) AS `votes` FROM `media_score` WHERE `game` = ? LIMIT 1;",
			[$id],
			[\PDO::PARAM_INT]
		)->first();
		$gameScoreObj->score = !empty($gameScoreObj->score) ? $gameScoreObj->score : 0;
		$gameScoreObj->votes = !empty($gameScoreObj->votes) ? $gameScoreObj->votes : 0;
		return $gameScoreObj;
	}
	
	public static function getUsersAverageScore(int $id) : ?object {
		$gameScoreObj = Database::getInstance()->query(
			"SELECT ROUND(AVG(`score`), 1) AS `score`, COUNT(`score`) AS `votes` FROM `game_score` WHERE `game` = ? LIMIT 1;",
			[$id],
			[\PDO::PARAM_INT]
		)->first();
		$gameScoreObj->score = !empty($gameScoreObj->score) ? $gameScoreObj->score : 0;
		$gameScoreObj->votes = !empty($gameScoreObj->votes) ? $gameScoreObj->votes : 0;
		return $gameScoreObj;
	}
	
	public static function getWebAverageScore(int $id) : ?object {
		$gameScoreObj = Database::getInstance()->query(
			'SELECT ROUND(AVG(`score`), 1) AS `score`, COUNT(`score`) AS `votes` FROM `entry` WHERE `game` = ? AND `category` = ? LIMIT 1;',
			[$id, EntryCategoriesConstants::REVIEW],
			[\PDO::PARAM_INT, \PDO::PARAM_STR]
		)->first();
		$gameScoreObj->score = !empty($gameScoreObj->score) ? $gameScoreObj->score : 0;
		$gameScoreObj->votes = !empty($gameScoreObj->votes) ? $gameScoreObj->votes : 0;
		return $gameScoreObj;
	}
	
	public static function getUserRated(int $gameId, int $userId) : ?object {
		return Database::getInstance()->query(
			"SELECT `game`, `score` FROM `game_score` WHERE `game` = ? AND `member` = ? LIMIT 1;",
			[$gameId, $userId],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->first();
	}
	
	public static function selectGameAchievement(int $gameId) : array {
		return Database::getInstance()->query(
			"SELECT
				`achievement`.`title` as `achievement_title`,
				`achievement`.`description` AS `achievement_description`,
				`achievement`.`value` AS `achievement_value`
			FROM
				`achievement`
			WHERE
				`achievement`.`game` = ?
			ORDER BY
				`achievement`.`title` ASC;",
			[$gameId],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function selectGamesByGroup(int $groupId) : array {
		return Database::getInstance()->query(
			"SELECT
				`game`.`id` as `game_id`,
				`platform`.`name` as `platform_name`,
				`platform`.`tag` as `platform_tag`,
				`platform`.`colour` as `platform_colour`
			FROM
				`game`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			WHERE
				`game`.`gamegroup` = ?
			ORDER BY
				`platform`.`tag` ASC;",
			[$groupId],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}

	public static function selectGameEntries(int $id) : array {
		return Database::getInstance()->query(
			"SELECT
				`entry`.`id` as `entry_id`,
				`entry`.`category` as `entry_category`,
				`entry`.`published` as `entry_published`,
				`entry`.`title` as `entry_title`
			FROM
				`entry`
			WHERE
				`entry`.`game` = ?
			AND
				`entry`.`category` <> ?
			ORDER BY
				`entry`.`category` ASC, `entry`.`published` DESC;",
			[$id, 'FAQ'],
			[\PDO::PARAM_INT, \PDO::PARAM_STR]
		)->resultsSanitize();
	}

	public static function selectGameEntriesByCategory(int $gameId, string $category) : array {
		return Database::getInstance()->query(
			"SELECT
				`entry`.`id` as `entry_id`,
				`entry`.`category` as `entry_category`,
				`entry`.`published` as `entry_published`,
				`entry`.`title` as `entry_title`
			FROM
				`entry`
			WHERE
				`entry`.`game` = ?
			AND
				`entry`.`category` = ?
			ORDER BY
				`entry`.`title` ASC;",
			[$gameId, $category],
			[\PDO::PARAM_INT, \PDO::PARAM_STR]
		)->resultsSanitize();
	}
	
	public static function selectLatestReleases(int $limit) : array {
		$results = Database::getInstance()->query(
			"SELECT
				`game`.`id` as `game_id`,
				`game`.`name` AS `game_name`,
				`game`.`release_day` AS `game_release_day`,
				`game`.`release_month` AS `game_release_month`,
				`game`.`release_year` AS `game_release_year`,
				`platform`.`name` as `platform_name`,
				`platform`.`tag` as `platform_tag`
			FROM
				`game`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			WHERE
				`game`.`release_year` IS NOT NULL
			AND
				`game`.`release_month` IS NOT NULL
			AND
				`game`.`release_day` IS NOT NULL
			ORDER BY
				`game`.`release_year` DESC,
				`game`.`release_month` DESC,
				`game`.`release_day` DESC,
				`game`.`name` ASC
			LIMIT ?;",
			[$limit],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
		foreach($results as $row) {
			$row->game_release_date = Dates::concatYearMonthDay($row->game_release_year, $row->game_release_month, $row->game_release_day);
		}
		return $results;
	}

	public static function getLikeSearch(string $words, int $limit) : array {
		$gamesList = Database::getInstance()->query(
			"SELECT
				ROUND(AVG(`score`), 1) AS `avg`,
				COUNT(`score`) AS `votes`,
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				`platform`.`colour` AS `platform_colour`,
				ROUND(AVG(`score`), 1) AS `game_useravg`,
				(
				SELECT
					ROUND(AVG(`score`), 1)
				FROM
					`entry`
				WHERE
					`game` = `game_id`
				AND
					`category` = ?
				LIMIT 1
				) AS `game_webavg`,
				(
				SELECT
					ROUND(AVG(`score`), 1)
				FROM
					`media_score`
				WHERE
					`game` = `game_id`
				LIMIT 1
				) AS `game_mediaavg`,
				(
				SELECT
					GROUP_CONCAT(CONCAT('<span class=\"border1-blue gradient-blue margin-right3 padding3\">', `metadata`.`value`, '</span>') ORDER BY `metadata`.`value` ASC SEPARATOR '')
				FROM
					`metadata` 
				LEFT JOIN
					`game_metadata`
				ON
					`game_metadata`.`metadata` = `metadata`.`id`
				WHERE
					`metadata`.`group` = 1
				AND
					`game_metadata`.`game` = `game_id`
				) AS `game_genres`
			FROM
				`game`
			LEFT JOIN
				`game_score`
			ON
				`game_score`.`game` = `game`.`id`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			WHERE
				UPPER(`game`.`name`) LIKE CONCAT('%', ?, '%')
			GROUP BY
				`game_id`
			ORDER BY
				`avg` DESC, `votes` DESC, `game`.`name` ASC
			LIMIT ?;",
			[EntryCategoriesConstants::REVIEW, strtolower($words), $limit],
			[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT]
		)->resultsSanitize(['game_genres']);
		$idx = 1;
		foreach($gamesList as $game) {
			$game->game_rank = $idx++;
			// Se recupera la carátula del juego.
			$game->game_cover = SecureImport::fixFileResourcePath(
				Paths::UGAMECOVER . $game->platform_tag . DIRECTORY_SEPARATOR . $game->game_id . '.jpg',
				Paths::SITE_TEMPLATE_IMG . 'no-image.png'
			);
		}
		return $gamesList;
	}
	
	public static function selectSimilarGames(int $gameId, int $platformId, int $limit) : array {
		$gamesList = Database::getInstance()->query(
			"SELECT
				`game`.`id` as `game_id`,
				`game`.`name` as `game_name`,
				`game`.`platform` as `game_platform`,
				`platform`.`tag` as `platform_tag`,
				SUM(`metagroup`.`relevance`) as `metagroup_relevance`
			FROM
				`game`
			INNER JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			INNER JOIN
				`game_metadata`
			ON
				`game`.`id` = `game_metadata`.`game`
			INNER JOIN
				`metadata`
			ON
				`game_metadata`.`metadata` = `metadata`.`id`
			INNER JOIN
				`metagroup`
			ON
				`metadata`.`group` = `metagroup`.`id`
			WHERE
				`platform`.`id` = ?
			AND
				`game`.`id` != ?
			AND
				`game_metadata`.`metadata` IN (
					SELECT
						`game_metadata`.`metadata`
					FROM
						`game_metadata`
					WHERE
						`game_metadata`.`game` = ?
				)
			GROUP BY
				`game_id`
			ORDER BY
				`metagroup_relevance` DESC
			LIMIT ?;",
			[$platformId, $gameId, $gameId, $limit],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
		foreach($gamesList as $game) {
			$game->game_cover = SecureImport::fixFileResourcePath(
				Paths::UGAMECOVER . $game->platform_tag . DIRECTORY_SEPARATOR . $game->game_id . '.jpg',
				Paths::SITE_TEMPLATE_IMG . 'no-image.png'
			);
		}
		return $gamesList;
	}

	public static function getTopGames(int $limit, string $top = 'users') : array {
		$avgSQL = '(SELECT ROUND(AVG(`score`), 1) FROM `game_score` WHERE `game` = `game_id`) AS `game_score`,';
		if($top === 'medias') {
			$avgSQL = '(SELECT ROUND(AVG(`score`), 1) FROM `media_score` WHERE `game` = `game_id`) AS `game_score`,';
		} elseif($top === 'web') {
			$avgSQL = '(SELECT ROUND(AVG(`score`), 1) FROM `entry` WHERE `game` = `game_id` AND `category` = \'REVIEW\' LIMIT 1) AS `game_score`,';
		}
		$gamesList = Database::getInstance()->query(
			"SELECT
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				`platform`.`colour` AS `platform_colour`,
				{$avgSQL}
				`platform`.`colour` AS `platform_colour`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				(
				SELECT
					GROUP_CONCAT(CONCAT('<span class=\"border1-blue gradient-blue margin-right3 padding3\">', `metadata`.`value`, '</span>') ORDER BY `metadata`.`value` ASC SEPARATOR '')
				FROM
					`metadata` 
				LEFT JOIN
					`game_metadata`
				ON
					`game_metadata`.`metadata` = `metadata`.`id`
				WHERE
					`metadata`.`group` = 1
				AND
					`game_metadata`.`game` = `game_id`
				) AS `game_genres`
			FROM
				`game`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			GROUP BY
				`game_id`
			HAVING
				`game_score` > 0
			ORDER BY
				`game_score` DESC, `game`.`name` ASC
			LIMIT ?;",
			[$limit],
			[\PDO::PARAM_INT]
		)->resultsSanitize(['game_genres']);
		$idx = 1;
		foreach($gamesList as $game) {
			$game->game_rank = $idx++;
			// Se recupera la carátula del juego.
			$game->game_cover = SecureImport::fixFileResourcePath(
				Paths::UGAMECOVER . $game->platform_tag . DIRECTORY_SEPARATOR . $game->game_id . '.jpg',
				Paths::SITE_TEMPLATE_IMG . 'no-image.png'
			);
		}
		return $gamesList;
	}
	
	public static function getGameRatingPosition(int $id) : int {
		$gamesScoresList = Database::getInstance()->query(
			"SELECT ROUND(AVG(`score`), 1) AS `avg`, COUNT(`score`) AS `votes`, `game` FROM `game_score` GROUP BY `game` ORDER BY `avg` DESC, `votes` DESC;",
			[],
			[]
		)->results();
		$idx = 1;
		foreach($gamesScoresList as $row) {
			if($row->game == $id) {
				return $idx;
			}
			$idx++;
		}
		return -1;
	}
	
	public static function getFilteredLimit(string $platformTag, string $genre, int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			"SELECT
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				ROUND(AVG(`score`), 1) AS `game_useravg`,
				(
				SELECT
					ROUND(AVG(`score`), 1)
				FROM
					`media_score`
				WHERE
					`game` = `game_id`
				LIMIT 1
				) AS `game_mediaavg`,
				(
				SELECT
					ROUND(AVG(`score`), 1)
				FROM
					`entry`
				WHERE
					`game` = `game_id`
				AND
					`category` = ?
				LIMIT 1
				) AS `game_webavg`,
				`platform`.`colour` AS `platform_colour`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				(
				SELECT
					GROUP_CONCAT(CONCAT('<span class=\"border1-blue gradient-blue margin-right3 padding3\">', `metadata`.`value`, '</span>') ORDER BY `metadata`.`value` ASC SEPARATOR '')
				FROM
					`metadata` 
				LEFT JOIN
					`game_metadata`
				ON
					`game_metadata`.`metadata` = `metadata`.`id`
				WHERE
					`metadata`.`group` = 1
				AND
					`game_metadata`.`game` = `game_id`
				) AS `game_genres`
			FROM
				`game`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			LEFT JOIN
				`game_score`
			ON
				`game`.`id` =`game_score`.`game`
			WHERE
				`platform`.`tag` " . ($platformTag === 'all' ? '<>' : '=') . " ?
			AND
				(
				SELECT
					COUNT(`metadata`.`value`)
				FROM
					`metadata` 
				LEFT JOIN
					`game_metadata`
				ON
					`game_metadata`.`metadata` = `metadata`.`id`
				WHERE
					`metadata`.`group` = 1
				AND
					`game_metadata`.`game` = `game`.`id`
				AND
					`metadata`.`id` " . ($genre === 'all' ? '<>' : '=') . " ?
				) <> " . ($genre === 'all' ? '-1' : '0') . "
			GROUP BY
				`game_id`
			ORDER BY
				`game`.`name` ASC, `platform`.`name` ASC
			LIMIT ?, ?;",
			[EntryCategoriesConstants::REVIEW, $platformTag, $genre, $startElement, $numberOfElements],
			[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize(['game_genres']);
	}

	public static function getTotalFilteredGames(string $platformTag = 'all', string $genre = 'all') : int {
		return Database::getInstance()->query(
			"SELECT
				`game`.`id`
			FROM
				`game`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			WHERE
				`platform`.`tag` " . ($platformTag === 'all' ? '<>' : '=') . " ?
			AND
				(
				SELECT
					COUNT(`metadata`.`value`)
				FROM
					`metadata` 
				LEFT JOIN
					`game_metadata`
				ON
					`game_metadata`.`metadata` = `metadata`.`id`
				WHERE
					`metadata`.`group` = 1
				AND
					`game_metadata`.`game` = `game`.`id`
				AND
					`metadata`.`id` " . ($genre === 'all' ? '<>' : '=') . " ?
				) <> " . ($genre === 'all' ? '-1' : '0') . ";",
			[$platformTag, $genre],
			[\PDO::PARAM_STR, \PDO::PARAM_STR]
		)->count();
	}
	
	public static function selectAll() : array {
		return Database::getInstance()->query(
			"SELECT
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`platform`.`colour` AS `platform_colour`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`
			FROM
				`game`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			LEFT JOIN
				`game_score`
			ON
				`game`.`id` =`game_score`.`game`
			GROUP BY
				`game_id`
			ORDER BY
				`game`.`name` ASC,
				`platform`.`name` ASC;",
			[],
			[]
		)->resultsSanitize();
	}
}
