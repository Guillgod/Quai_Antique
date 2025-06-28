<?php
require_once __DIR__ . '/../config/Database.php';

class ModelInfo {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupère toutes les infos (1 ligne par jour)
    public function getAllInfos() {
        $sql = "SELECT * FROM info ORDER BY id_day ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateInfos($infosArray) {
    foreach ($infosArray as $id_day => $data) {
        $sql = "UPDATE info
                SET opening_morning = :opening_morning,
                    closure_morning = :closure_morning,
                    opening_night = :opening_night,
                    closure_night = :closure_night,
                    nb_max_persons = :nb_max_persons
                WHERE id_day = :id_day";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':opening_morning', $data['opening_morning']);
        $stmt->bindValue(':closure_morning', $data['closure_morning']);
        $stmt->bindValue(':opening_night', $data['opening_night']);
        $stmt->bindValue(':closure_night', $data['closure_night']);
        $stmt->bindValue(':nb_max_persons', $data['nb_max_persons'], PDO::PARAM_INT);
        $stmt->bindValue(':id_day', $id_day, PDO::PARAM_INT);
        $stmt->execute();
    }
}
}