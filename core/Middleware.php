<?php

namespace Core;

class Middleware {
    public static function handleAuth() {
        // Örnek  kimlik doğrulama controll
        if (!isset($_SESSION['user'])) {
            http_response_code(403);
            echo "Access Denied: Authentication Required";
            exit;
        }
    }
}
