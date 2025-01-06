<?php
namespace app\model;

use app\data\Database;
use app\security\Logger;

class PlatformModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `platform`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}

	public static function insert(string $name, string $tag, string $releaseDate, string $colour, int $featured) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `platform` (`id`, `name`, `tag`, `release_date`, `colour`, `featured`) VALUES (?, ?, ?, ?, ?, ?);',
				[$insertId, $name, $tag, $releaseDate, $colour, $featured],
				[\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT]
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
	
	public static function update(int $id, string $name, string $tag, string $releaseDate, string $colour, int $featured) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `platform` SET `name` = ?, `tag` = ?, `release_date` = ?, `colour` = ?, `featured` = ? WHERE `id` = ?;',
				[$name, $tag, $releaseDate, $colour, $featured, $id],
				[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT]
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
	
	public static function getFeaturedPlatforms() : array {
		return Database::getInstance()->query(
			'SELECT
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				`platform`.`colour` AS `platform_colour`
			FROM
				`platform`
			WHERE
				`platform`.`featured` = ?
			ORDER BY
				`platform`.`tag` ASC;',
			[1],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getById(int $id) : ?object {
		return Database::getInstance()->query(
			'SELECT
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				`platform`.`colour` AS `platform_colour`,
				`platform`.`release_date` AS `platform_release_date`,
				`platform`.`featured` AS `platform_featured`
			FROM
				`platform`
			WHERE
				`platform`.`id` = ?
			LIMIT 1;',
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize();
	}
	
	public static function getPlatformByTag(string $tag) : ?object {
		return Database::getInstance()->query(
			'SELECT
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				`platform`.`colour` AS `platform_colour`
			FROM
				`platform`
			WHERE
				`platform`.`tag` = ?
			LIMIT 1;',
			[$tag],
			[\PDO::PARAM_STR]
		)->firstSanitize();
	}
	
	public static function selectAll() : array {
		return Database::getInstance()->query(
			'SELECT
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`
			FROM
				`platform`
			ORDER BY
				`platform`.`name` ASC;',
			[],
			[]
		)->resultsSanitize();
	}
	
	public static function getLimit(int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			'SELECT
				`platform`.`id` AS `platform_id`,
				`platform`.`name` AS `platform_name`,
				`platform`.`tag` AS `platform_tag`,
				COUNT(`game`.`id`) AS `platform_total_games`
			FROM
				`platform`
			LEFT JOIN
				`game`
			ON
				`platform`.`id` = `game`.`platform`
			GROUP BY
				`platform`.`id`
			ORDER BY
				`platform`.`name` ASC
			LIMIT ?, ?;',
			[$startElement, $numberOfElements],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotal() : int {
		return Database::getInstance()->query('SELECT `platform`.`id` FROM `platform`;', [], [])->count();
	}
}
