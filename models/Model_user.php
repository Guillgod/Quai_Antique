<?php
// models/Model_user.php

require_once __DIR__ . '/../config/Database.php';

class ModelUser {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function verifyLogin($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Connexion OK, retourne les infos utilisateur
        }
        return false; // Identifiants incorrects
    }
}
?>