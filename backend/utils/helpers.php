<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

class Helpers {
    public static function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public static function jsonResponse($status, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
        exit;
    }

    public static function generateRandomToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
}
