<?php
// controllers/CreationCompteController.php
require_once __DIR__ . '/../models/Model_user.php';

class CreationCompteController {
    public function handleInscription($formData) {
        $model = new ModelUser();

        // Extraction des champs du formulaire (avec protection XSS)
        $prenom = htmlspecialchars(trim($formData['prenom']));
        $nom = htmlspecialchars(trim($formData['nom']));
        $email = htmlspecialchars(trim($formData['email']));
        $password = $formData['mdp'];
        $mdp2 = $formData['mdp2'];
        $nb_persons = intval($formData['nb_persons']);
        $tel = htmlspecialchars(trim($formData['tel'] ?? ''));
        $allergies = $formData['allergies'] ?? [];

        // Sécurité et validation
        if ($password !== $mdp2) return "Les mots de passe ne correspondent pas.";
        if (strlen($password) < 8) return "Mot de passe trop court.";

        // TODO : ajouter d’autres contrôles (mail déjà utilisé...)

        // Appel du modèle
        $result = $model->createUser($prenom, $nom, $email, $password, $nb_persons, $tel, $allergies);
        if ($result === true) {
            header("Location: login.php?created=1");
            exit();
        } else {
            return $result;
        }
    }
}