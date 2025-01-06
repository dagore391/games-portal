<?php
    namespace app\security;
    
    final class Cookie {
        public static function delete(string $name) : void {
            if(self::exists($name)) {
                setcookie($name, '', -1);
                unset($_COOKIE[$name]);
            }
        }
        
        public static function exists(string $name) : bool {
            return isset($_COOKIE[$name]);
        }
        
        public static function get(string $name) {
            return $_COOKIE[$name];
        }
        
        public static function set(string $name, $value, int $expirationInDays) {
            setcookie($name, $value, time() + 60 * 60 * 24 * $expirationInDays);
        }
    }
