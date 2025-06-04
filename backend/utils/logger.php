<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class Logger {
    private static $logFile = '../../logs/errors.log';

    public static function log($message) {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}
