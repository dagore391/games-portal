<?php
namespace app\security;

use app\model\SecurityTokenModel;

final class Token {
	public static function check(string $tokenString, string $page, string $ip, string $browser) : bool {
		$result = false;
		if($ip !== null) {
			$token = SecurityTokenModel::getByPageIp($page, $ip);
			if($token !== null) {
				$result = $token->content_hash === $tokenString;
				SecurityTokenModel::delete($ip, $page);
				self::generate($ip, $page, $browser);
			}
		}
		return $result;
	}
	
	public static function generate(string $ip, string $page, string $browser) : string {
		if($ip !== null) {
			SecurityTokenModel::delete($ip, $page);
			$token = hash('sha512', (time() .uniqid()));
			SecurityTokenModel::insert($ip, $browser, $token, $page);
		}
		return $token;
	}
}
