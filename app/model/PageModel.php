<?php
namespace app\model;

use app\data\Database;
class PageModel {
	public static function getByPath(string $path) : object {
		return Database::getInstance()->query(
			'SELECT
				`page`.`path` AS `page_path`,
				`page`.`title` AS `page_title`,
				`page`.`content` AS `page_content`,
				`page`.`published` AS `page_published`,
				`member`.`id` AS `member_id`,
				`member`.`username` AS `member_username`
			FROM
				`page`
			LEFT JOIN
				`member`
			ON
				`page`.`author` = `member`.`id`
			WHERE
				`page`.`path` = ?
			LIMIT 1;',
			[$path],
			[\PDO::PARAM_STR]
		)->firstSanitize(['page_content']);
	}
}
