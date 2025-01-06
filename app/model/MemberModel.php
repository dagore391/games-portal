<?php
namespace app\model;

use app\config\Constants;
use app\config\Paths;
use app\data\AccountStatesConstants;
use app\data\Database;
use app\file\SecureImport;
use app\file\UploadFile;
use app\security\Logger;
use app\security\Security;

class MemberModel {
	public static function getNextId() : int {
		$nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `member`;', [], [])->first();
		return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
	}

	public static function insert(string $username, string $password, string $salt, string $email, int $inboxMax, string $accountState, string $accountGroup, array $avatar = [], string $activationCode) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			$insertId = self::getNextId();
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `member` (`id`, `username`, `password`, `salt`, `email`, `inbox_max`, `account_state`, `account_group`, `activation_code`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);',
				[$insertId, $username, $password, $salt, $email, $inboxMax, $accountState, $accountGroup, $activationCode],
				[\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR]
			);
			// Se sube el avatar.
			if(!empty($avatar['tmp_name'])) {
				$errors = UploadFile::upload($avatar, Paths::UMEMBERAVATAR, $username, array('image/jpeg'), 262144, true, Constants::IMG_MEMBER_AVATAR_WIDTH, Constants::IMG_MEMBER_AVATAR_HEIGHT);
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
	
	public static function update(string $username, string $email, int $inboxMax, string $accountGroup, string $accountState, array $avatar = []) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `member` SET `email` = ?, `inbox_max` = ?, `account_state` = ?, `account_group` = ? WHERE `username` = ?;',
				[$email, $inboxMax, $accountState, $accountGroup, $username],
				[\PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR]
			);
			// Se sube el avatar.
			if(!empty($avatar['tmp_name'])) {
				$errors = UploadFile::upload($avatar, Paths::UMEMBERAVATAR, $username, ['image/jpeg'], 262144, true, Constants::IMG_MEMBER_AVATAR_WIDTH, Constants::IMG_MEMBER_AVATAR_HEIGHT);
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
	
	public static function getByEmail(string $email) : ?object {
		$memberObject = Database::getInstance()->query('SELECT * FROM `member` WHERE `email` = ? LIMIT 1;', [$email], [\PDO::PARAM_STR])->first();
		if($memberObject != null) {
			// Se recupera la miniatura de la entrada.
			$memberObject->avatar = SecureImport::fixFileResourcePath(
				Paths::UMEMBERAVATAR . $memberObject->username . '.jpg',
				Paths::UMEMBER . 'no-avatar.jpg'
			);
		}
		return $memberObject;
	}
	
	public static function getById(int $id) : ?object {
		$memberObject = Database::getInstance()->query('SELECT * FROM `member` WHERE `id` = ? LIMIT 1;', [$id], [\PDO::PARAM_INT])->first();
		if($memberObject != null) {
			// Se recupera la miniatura de la entrada.
			$memberObject->avatar = SecureImport::fixFileResourcePath(
				Paths::UMEMBERAVATAR . $memberObject->username . '.jpg',
				Paths::UMEMBER . 'no-avatar.jpg'
			);
		}
		return $memberObject;
	}
	
	public static function getByUsername(string $username) : ?object {
		$memberObject = Database::getInstance()->query('SELECT * FROM `member` WHERE `username` = ? LIMIT 1;', [$username], [\PDO::PARAM_STR])->first();
		if($memberObject != null) {
			// Se recupera la miniatura de la entrada.
			$memberObject->avatar = SecureImport::fixFileResourcePath(
				Paths::UMEMBERAVATAR . $memberObject->username . '.jpg',
				Paths::UMEMBER . 'no-avatar.jpg'
			);
		}
		return $memberObject;
	}
	
	public static function updatePassword(int $id, string $password) : bool {
		$result = false;
		$db = Database::getInstance();
		try {
			// Se da de alta el juego en la base de datos.
			$db->query('UPDATE `member` SET `password` = ? WHERE `id` = ?;', [$password, $id], [\PDO::PARAM_STR, \PDO::PARAM_INT]);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
				$result = true;
			} else {
				throw new \PDOException(LANG_DDBB_UPDATE_ERROR);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		} finally {
			$db = null;
		}
		return $result;
	}
	
	public static function isLocked(int $member1, int $member2) : bool {
		return Database::getInstance()->query(
			'SELECT
				`member1`
			FROM
				`member_relation`
			WHERE
				`member_relation`.`member1` = ? AND `member_relation`.`member2` = ?
			AND
				`member_relation`.`state` = ?
			LIMIT 1;',
			[$member1, $member2, AccountStatesConstants::LOCKED],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
		)->first() !== null;
	}
	
	public static function areRequestsBetweenMembers(int $member1, int $member2) : bool {
		return sizeof(Database::getInstance()->query(
			'SELECT
				`member1`
			FROM
				`member_relation`
			WHERE
				`member_relation`.`member1` = ? AND `member_relation`.`member2` = ?
			OR
				`member_relation`.`member1` = ? AND `member_relation`.`member2` = ?;',
			[$member1, $member2, $member2, $member1],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
		)->results()) > 0;
	}
	
	public static function countFailedLogInAttempts(string $username, string $ip) : int {
		return Database::getInstance()->query(
			'SELECT * FROM `login_history` WHERE (`login_member` = ? OR `login_ip` = ?) AND `login_result` = ? AND DATE_ADD(`login_timestamp`, INTERVAL ? MINUTE) >= NOW();',
			[$username, Security::ipStringToBinary($ip), 'NOK', Constants::ATTEMPT_LIMIT_MINUTES],
			[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT]
		)->count();
	}
	
	public static function updateLastLogin(string $username) : bool {
		$result = false;
		$db = Database::getInstance();
		try {
			// Se da de alta el juego en la base de datos.
			$db->query(
				'UPDATE `member` SET `last_login` = CURRENT_TIMESTAMP WHERE `username` = ?;',
				[$username],
				[\PDO::PARAM_STR]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
				$result = true;
			} else {
				throw new \PDOException(LANG_DDBB_UPDATE_ERROR);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		} finally {
			$db = null;
		}
		return $result;
	}
	
	public static function addAttemptToLoginHistory(string $ip, string $username, bool $loginResult) : bool {
		$result = false;
		$db = Database::getInstance();
		try {
			// Se da de alta el juego en la base de datos.
			$db->query(
				'INSERT INTO `login_history` (`login_ip`, `login_member`, `login_result`) VALUES (?, ?, ?);',
				[Security::ipStringToBinary($ip), $username, $loginResult],
				[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
				$result = true;
			} else {
				throw new \PDOException(LANG_DDBB_INSERT_ERROR);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		} finally {
			$db = null;
		}
		return $result;
	}
	
	public static function updatePreferences(int $id, string $email, string $myProfileVisivility, string $myProfilePublish, string $sendMeMessages, string $language) : bool {
		$result = false;
		$db = Database::getInstance();
		try {
			// Se da de alta el juego en la base de datos.
			$db->query(
				'UPDATE `member` SET `email` = ?, `myprofile_visivility` = ?, `myprofile_publish` = ?, `sendme_messages` = ?, `language` = ? WHERE `id` = ?;',
				[$email, strtoupper($myProfileVisivility), strtoupper($myProfilePublish), strtoupper($sendMeMessages), $language, $id],
				[\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
				$result = true;
			} else {
				throw new \PDOException(LANG_DDBB_UPDATE_ERROR);
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
		return $result;
	}
	
	public static function getMessage(int $messageId, int $memberId) : ?object {
		return Database::getInstance()->query(
			'SELECT `member_message`.`id` AS `message_id`, `is_read`, `message`, `send_date`, `subject`, `username` FROM `member_message` LEFT JOIN `member` ON `member_message`.`member_from` = `member`.`id` WHERE `member_message`.`id` = ? AND `member_to` = ? AND `is_deleted` <> 1 ORDER BY `send_date` DESC LIMIT 1;',
			[$messageId, $memberId],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->firstSanitize(['subject']);
	}

	public static function getTotalFriends(int $memberId) : int {
		return Database::getInstance()->query(
			'SELECT `id`, `username` FROM `member` LEFT JOIN `member_relation` ON `member`.`id` = `member_relation`.`member2` WHERE (`member1` = ? OR `member2` = ?) AND `member_relation`.`state` = ?;',
			[$memberId, $memberId, 'FRIENDSHIP'],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
		)->count();
	}

	public static function areFriends(int $member1Id, int $member2Id) : bool {
		$member = Database::getInstance()->query(
			'SELECT `id`, `username` FROM `member` LEFT JOIN `member_relation` ON `member`.`id` = `member_relation`.`member2` WHERE (`member1` = ? AND `member2` = ? OR `member2` = ? AND `member1` = ?) AND `member_relation`.`state` = ?;',
			[$member1Id, $member2Id, $member1Id, $member2Id, 'FRIENDSHIP'],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
		)->firstSanitize();
		return $member != null;
	}

	public static function getByMemberAndState(int $memberId, string $state) : array {
		return Database::getInstance()->query(
			'SELECT `member`.`id` AS `member_id`, `member`.`username` AS `member_username`, `relation_date` FROM `member_relation` RIGHT JOIN `member` ON `member_relation`.`member1` = `member`.`id` WHERE (`member_relation`.`member1` = ? OR `member_relation`.`member2` = ?) AND `member_relation`.`state` = ?;',
			[$memberId, $memberId, $state],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
		)->resultsSanitize();
	}

	public static function getFriends(int $memberId) : array {
		return Database::getInstance()->query(
			'SELECT
				`m`.`id`AS `member_id`,
				`m`.`username` AS `member_username`,
				`r`.`relation_date` AS `member_relation_date`
			FROM
				`member` `m`
			LEFT JOIN
				`member_relation` `r`
			ON
				`m`.`id` = `r`.`member1`
			WHERE
				`r`.`state` = ?
			AND
				`m`.`id` <> ?
			AND
				`r`.`member2` IN (SELECT `member_relation`.`member2` FROM `member_relation` WHERE `member_relation`.`member1` = `m`.`id`)
			UNION ALL
			SELECT
				`m`.`id`AS `member_id`,
				`m`.`username` AS `member_username`,
				`r`.`relation_date` AS `member_relation_date`
			FROM
				`member` `m`
			LEFT JOIN
				`member_relation` `r`
			ON
				`m`.`id` = `r`.`member2`
			WHERE
				`r`.`state` = ?
			AND
				`m`.`id` <> ?
			AND
				`r`.`member1` IN (SELECT `member_relation`.`member1` FROM `member_relation` WHERE `member_relation`.`member2` = `m`.`id`);',
			['FRIENDSHIP', $memberId, 'FRIENDSHIP', $memberId],
			[\PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_INT]
		)->resultsSanitize();
	}

	public static function getInboxMessages(int $memberId) : array {
		return Database::getInstance()->query(
			'SELECT `member_message`.`id` AS `message_id`, `is_read`, `message`, `subject`, `username` FROM `member_message` LEFT JOIN `member` ON `member_message`.`member_from` = `member`.`id` WHERE `member_to` = ? AND `is_deleted` = 0;',
			[$memberId],
			[\PDO::PARAM_INT]
		)->resultsSanitize(['message']);
	}

	public static function markMessageAsRead(int $messageId, int $memberId) : int {
		$affectedRows = 0;
		$db = Database::getInstance();
		try {
			// Se elimina el mensaje privado.
			$affectedRows = $db->query(
				'UPDATE `member_message` SET `is_read` = ? WHERE `id` = ? AND `is_deleted` <> 1 AND `member_to` = ?;',
				[1, $messageId, $memberId],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
			)->affectedRows();
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
		return $affectedRows;
	}

	public static function deleteMessage(int $messageId, int $memberId) : int {
		$affectedRows = 0;
		$db = Database::getInstance();
		try {
			// Se elimina el mensaje privado.
			$affectedRows = $db->query(
				'UPDATE `member_message` SET `is_deleted` = 1 WHERE `id` = ? AND `member_to` = ? AND `is_deleted` <> 1;',
				[$messageId, $memberId],
				[\PDO::PARAM_INT, \PDO::PARAM_INT]
			)->affectedRows();
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(!$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(sprintf(LANG_DELETING_PRIVATE_MESSAGE_ERROR, $memberId, $messageId));
			}
		} catch(\PDOException $exception) {
			$db->rollback();
			Logger::logError($exception->getMessage(), $exception);
		}
		return $affectedRows;
	}
	
	public static function reportMessage(int $informerMemberId, int $reportedMemberId, int $messageId, string $messageContent, string $reportedType) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se da de alta el registro en la base de datos.
			$db->query(
				'INSERT INTO `member_report` (`message_id`, `message_content`, `informer_member`, `reported_member`, `type`) VALUES (?, ?, ?, ?, ?);',
				[$messageId, $messageContent, $informerMemberId, $reportedMemberId, $reportedType],
				[\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
			);
			// Si no ha habido errores, se hace commit, en caso contrario, se deshace la operación.
			if(sizeof($errors) === 0 && !$db->isError()) {
				$db->commit();
			} else {
				throw new \PDOException(LANG_REPORTING_MESSAGE_ERROR);
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
	
	public static function checkReportedMessage(int $informerMemberId, int $reportedMemberId, int $messageId, string $reportedType) : bool {
		$count = Database::getInstance()->query(
			'SELECT `id` FROM `member_report` WHERE `message_id` = ? AND `informer_member` = ? AND `reported_member` = ? AND `type` = ?;',
			[$messageId, $informerMemberId, $reportedMemberId, $reportedType],
			[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
		)->count();
		return $count == 1;
	}

	public static function updateRequest(int $member1Id, int $member2Id, string $state) : int {
		$affectedRows = 0;
		$db = Database::getInstance();
		try {
			// Se eliminan todos las relaciones del usuario 1 con el 2 (menos las de bloqueo del usuario 2 con el 1).
			$affectedRows = $db->query(
				'UPDATE `member_relation` SET `state` = ?, `relation_date` = CURRENT_TIMESTAMP() WHERE `member1` = ? AND `member2` = ?;',
				[$state, $member1Id, $member2Id],
				[\PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT]
			)->affectedRows();
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
		return $affectedRows;
	}

	public static function deleteRequest(int $member1Id, int $member2Id, string $state) : int {
		$affectedRows = 0;
		$db = Database::getInstance();
		try {
			// Se eliminan todos las relaciones del usuario 1 con el 2 (menos las de bloqueo del usuario 2 con el 1).
			$affectedRows = $db->query(
				'DELETE FROM `member_relation` WHERE (`member1` = ? AND `member2` = ? OR `member2` = ? AND `member1` = ?) AND `state` = ?;',
				[$member1Id, $member2Id, $member1Id, $member2Id, $state],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
			)->affectedRows();
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
		return $affectedRows;
	}

	public static function getTotalUnreadMessages(int $memberId) : int {
		return Database::getInstance()->query(
			'SELECT `id` FROM `member_message` WHERE `member_to` = ? AND `is_deleted` = 0 AND `is_read` = 0;',
			[$memberId],
			[\PDO::PARAM_INT]
		)->count();
	}
	
	public static function lock(int $member1, int $member2) : void {
		$db = Database::getInstance();
		try {
			// Se eliminan todos las relaciones del usuario 1 con el 2 (menos las de bloqueo del usuario 2 con el 1).
			$db->query(
				'DELETE FROM `member_relation` WHERE `member1` = ? AND `member2` = ? OR `member2` = ? AND `member1` = ? AND `state` <> ?;',
				[$member1, $member2, $member2, $member1, 'LOCKED'],
				[\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_STR]
			);
			// Se da de alta el registro para bloquear a un usuario.
			$db->query(
				'INSERT INTO `member_relation` (`member1`, `member2`, `state`) VALUES (?, ?, ?);',
				[$member1, $member2, AccountStatesConstants::LOCKED],
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
	
	public static function unlock(int $member1, int $member2) : void {
		$db = Database::getInstance();
		try {
			// Se elimina la relación de bloqueo del usuario 1 con el 2 (no a la inversa).
			$db->query(
				'DELETE FROM `member_relation` WHERE `member1` = ? AND `member2` = ? AND `state` = ?;',
				[$member1, $member2, AccountStatesConstants::LOCKED],
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
	
	public static function sendRequest(int $member1, int $member2) : void {
		$db = Database::getInstance();
		try {
			// Se crea una relación de solicitud del usuario 1 con el 2 (no a la inversa).
			$db->query(
				'INSERT INTO `member_relation` (`member1`, `member2`, `state`) VALUES (?, ?, ?);',
				[$member1, $member2, AccountStatesConstants::REQUEST],
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
	
	public static function getLimit(int $startElement, int $numberOfElements) : array {
		return Database::getInstance()->query(
			'SELECT
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`member`.`account_group` AS `member_account_group`
			FROM
				`member`
			ORDER BY
				`member`.`username` ASC
			LIMIT ?, ?;',
			[$startElement, $numberOfElements],
			[\PDO::PARAM_INT, \PDO::PARAM_INT]
		)->resultsSanitize();
	}
	
	public static function getTotal() : int {
		return Database::getInstance()->query('SELECT `member`.`id` FROM `member`;', [], [])->count();
	}
	
	public static function getTotalOnline() : int {
		return Database::getInstance()->query('SELECT `member`.`id` FROM `member` WHERE `last_login` > NOW() - INTERVAL 15 MINUTE;', [], [])->count();
	}
	
	public static function activateAccount(string $username) : array {
		$errors = [];
		$db = Database::getInstance();
		try {
			// Se actualizan los datos del registro.
			$db->query(
				'UPDATE `member` SET `account_state` = ?, `activation_code` = NULL WHERE `username` = ?;',
				[AccountStatesConstants::ACTIVED, $username],
				[\PDO::PARAM_STR, \PDO::PARAM_STR]
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
}
