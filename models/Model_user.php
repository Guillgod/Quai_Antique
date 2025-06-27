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
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Connexion OK, retourne les infos utilisateur
        }
        return false; // Identifiants incorrects
    }

      // Création utilisateur + gestion des allergies (table de jointure)
    public function createUser($prenom, $nom, $email, $password, $nb_persons, $tel, $allergies = []) {
        try {
            // Début transaction pour tout ou rien
            $this->conn->beginTransaction();

            // 1. Insérer l'utilisateur
            $sql = "INSERT INTO users (prenom, nom, email, password, nb_persons, is_admin, tel)
                    VALUES (:prenom, :nom, :email, :password, :nb_persons, 0, :tel)";
            $stmt = $this->conn->prepare($sql);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindValue(':prenom', $prenom);
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':nb_persons', $nb_persons);
            $stmt->bindValue(':tel', $tel);
            $stmt->execute();

            $user_id = $this->conn->lastInsertId();

            // 2. Associer les allergies
            if (!empty($allergies)) {
                // Création dynamique des placeholders
                $placeholders = [];
                foreach ($allergies as $idx => $allergie) {
                    $placeholders[] = ":allergie_$idx";
                }
                $in = implode(',', $placeholders);
                $sqlAllergies = "SELECT id_allergie FROM allergie WHERE type_allergie IN ($in)";
                $stmtAllergies = $this->conn->prepare($sqlAllergies);

                // Liaison des paramètres (bindValue)
                foreach ($allergies as $idx => $allergie) {
                    // PDO::PARAM_STR pour un VARCHAR
                    $stmtAllergies->bindValue(":allergie_$idx", $allergie, PDO::PARAM_STR);
                }

                $stmtAllergies->execute();
                $ids = $stmtAllergies->fetchAll(PDO::FETCH_COLUMN);

                foreach ($ids as $id_allergie) {
                    $sqlJointure = "INSERT INTO user_allergie (user_id, allergie_id) VALUES (:user_id, :allergie_id)";
                    $stmtJointure = $this->conn->prepare($sqlJointure);
                    $stmtJointure->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $stmtJointure->bindValue(':allergie_id', $id_allergie, PDO::PARAM_INT);
                    $stmtJointure->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }


// // Récupère les allergies d'un utilisateur par son ID
public function getAllergiesByUserId($user_id) {
    $sql = "SELECT type_allergie FROM allergie 
            JOIN user_allergie ON user_allergie.allergie_id = allergie.id_allergie
            WHERE user_allergie.user_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function getUserById($user_id) {
    $sql = "SELECT * FROM users WHERE id_users = :user_id LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Renvoie un tableau associatif ou false si non trouvé
}
}