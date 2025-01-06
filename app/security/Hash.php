<?php
    namespace app\security;
    
    final class Hash {
        public static function make(string $value, string $salt) : string {
            return hash('sha256', $value . $salt);
        }
        
        public static function salt(int $size) : string {
            return mcrypt_create_iv($size);
        }
        
        public static function unique() : string {
            return self::make(uniqid(), '');
        }
    }
?>
