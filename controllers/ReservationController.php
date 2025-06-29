<?php
// controllers/ReservationController.php

require_once '../models/ModelReservation.php';
require_once '../models/Model_user.php';

class ReservationController {
    public function submitReservation($data) {
        $is_admin = !empty($_GET['from_admin']) || !empty($data['from_admin']);

        if (!$is_admin && !isset($_SESSION['user'])) {
            header('Location: login.php?must_connect=1');
            exit();
        }

        $model = new ModelReservation();

        $user_id = $_SESSION['user']['id_users'] ?? null;
        $reservation_date = $data['date'] ?? '';
        $service = $data['service'] ?? '';
        $reservation_heure = $data['horaire'] ?? '';
        $couvert = $data['couvert'] ?? 2;
        $allergies = isset($data['allergies']) ? $data['allergies'] : [];

        // Si édition
        if (!empty($data['edit_resa'])) {
            if ($is_admin) {
                $model->updateReservationByIdAdmin((int)$data['edit_resa'], $reservation_date, $reservation_heure, $couvert, $allergies);
            } else {
                $model->updateReservationById((int)$data['edit_resa'], $reservation_date, $reservation_heure, $couvert, $allergies, $user_id);
            }
        } else {
            $model->addReservation($reservation_date, $reservation_heure, $user_id, $couvert, $allergies);
        }
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
