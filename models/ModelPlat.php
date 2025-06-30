<?php
require_once __DIR__ . '/../config/Database.php';

class ModelPlat {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupère tous les plats, classés par catégorie puis par titre
    public function getAllPlats() {
        $sql = "SELECT * FROM plats ORDER BY 
                  CASE 
                    WHEN categorie = 'Entrée' THEN 1
                    WHEN categorie = 'Plat' THEN 2
                    WHEN categorie = 'Dessert' THEN 3
                    ELSE 4
                  END,
                  titre DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function addPlat($titre, $description, $prix, $categorie) {
        $sql = "INSERT INTO plats (titre, description, prix, categorie) VALUES (:titre, :description, :prix, :categorie)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':prix', $prix);
        $stmt->bindValue(':categorie', $categorie);
        return $stmt->execute();
    }

    public function updatePlat($id_plats, $titre, $description, $prix, $categorie) {
    $sql = "UPDATE plats SET titre = :titre, description = :description, prix = :prix, categorie = :categorie WHERE id_plats = :id_plats";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':titre', $titre);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':prix', $prix);
    $stmt->bindValue(':categorie', $categorie);
    $stmt->bindValue(':id_plats', $id_plats, PDO::PARAM_INT);
    return $stmt->execute();
    }
    public function deletePlat($id_plats) {
        $sql = "DELETE FROM plats WHERE id_plats = :id_plats";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_plats', $id_plats, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
