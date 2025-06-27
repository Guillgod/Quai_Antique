<?php
// controllers/LoginController.php

require_once  '../models/Model_user.php';

class LoginController {
    public function login($email, $password) {
        $userModel = new ModelUser();
        $user = $userModel->verifyLogin($email, $password);

        if ($user) {
            // Connexion OK : on démarre la session
            session_start();
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nom'] = $user['nom'];
            // Redirection ou affichage d’un message de bienvenue
            header('Location: ../views/Page_accueil.php');
            exit();
        } else {
            // Connexion KO : renvoyer une erreur
            return "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>