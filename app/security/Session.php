<?php
namespace app\security;

final class Session {
    public static function delete(string $name) : void {
        if(self::exists($name)) {
            $_SESSION[$name] = null;
            unset($_SESSION[$name]);
        }
    }
    
    public static function exists(string $name) : bool {
        return isset($_SESSION[$name]);
    }
    
    public static function get(string $name) : mixed {
        return $_SESSION[$name];
    }
    
    public static function put(string $name, $value) : mixed {
        return $_SESSION[$name] = $value;
    }
    
    public static function addElement(string $name, $value) : void {
        $values = [];
        if(self::exists($name)) {
            if(is_array(self::get($name))) {
                $values = self::get($name);
            } else {
                array_push($values, self::get($name));
            }
        }
        array_push($values, $value);
        self::put($name, $values);
    }
}
