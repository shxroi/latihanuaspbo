<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the default timezone
date_default_timezone_set('Asia/Jakarta');

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload function
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Start session
session_start();

// Handle routing
$request = $_SERVER['REQUEST_URI'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($basePath, '', $request);

// Simple router
switch ($path) {
    case '/':
    case '':
        require BASE_PATH . '/views/login.php';
        break;
    
    case '/register':
        require BASE_PATH . '/views/register.php';
        break;
    
    case '/login':
        require BASE_PATH . '/views/login.php';
        break;
    
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
