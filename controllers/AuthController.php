<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $database = new Database();
        $this->userModel = new UserModel($database->getConnection());
    }
    
    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'customer';
        
        if (empty($username) || empty($email) || empty($password)) {
            return ['error' => 'All fields are required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format'];
        }
        
        if ($this->userModel->emailExists($email)) {
            return ['error' => 'Email already registered'];
        }
        
        if ($this->userModel->register($username, $email, $password, $role)) {
            return ['success' => true];
        }
        
        return ['error' => 'Registration failed'];
    }
    
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            return ['error' => 'Email and password are required'];
        }
        
        $user = $this->userModel->login($email, $password);
        
        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: ../views/admin/dashboard.php');
            } else {
                header('Location: ../views/customer/dashboard.php');
            }
            exit();
        }
        
        return ['error' => 'Invalid email or password'];
    }
}