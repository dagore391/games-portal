<?php
namespace app\config;

use app\data\UrlsEnum;

final class Urls {
    public static function getUrl(UrlsEnum $url, array $params = []): string {
        return sizeof($params) > 0 ? vsprintf($url->value, $params) : $url->value;
    }

    public static function conditionalRedirectionTo(UrlsEnum $url, array $params = [], bool $redirect = false): void {
        if($redirect) {
            self::redirectTo($url, $params);
        }
    }

    public static function redirectTo(UrlsEnum $url, array $params = []): void {
        header('Location: ' . self::getUrl($url, $params));
        exit();
    }
}
