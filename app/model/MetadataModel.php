<?php
namespace app\model;

use app\data\Database;
use app\security\Logger;

class MetadataModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `metadata`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}

	public static function insert(string $value, string $group) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `metadata` (`id`, `value`, `group`) VALUES (?, ?, ?);',
				[$insertId, $value, $group],
				[\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_INT]
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
	
	public static function update(int $id, string $value, string $group) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `metadata` SET `value` = ?, `group` = ? WHERE `id` = ?;',
				[$value, $group, $id],
				[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT]
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
	
	public static function getById(int $id) : object {
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
			LEFT JOIN
				`metagroup`
			ON
				`metadata`.`group` = `metagroup`.`id`
			WHERE
				`metadata`.`id` = ?
			LIMIT 1;',
			[$id],
			[\PDO::PARAM_INT]
		)->firstSanitize();
	}
	
	public static function getByGroup(int $metagroupId) : array {
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
			LEFT JOIN
				`metagroup`
			ON
				`metadata`.`group` = `metagroup`.`id`
			WHERE
				`metagroup`.`id` = ?
			ORDER BY
				`metadata`.`value` ASC;',
			[$metagroupId],
			[\PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getLimit(int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			'SELECT
				`metadata`.`id` AS `metadata_id`,
				`metadata`.`value` AS `metadata_value`,
				`metagroup`.`name` AS `metagroup_name`
			FROM
				`metadata`
			LEFT JOIN
				`metagroup`
			ON
				`metadata`.`group` = `metagroup`.`id`
			ORDER BY
				`metagroup`.`name` ASC,
				`metadata`.`value` ASC
			LIMIT ?, ?;',
			[$startElement, $numberOfElements],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotal() : int {
		return Database::getInstance()->query('SELECT `metadata`.`id` FROM `metadata`;', [], [])->count();
	}
}
