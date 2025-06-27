<?php
// models/ModelReservation.php
require_once __DIR__ . '/../config/Database.php';

class ModelReservation {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // public function addReservation($date, $heure, $user_id, $couvert) {
    //     $sql = "INSERT INTO reservations (reservation_date, reservation_heure, user_id, couvert) VALUES (:date, :heure, :user_id, :couvert)";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bindParam(':date', $date);
    //     $stmt->bindParam(':heure', $heure);
    //     $stmt->bindParam(':user_id', $user_id);
    //     $stmt->bindParam(':couvert', $couvert);
    //     return $stmt->execute();
    // }

    public function addReservation($date, $heure, $user_id, $couvert, $allergies = []) {
    try {
        $this->conn->beginTransaction();

        $sql = "INSERT INTO reservations (reservation_date, reservation_heure, user_id, couvert) 
                VALUES (:date, :heure, :user_id, :couvert)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':heure', $heure);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':couvert', $couvert);
        $stmt->execute();

        $id_reservations = $this->conn->lastInsertId();

        if (!empty($allergies)) {
            $placeholders = [];
            foreach ($allergies as $idx => $allergie) {
                $placeholders[] = ":allergie_$idx";
            }
            $in = implode(',', $placeholders);
            $sqlAllergies = "SELECT id_allergie FROM allergie WHERE type_allergie IN ($in)";
            $stmtAllergies = $this->conn->prepare($sqlAllergies);

            foreach ($allergies as $idx => $allergie) {
                $stmtAllergies->bindValue(":allergie_$idx", $allergie, PDO::PARAM_STR);
            }
            
            $stmtAllergies->execute();
            $ids = $stmtAllergies->fetchAll(PDO::FETCH_COLUMN);

            foreach ($ids as $id_allergie) {
                $sqlJointure = "INSERT INTO reservations_allergie (id_reservation_allergie, id_allergie_reservation) VALUES (:id_reservation_allergie, :id_allergie_reservation)";
                $stmtJointure = $this->conn->prepare($sqlJointure);
                $stmtJointure->bindValue(':id_reservation_allergie', $id_reservations, PDO::PARAM_INT);
                $stmtJointure->bindValue(':id_allergie_reservation', $id_allergie, PDO::PARAM_INT);
                $stmtJointure->execute();
            }
            
        }

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        // TEMP : affiche l’erreur (puis exit pour arrêter)
        echo '<pre>Erreur lors de la réservation : '.htmlspecialchars($e->getMessage()).'</pre>';
        exit();
    }
}


}

