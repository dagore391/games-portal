<?php
namespace app\model;

use app\data\Database;
use app\security\Logger;
use app\security\Security;

class VisitorModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `visitor`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}
	
	public static function insert(string $clientIp, string $clientBrowser, string $requestPage) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `visitor` (`id`, `client_ip`, `client_browser_hash`, `visit_time`, `request_page`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?);',
				[$insertId, $clientIp, Security::hashString($clientBrowser), $requestPage],
				[\PDO::PARAM_INT, \PDO::PARAM_LOB, \PDO::PARAM_STR, \PDO::PARAM_STR]
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
	
	public static function getLastVisit(string $clientIp, string $clientBrowser) : ?object {
		return Database::getInstance()->query(
			'SELECT
				`client_ip`, `client_browser_hash`, `visit_time`, `request_page`
			FROM
				`visitor`
			WHERE
				`client_ip` = ?
			AND
				`client_browser_hash` = ?
			ORDER BY
				`visit_time` DESC
			LIMIT 1;',
			[$clientIp, Security::hashString($clientBrowser)],
			[\PDO::PARAM_LOB, \PDO::PARAM_STR]
		)->firstSanitize();
	}
	
	public static function getTotalUniqueVisits() : int {
		return Database::getInstance()->query('SELECT DISTINCT `client_ip` FROM `visitor`;')->count();
	}
	
	public static function getTotalVisits() : int {
		return Database::getInstance()->query('SELECT `client_ip` FROM `visitor`;')->count();
	}
}
