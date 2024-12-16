<?php
class UserModel {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function register($username, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO Users (username, email, password_hash, role) 
                  VALUES (:username, :email, :password_hash, :role)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $hashedPassword,
            ':role' => $role
        ]);
    }
    
    public function login($email, $password) {
        $query = "SELECT user_id, username, email, password_hash, role 
                 FROM Users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        
        return false;
    }
    
    public function emailExists($email) {
        $query = "SELECT COUNT(*) FROM Users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}