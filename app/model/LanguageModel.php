<?php
namespace app\model;

use app\data\Database;

class LanguageModel {
	public static function selectAll() : ?array {
		return Database::getInstance()->query("SELECT `tag`, `name` FROM `language`;", [], [])->resultsSanitize();
	}
}
