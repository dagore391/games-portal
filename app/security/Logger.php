<?php
namespace app\security;

final class Logger {
    public const LOG_FILE_PATH = 'C:/xampp/htdocs/app/logs/logs.log';
    
    public static function logError(string $message = '', \Exception $exception = null) : void {
        self::_logToFile("[ERROR] - $message", $exception);
    }
    
    public static function logTrace(string $message = '', \Exception $exception = null) : void {
        self::_logToFile("[TRACE] - $message", $exception);
    }
    
    private static function _logToFile(string $message = '', \Exception $exception = null) : void {
        if(!empty($message)) {
            error_log($message . ($exception !== null ? '' : ": $exception"), 3, self::LOG_FILE_PATH);
        }
    }
}
