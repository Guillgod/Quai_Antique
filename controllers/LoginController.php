<?php
// controllers/LoginController.php

require_once '../models/Model_user.php';

class LoginController {
    public function login($email, $password) {
        $userModel = new ModelUser();
        $user = $userModel->verifyLogin($email, $password);

        if ($user) {
            // Connexion OK : on stocke l'utilisateur dans la session avec les bonnes clés
            session_start();
            $_SESSION['user'] = [
                'id_users'  => $user['id_users'],      // <-- CLÉ ALIGNÉE sur ta BDD
                'email'     => $user['email'],
                'nom'       => $user['nom'],
                'is_admin'  => $user['is_admin'] ?? 0, // Optionnel selon ton schéma
            ];
            header('Location: ../views/Page_accueil.php');
            exit();
        } else {
            // Connexion KO : renvoyer une erreur
            return "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>
