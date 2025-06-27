<?php
// controllers/LoginController.php

require_once  '../models/Model_user.php';

class LoginController {
    public function login($email, $password) {
        $userModel = new ModelUser();
        $user = $userModel->verifyLogin($email, $password);

        if ($user) {
            session_start();
            $_SESSION['user'] = [
                'id_utilisateur' => $user['id_utilisateur'],
                'email' => $user['email'],
                'nom' => $user['nom'],
                'is_admin' => $user['is_admin'] ?? 0, // Ajoute is_admin si tu l'as
            ];
            header('Location: ../views/Page_accueil.php');
            exit();
        } else {
            // Connexion KO : renvoyer une erreur
            return "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>