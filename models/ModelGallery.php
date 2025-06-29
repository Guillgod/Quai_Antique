<?php
require_once __DIR__ . '/../config/Database.php';

class ModelGallery {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->conn->exec("SET NAMES 'binary'");
    }

    // Récupérer toutes les photos
    public function getAllPhotos() {
        $sql = "SELECT * FROM gallery ORDER BY id_gallery DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une photo par ID
    public function getPhotoById($id) {
    $sql = "SELECT * FROM gallery WHERE id_gallery = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Récupère la ligne sous forme de tableau associatif
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Recharge le BLOB comme flux
        $stmt = $this->conn->prepare("SELECT photo FROM gallery WHERE id_gallery = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->bindColumn(1, $lob, PDO::PARAM_LOB);
        $stmt->fetch(PDO::FETCH_BOUND);

        // Lis tout le flux (resource -> string)
        $row['photo'] = is_resource($lob) ? stream_get_contents($lob) : $lob;
    }
    return $row;
    }

    // Ajouter une nouvelle photo (titre, blob, mime_type)
    public function addPhoto($titre, $photo_blob, $mime_type) {
        $sql = "INSERT INTO gallery (titre, photo, mime_type) VALUES (:titre, :photo, :mime_type)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':photo', $photo_blob, PDO::PARAM_LOB);
        $stmt->bindValue(':mime_type', $mime_type);
        return $stmt->execute();
    }
    public function deletePhoto($id) {
    $sql = "DELETE FROM gallery WHERE id_gallery = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
    }

    public function updatePhotoTitle($id, $titre) {
        $sql = "UPDATE gallery SET titre = :titre WHERE id_gallery = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>