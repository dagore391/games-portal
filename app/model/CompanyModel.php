<?php
namespace app\model;

use app\data\Database;
use app\security\Logger;

class CompanyModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `company`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}
	
	public static function insert(string $name) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `company` (`id`, `name`) VALUES (?, ?);',
				[$insertId, $name],
				[\PDO::PARAM_INT, \PDO::PARAM_STR]
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
	
	public static function update(int $id, string $name) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `company` SET `name` = ? WHERE `id` = ?;',
				[$name, $id],
				[\PDO::PARAM_STR, \PDO::PARAM_INT]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(sizeof($errors) === 0 && !$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_DDBB_UPDATE_ERROR);
			}
		} catch(\PDOException $exception) {
			if(sizeof($errors) === 0) {
				$errors = array($exception->getMessage());
			}
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
			echo $exception->getMessage();
		}
		return $errors;
	}
	
	public static function getById(int $id) : object {
		return Database::getInstance()->query(
			'SELECT
				`company`.`id` AS `company_id`,
				`company`.`name` AS `company_name`
			FROM
				`company`
			WHERE
				`company`.`id` = ?
			LIMIT 1;',
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize();
	}
	
	public static function getGames(int $id) : array {
		return Database::getInstance()->query(
			'SELECT
				`game`.`id` AS `game_id`,
				`game`.`name` AS `game_name`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`
			FROM
				`game_company`
			LEFT JOIN
				`game`
			ON
				`game_company`.`game` = `game`.`id`
			LEFT JOIN
				`platform`
			ON
				`game`.`platform` = `platform`.`id`
			WHERE
				`game_company`.`company` = ?
			GROUP BY
				`game`.`name`
			ORDER BY
				`game`.`name` ASC;',
			[$id],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getLimit(int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			'SELECT
				`company`.`id` AS `company_id`,
				`company`.`name` AS `company_name`,
				(SELECT
					COUNT(DISTINCT(`game_company`.`game`)) FROM `game_company` WHERE `game_company`.`company` = `company`.`id` LIMIT 1
				) AS `company_games`
			FROM
				`company`
			ORDER BY
				`company`.`name` ASC
			LIMIT ?, ?;',
			[$startElement, $numberOfElements],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotal() : int {
		return Database::getInstance()->query('SELECT `company`.`id` FROM `company`;', [], [])->count();
	}
}
