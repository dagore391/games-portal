<?php
namespace app\model;

use app\data\Database;
use app\security\Logger;

class ForumPostModel {
    public static function getNextId() : int {
        $nextValue = Database::getInstance()->query('SELECT MAX(`id`) + 1 AS `id` FROM `forum_post`;', [], [])->first();
        return $nextValue === null || $nextValue->id === null ?  0 : $nextValue->id;
    }
    
    public static function getById(int $id) : ?object {
        return Database::getInstance()->query(
            'SELECT
					`forum_post`.`id` AS `post_id`,
					`forum_post`.`title` AS `post_title`,
					`forum_post`.`content` AS `post_content`,
					`forum_post`.`published` AS `post_published`,
					`forum_post`.`is_closed` AS `post_is_closed`,
					`forum_post`.`topic` AS `post_topic`,
					`forum`.`id` AS `forum_id`,
					`forum`.`title` AS `forum_title`,
					`member`.`id` AS `member_id`,
					`member`.`username` AS `member_username`,
					`member`.`account_group` AS `member_account_group`
				FROM
					`forum_post`
				LEFT JOIN
					`member`
				ON
					`forum_post`.`author` = `member`.`id`
				LEFT JOIN
					`forum`
				ON
					`forum_post`.`forum` = `forum`.`id`
				WHERE
					`forum_post`.`id` = ?
				LIMIT 1;',
            [$id],
            [\PDO::PARAM_INT]
        )->firstSanitize(['topic_content']);
    }
    
    public static function insert(string $title, string $content, int $author, int $forum, int $topic, bool $isClosed) : array {
        $errors = [];
        $db = Database::getInstance();
        try {
            $insertId = self::getNextId();
            // Se da de alta el registro en la base de datos.
            $db->query(
                'INSERT INTO `forum_post` (`id`, `title`,`content`, `author`, `forum`, `topic`, `is_closed`) VALUES (?, ?, ?, ?, ?, ?, ?);',
                [$insertId, $title, $content, $author, $forum, $topic, $isClosed],
                [\PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT]
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
