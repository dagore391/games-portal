<?php
namespace app\model;

use app\config\Constants;
use app\config\Paths;
use app\data\Database;
use app\file\SecureImport;
use app\security\Logger;

class EntryModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `entry`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}

	public static function insert(string $title, string $category, string $resume, string $content, int $author, int $game = null, int $platform = null, int $score = null) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `entry` (`id`, `title`, `category`, `resume`, `content`, `author`, `game`, `platform`, `score`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);',
				[$insertId, $title, $category, $resume, $content, $author, $game, $platform, $score],
				[\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
			);
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
	
	public static function update(int $id, string $title, string $category, string $resume, string $content, int $author, int $game = null, int $platform = null, int $score = null) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `entry` SET `title` = ?, `category` = ?, `resume` = ?, `content` = ?, `author` = ?, `game` = ?, `platform` = ?, `score` = ? WHERE `id` = ?;',
				[$title, $category, $resume, $content, $author, $game, $platform, $score, $id],
				[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
			);
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
		$entryObj = Database::getInstance()->query(
			'SELECT
				`entry`.`id` AS `entry_id`,
				`entry`.`category` AS `entry_category`,
				`entry`.`title` AS `entry_title`,
				`entry`.`resume` AS `entry_resume`,
				`entry`.`content` AS `entry_content`,
				`entry`.`published` AS `entry_published`,
				`entry`.`score` AS `entry_score`,
				YEAR(`entry`.`published`) AS `entry_published_year`,
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`
			FROM
				`entry`
			LEFT JOIN
				`member`
			ON
				`entry`.`author` = `member`.`id`
			LEFT JOIN
				`game`
			ON
				`entry`.`game` = `game`.`id`
			LEFT JOIN
				`platform`
			ON
				`entry`.`platform` = `platform`.`id`
			WHERE
				`entry`.`id` = ?
			LIMIT 1;',
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize(['entry_content']);
		if($entryObj != null) {
			// Se recupera la miniatura de la entrada.
			$entryObj->entry_thumbnail = SecureImport::getImageBase64(SecureImport::fixFileResourcePath(
				Paths::UENTRYTHUMBNAIL . $entryObj->entry_published_year . DIRECTORY_SEPARATOR . $entryObj->entry_id . '.' . Constants::ENTRY_THUMBNAIL_TYPE,
				Paths::UENTRYTHUMBNAIL . 'no-thumbnail.png'
			));
		}
		return $entryObj;
	}
	
	public static function getLimit(int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			'SELECT
				`entry`.`id` AS `entry_id`,
				`entry`.`category` AS `entry_category`,
				`entry`.`title` AS `entry_title`,
				`entry`.`published` AS `entry_published`
			FROM
				`entry`
			ORDER BY
				`entry`.`published` DESC,
				`entry`.`title` ASC
			LIMIT ?, ?;',
			[$startElement, $numberOfElements],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotal() : int {
		return Database::getInstance()->query('SELECT `entry`.`id` FROM `entry`;', [], [])->count();
	}
	
	/*
		* TODO: REVISAR Todas las funciones (no solo las de esta clase, también en otros modelos) cuando se puedan dar de alta y modificar entradas!!!
		* Unir `entry`.`platform` con `platform`.`id` en lugar de con `game`.`platform`. 
		* Si se asocia un juego, el campo platform se sustituirá por la plataforma a la que pertenece el juego.
		*/
	public static function getLimitByCategory(string $category, int $startElement, int $numberOfElements) : array {
		$results = Database::getInstance()->query(
			'SELECT
				`entry`.`id` AS `entry_id`,
				`entry`.`category` AS `entry_category`,
				`entry`.`title` AS `entry_title`,
				`entry`.`resume` AS `entry_resume`,
				`entry`.`content` AS `entry_content`,
				`entry`.`published` AS `entry_published`,
				`entry`.`score` AS `entry_score`,
				YEAR(`entry`.`published`) AS `entry_published_year`,
				COUNT(`entry_comment`.`id`) AS `entry_comments`,
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`
			FROM
				`entry`
			LEFT JOIN
				`entry_comment`
			ON
				`entry`.`id` = `entry_comment`.`entry`
			LEFT JOIN
				`member`
			ON
				`entry`.`author` = `member`.`id`
			LEFT JOIN
				`game`
			ON
				`entry`.`game` = `game`.`id`
			LEFT JOIN
				`platform`
			ON
				`entry`.`platform` = `platform`.`id`
			WHERE
				`entry`.`category` = ?
			GROUP BY
				`entry`.`id`
			ORDER BY
				`entry`.`published` DESC
			LIMIT ?, ?;',
			[$category, $startElement, $numberOfElements],
			[\PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize(['entry_content']);
		foreach($results as $row) {
			$row->entry_featured = SecureImport::getImageBase64(SecureImport::fixFileResourcePath(
				Paths::UENTRYFEATURED . $row->entry_published_year . DIRECTORY_SEPARATOR . $row->entry_id . '.' . Constants::ENTRY_FEATURED_TYPE,
				Paths::UENTRYTHUMBNAIL . 'no-thumbnail.png'
			));
			$row->entry_thumbnail = SecureImport::getImageBase64(SecureImport::fixFileResourcePath(
				Paths::UENTRYTHUMBNAIL . $row->entry_published_year . DIRECTORY_SEPARATOR . $row->entry_id . '.' . Constants::ENTRY_THUMBNAIL_TYPE,
				Paths::UENTRYTHUMBNAIL . 'no-thumbnail.png'
			));
		}
		return $results;
	}
	
	public static function getTotalByCategory(string $category) : int {
		return Database::getInstance()->query('SELECT `entry`.`id` FROM `entry` WHERE `entry`.`category` = ?;', [$category], [\PDO::PARAM_STR])->count();
	}
}
