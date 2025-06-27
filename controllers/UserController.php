<?php
require_once '../models/Model_user.php';

class UserController {
    public function updateUserInfos($post, $user_id, $currentHash) {
    $prenom = trim($post['prenom']);
    $nom = trim($post['nom']);
    $email = trim($post['email']);
    $tel = $post['tel'] ?? '';
    $nb_persons = $post['nb_persons'];
    $mdp = $post['mdp'] ?? '';
    $mdp2 = $post['mdp2'] ?? '';
    $allergies = (isset($post['allergie']) && $post['allergie'] === 'oui' && isset($post['allergies'])) ? $post['allergies'] : [];

    // Si au moins un des deux champs mot de passe est rempli, on doit vérifier les deux.
    if ($mdp !== '' || $mdp2 !== '') {
        if ($mdp !== $mdp2) {
            return "Les mots de passe ne correspondent pas.";
        }
        $passwordToSave = $mdp;
    } else {
        // Aucun changement de mdp
        $passwordToSave = $currentHash; // Il ne sera pas re-hashé dans ce cas
    }

    $modelUser = new ModelUser();
    $result = $modelUser->updateUser($user_id, $prenom, $nom, $email, $passwordToSave, $nb_persons, $tel, $allergies, ($mdp === '' && $mdp2 === ''));

    if ($result === true) {
        return "Modifications enregistrées !";
    } else {
        return "Erreur : " . $result;
    }
}
}
