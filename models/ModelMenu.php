<?php
require_once __DIR__ . '/../config/Database.php';

class ModelMenu {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllMenus() {
        $sql = "SELECT * FROM menu ORDER BY id_menu DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMenu($titre, $periode, $description, $prix) {
        $sql = "INSERT INTO menu (titre, periode, description, prix) VALUES (:titre, :periode, :description, :prix)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':periode', $periode);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':prix', $prix);
        return $stmt->execute();
    }

    public function updateMenu($id_menu, $titre, $periode, $description, $prix) {
    $sql = "UPDATE menu SET titre = :titre, periode = :periode, description = :description, prix = :prix WHERE id_menu = :id_menu";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':titre', $titre);
    $stmt->bindValue(':periode', $periode);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':prix', $prix);
    $stmt->bindValue(':id_menu', $id_menu, PDO::PARAM_INT);
    return $stmt->execute();
}

public function deleteMenu($id_menu) {
    $sql = "DELETE FROM menu WHERE id_menu = :id_menu";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id_menu', $id_menu, PDO::PARAM_INT);
    return $stmt->execute();
}
}