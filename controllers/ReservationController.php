<?php
// controllers/ReservationController.php

require_once '../models/ModelReservation.php';
require_once '../models/Model_user.php';

class ReservationController {
    public function submitReservation($data) {
        
        if (!isset($_SESSION['user'])) {
            // Redirige vers login si pas connecté
            header('Location: login.php?must_connect=1');
            exit();
        }

        $user_id = $_SESSION['user']['id_users'];
        $reservation_date = $data['date'] ?? '';
        $service = $data['service'] ?? '';
        $reservation_heure = $data['horaire'] ?? '';
        $couvert = $data['couvert'] ?? 2;
        $allergies = isset($data['allergies']) ? $data['allergies'] : [];
        // Insertion réservation

        $model = new ModelReservation();
        $model->addReservation($reservation_date, $reservation_heure, $user_id, $couvert, $allergies);

        // (Bonus) On pourrait aussi stocker les allergies réservées ce jour, si tu veux un historique.

        // Redirige ou affiche un message de succès
        header('Location: success_reservation.php');
        exit();
    }

    // Pour le préremplissage
    public function getUserDefaultData() {
        
        if (!isset($_SESSION['user'])) return null;
        $modelUser = new ModelUser();
        $user = $modelUser->getUserById($_SESSION['user']['id_users']);
        $allergies = $modelUser->getAllergiesByUserId($_SESSION['user']['id_users']);
        return [
            'nb_persons' => $user['nb_persons'],
            'allergies' => $allergies
        ];
    }
}
