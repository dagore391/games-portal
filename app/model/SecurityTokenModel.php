<?php
namespace app\model;

use app\data\Database;
use app\security\Security;

class SecurityTokenModel {
	private const _EXPIRATION_SECONDS = 600;

	public static function delete(string $ip, string $page) : bool {
		$db = Database::getInstance();
		try {
			// Se elimina el token de la base de datos.
			$db->query(
				'DELETE FROM `security_token` WHERE `client_ip` = ? AND `request_page` = ? OR `expiration_time` < CURRENT_TIMESTAMP;',
				[Security::ipStringToBinary($ip), $page],
				[\PDO::PARAM_LOB, \PDO::PARAM_STR]
			);
			$db->commit();
			return true;
		} catch(\PDOException $exception) {
			$db->rollback();
			throw $exception;
		}
	}

	public static function insert(string $ip, string $browser, string $token = null, string $page) : bool {
		$db = Database::getInstance();
		try {
			// Se da de alta el token en la base de datos.
			$db->query(
				'INSERT INTO `security_token` (`client_ip`, `client_browser_hash`, `content_hash`, `expiration_time`, `request_page`) VALUES (?, ?, ?, CURRENT_TIMESTAMP + INTERVAL ? SECOND, ?);',
				[Security::ipStringToBinary($ip), Security::hashString($browser), $token, self::_EXPIRATION_SECONDS, $page],
				[\PDO::PARAM_LOB, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_STR]
			);
			$db->commit();
			return true;
		} catch(\PDOException $exception) {
			$db->rollback();
			throw $exception;
		}
	}

	public static function getByPageIp(string $page, string $ip) : ?object {
		$token = Database::getInstance()->query(
			'SELECT
				`client_ip`,
				`client_browser_hash`,
				`content_hash`,
				`expiration_time`,
				`request_page`
			FROM
				`security_token`
			WHERE
				`request_page` = ?
			AND
				`client_ip` = ?
			AND
				`expiration_time` > CURRENT_TIMESTAMP
			LIMIT 1;',
			[$page, Security::ipStringToBinary($ip)],
			[\PDO::PARAM_STR, \PDO::PARAM_LOB]
		)->first();
		// Se convierte la ip de binario a string
		if($token !== null) {
			$token->client_ip = Security::ipBinaryToString($token->client_ip);
		}
		// Se devuelve el token.
		return $token;
	}
}
