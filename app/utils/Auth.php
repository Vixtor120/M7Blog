<?php
namespace utils;

class Auth {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($user) {
        self::startSession();
        $_SESSION['user'] = $user;
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user']);
    }

    public static function logout() {
        self::startSession();
        unset($_SESSION['user']);
        session_destroy();
    }
}
?>