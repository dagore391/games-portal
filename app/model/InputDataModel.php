<?php
namespace app\model;

use app\data\Database;

class InputDataModel {
	public static function getCompanies() : array {
		return Database::getInstance()->query(
			'SELECT
				`company`.`name` AS `tag`,
				`company`.`id` AS `value`
			FROM
				`company`
			ORDER BY
				`company`.`name` ASC;',
			[],
			[]
		)->resultsSanitize();
	}
	
	public static function getMetadata() : array {
		return Database::getInstance()->query(
			'SELECT
				`metadata`.`id` AS `value`,
				`metadata`.`value` AS `tag`,
				`metagroup`.`name` AS `group`
			FROM
				`metadata`
			LEFT JOIN
				`metagroup`
			ON
				`metadata`.`group` = `metagroup`.`id`
			ORDER BY
				`metagroup`.`name` ASC, `metadata`.`value` ASC;',
			[],
			[]
		)->resultsSanitize();
	}
}
