<?php
namespace app\model;

use app\data\Database;
use app\security\Logger;

class CheatModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `cheat`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}
	
	public static function insert(string $title = null, string $description, int $author, int $game) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `cheat` (`id`, `title`, `description`, `author`, `game`) VALUES (?, ?, ?, ?, ?);',
				[$insertId, $title, $description, $author, $game],
				[\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT]
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
	
	public static function update(int $id, string $title = null, string $description, int $author, int $game) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `cheat` SET `title` = ?, `description` = ?,`author` = ?, `game` = ? WHERE `id` = ?;',
				[$title, $description, $author, $game, $id],
				[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
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
			echo $exception->getMessage();
		}
		return $errors;
	}
	
	public static function getByGame(int $gameId) : array {
		return Database::getInstance()->query(
			'SELECT
				`cheat`.`id` AS `cheat_id`,
				`cheat`.`title` AS `cheat_title`,
				`cheat`.`description` AS `cheat_description`,
				`cheat`.`published` AS `cheat_published`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`cheat`
			LEFT JOIN
				`member`
			ON
				`cheat`.`author` = `member`.`id`
			WHERE
				`cheat`.`game` = ?
			ORDER BY
				`cheat`.`title` ASC;',
			[$gameId],
			[\PDO::PARAM_INT]
		)->resultsSanitize(['cheat_description']);
	}
	
	public static function getById(int $id) : object {
		return Database::getInstance()->query(
			'SELECT
				`cheat`.`id` AS `cheat_id`,
				`cheat`.`title` AS `cheat_title`,
				`cheat`.`description` AS `cheat_description`,
				`cheat`.`published` AS `cheat_published`,
				`cheat`.`game` AS `cheat_game`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`cheat`
			LEFT JOIN
				`member`
			ON
				`cheat`.`author` = `member`.`id`
			WHERE
				`cheat`.`id` = ?
			LIMIT 1;',
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize(['cheat_description']);
	}
	
	public static function getLimit(int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			'SELECT
				`cheat`.`id` AS `cheat_id`,
				`cheat`.`title` AS `cheat_title`,
				`cheat`.`published` AS `cheat_published`,
				`game`.`name` AS `game_name`,
				`platform`.`name` AS `platform_name`
			FROM
				`cheat`
			LEFT JOIN
				`game`
			ON
				`cheat`.`game` = `game`.`id`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			ORDER BY
				`game`.`name` ASC,
				`platform`.`name` ASC,
				`cheat`.`title` ASC
			LIMIT ?, ?;',
			[$startElement, $numberOfElements],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotal() : int {
		return Database::getInstance()->query('SELECT `cheat`.`id` FROM `cheat`;', [], [])->count();
	}
}
