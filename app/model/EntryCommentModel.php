<?php
namespace app\model;

use app\config\Paths;
use app\data\Database;
use app\file\SecureImport;
use app\security\Logger;

class EntryCommentModel {
    public static function getNextId() : int {
        $nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `entry_comment`;', [], [])->first();
        return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
    }
    
    public static function getById(int $id) : ?object {
        return Database::getInstance()->query(
            'SELECT
            	`entry_comment`.`id` AS `comment_id`,
            	`entry_comment`.`content` AS `comment_content`,
            	`entry_comment`.`published` AS `comment_published`,
            	`entry_comment`.`entry` AS `comment_entry`,
            	`entry_comment`.`author` AS `comment_author`,
                `entry`.`category` AS `entry_category`
			FROM
				`entry_comment`
			LEFT JOIN
				`entry`
			ON
				`entry_comment`.`entry` = `entry`.`id`
            WHERE
                `entry_comment`.`id` = ?
			LIMIT 1;',
            [$id],
            [\PDO::PARAM_INT]
        )->firstSanitize(['entry_content']);
    }
    
    public static function getEntryLimitComments(int $entryId, int $start, int $limit) : array {
        $results = Database::getInstance()->query(
            'SELECT
				`entry_comment`.`id` AS `comment_id`,
				`entry_comment`.`content` AS `comment_content`,
				`entry_comment`.`entry` AS `comment_entry`,
				`entry_comment`.`author` AS `comment_author`,
				`entry_comment`.`published` AS `comment_published`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`,
				`member`.`account_group` AS `member_account_group`
			FROM
				`entry_comment`
			LEFT JOIN
				`member`
			ON
				`entry_comment`.`author` = `member`.`id`
			WHERE
				`entry_comment`.`entry` = ?
			LIMIT ?, ?;',
            [$entryId, $start, $limit],
            [\PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
        )->resultsSanitize(['comment_content']);
        foreach($results as $row) {
            $row->member_avatar = SecureImport::fixFileResourcePath(
                Paths::UMEMBERAVATAR . $row->member_username . '.jpg',
                Paths::UMEMBER . 'no-avatar.jpg'
            );
        }
        return $results;
    }
    
    public static function getTotalEntryComments(int $id) : int {
        return Database::getInstance()->query('SELECT `id` FROM `entry_comment` WHERE `entry` = ?;', [$id], [\PDO::PARAM_INT])->count();
    }
    
    public static function getLatestByUser(int $userId) : ?object {
        return Database::getInstance()->query(
            'SELECT
					`entry_comment`.`id` AS `comment_id`,
					`entry_comment`.`content` AS `comment_content`,
					`entry_comment`.`entry` AS `comment_entry`,
					`entry_comment`.`author` AS `comment_author`,
					`entry_comment`.`published` AS `comment_published`
				FROM
					`entry_comment`
                WHERE
                    `entry_comment`.`author` = ?
                ORDER BY
                    `entry_comment`.`published` DESC
				LIMIT 1;',
            [$userId],
            [\PDO::PARAM_INT]
        )->firstSanitize(['comment_content']);
    }
    
    public static function insert(string $content, int $entry, int $author) : array {
        $errors = [];
        $db = Database::getInstance();
        try {
            $insertId = self::getNextId();
            // Se da de alta el registro en la base de datos.
            $db->query(
                'INSERT INTO `entry_comment` (`id`, `content`, `entry`, `author`) VALUES (?, ?, ?, ?);',
                [$insertId, $content, $entry, $author],
                [\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT]
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
}
