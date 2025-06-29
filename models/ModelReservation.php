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

    public function getReservationsByUserId($user_id) {
    $sql = "SELECT r.*, 
                (SELECT GROUP_CONCAT(a.type_allergie SEPARATOR ', ')
                    FROM reservations_allergie ra 
                    JOIN allergie a ON ra.id_allergie_reservation = a.id_allergie
                    WHERE ra.id_reservation_allergie = r.id_reservations
                ) as allergies
            FROM reservations r
            WHERE r.user_id = :user_id
            ORDER BY r.reservation_date DESC, r.reservation_heure DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function deleteReservationById($idResa, $user_id) {
    try {
        $this->conn->beginTransaction();

        // 1. Supprime d’abord les lignes dans la table de jointure
        $sqlDelJointure = "DELETE FROM reservations_allergie WHERE id_reservation_allergie = :resa_id";
        $stmtJointure = $this->conn->prepare($sqlDelJointure);
        $stmtJointure->bindValue(':resa_id', $idResa, PDO::PARAM_INT);
        $stmtJointure->execute();

        // 2. Puis la réservation
        $sqlDelResa = "DELETE FROM reservations WHERE id_reservations = :id AND user_id = :user_id";
        $stmtResa = $this->conn->prepare($sqlDelResa);
        $stmtResa->bindValue(':id', $idResa, PDO::PARAM_INT);
        $stmtResa->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmtResa->execute();

        $this->conn->commit();
    } catch (Exception $e) {
        $this->conn->rollBack();
        throw $e;
    }
}

public function getReservationById($idResa, $user_id) {
    $sql = "SELECT r.*, 
                (SELECT GROUP_CONCAT(a.type_allergie SEPARATOR ',')
                 FROM reservations_allergie ra 
                 JOIN allergie a ON ra.id_allergie_reservation = a.id_allergie
                 WHERE ra.id_reservation_allergie = r.id_reservations
                ) as allergies
            FROM reservations r
            WHERE r.id_reservations = :id AND r.user_id = :user_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', $idResa, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $resa = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resa && $resa['allergies']) {
        $resa['allergies'] = explode(',', $resa['allergies']);
    } else {
        $resa['allergies'] = [];
    }
    return $resa;
}

public function updateReservationById($idResa, $date, $heure, $couvert, $allergies, $user_id) {
    try {
        $this->conn->beginTransaction();
        $sql = "UPDATE reservations SET reservation_date=:date, reservation_heure=:heure, couvert=:couvert
                WHERE id_reservations=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':heure', $heure);
        $stmt->bindParam(':couvert', $couvert);
        $stmt->bindParam(':id', $idResa);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Supprimer puis remettre les allergies (plus propre)
        $sqlDel = "DELETE FROM reservations_allergie WHERE id_reservation_allergie = :id";
        $stmtDel = $this->conn->prepare($sqlDel);
        $stmtDel->bindValue(':id', $idResa, PDO::PARAM_INT);
        $stmtDel->execute();

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
                $stmtJointure->bindValue(':id_reservation_allergie', $idResa, PDO::PARAM_INT);
                $stmtJointure->bindValue(':id_allergie_reservation', $id_allergie, PDO::PARAM_INT);
                $stmtJointure->execute();
            }
        }
        $this->conn->commit();
    } catch (Exception $e) {
        $this->conn->rollBack();
        throw $e;
    }
}

    public function getReservationsByDateWithUser($date) {
        $sql = "SELECT r.*, u.nom, u.prenom, u.tel,
                       (SELECT GROUP_CONCAT(a.type_allergie SEPARATOR ', ')
                            FROM reservations_allergie ra
                            JOIN allergie a ON ra.id_allergie_reservation = a.id_allergie
                            WHERE ra.id_reservation_allergie = r.id_reservations
                       ) as allergies
                FROM reservations r
                JOIN users u ON u.id_users = r.user_id
                WHERE r.reservation_date = :date
                ORDER BY r.reservation_heure ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Récupération d'une réservation pour l'admin, sans vérifier le user_id
        public function getReservationByIdAdmin($idResa) {
            $sql = "SELECT r.*, 
                        (SELECT GROUP_CONCAT(a.type_allergie SEPARATOR ',')
                        FROM reservations_allergie ra 
                        JOIN allergie a ON ra.id_allergie_reservation = a.id_allergie
                        WHERE ra.id_reservation_allergie = r.id_reservations
                        ) as allergies
                    FROM reservations r
                    WHERE r.id_reservations = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $idResa, PDO::PARAM_INT);
            $stmt->execute();
            $resa = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resa && $resa['allergies']) {
                $resa['allergies'] = explode(',', $resa['allergies']);
            } else {
                $resa['allergies'] = [];
            }
            return $resa;
        }


    // Ajoute cette méthode pour la suppression d'une réservation par l'admin
    public function deleteReservationByIdAdmin($idResa) {
        try {
            $this->conn->beginTransaction();
            $this->conn->prepare("DELETE FROM reservations_allergie WHERE id_reservation_allergie = ?")->execute([$idResa]);
            $this->conn->prepare("DELETE FROM reservations WHERE id_reservations = ?")->execute([$idResa]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Mise à jour d'une réservation sans contrôle user_id (admin)
    public function updateReservationByIdAdmin($idResa, $date, $heure, $couvert, $allergies) {
        try {
            $this->conn->beginTransaction();
            $sql = "UPDATE reservations SET reservation_date=:date, reservation_heure=:heure, couvert=:couvert
                    WHERE id_reservations=:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':heure', $heure);
            $stmt->bindParam(':couvert', $couvert);
            $stmt->bindParam(':id', $idResa);
            $stmt->execute();

            // Allergies (déjà OK)
            $sqlDel = "DELETE FROM reservations_allergie WHERE id_reservation_allergie = :id";
            $stmtDel = $this->conn->prepare($sqlDel);
            $stmtDel->bindValue(':id', $idResa, PDO::PARAM_INT);
            $stmtDel->execute();

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
                    $stmtJointure->bindValue(':id_reservation_allergie', $idResa, PDO::PARAM_INT);
                    $stmtJointure->bindValue(':id_allergie_reservation', $id_allergie, PDO::PARAM_INT);
                    $stmtJointure->execute();
                }
            }
            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }


}

