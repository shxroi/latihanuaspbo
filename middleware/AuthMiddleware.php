<?php
session_start();

class AuthMiddleware {
    public static function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /views/login.php');
            exit();
        }
    }
    
    public static function requireAdmin() {
        self::requireAuth();
        if ($_SESSION['role'] !== 'admin') {
            header('Location: /views/403.php');
            exit();
        }
    }
}
