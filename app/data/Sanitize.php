<?php
namespace app\data;

final class Sanitize {
	public static function cleanHtml(string $text) : string {
		return htmlspecialchars($text);
	}
	
	public static function trimText(string $text) : string {
		return trim($text);
	}
	
	public static function fullClean(string $text) : string {
		return self::cleanHtml(self::trimText($text));
	}
	
	public static function cleanObjectHtml(object $object, array $excludeFields = []) : object {
		foreach($object as $key => $value) {
			if(!in_array($key, $excludeFields) && is_string($value)) {
				$object->$key = self::cleanHtml($value);
			}
		}
		return $object;
	}
}
