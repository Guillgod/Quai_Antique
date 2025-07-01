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
        $sql = "SELECT * FROM gallery ORDER BY id_gallery ASC";
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

    function resizeImageToBlob($filePath, $maxWidth = 1920, $maxHeight = 1080, &$mimeType = null) {
    $info = getimagesize($filePath);
    if (!$info) return false;
    $srcMime = $info['mime'];
    $mimeType = $srcMime;
    list($srcWidth, $srcHeight) = $info;

    // HARD LIMIT dimensions (sauvegarde serveur)
    if ($srcWidth > 9000 || $srcHeight > 9000) {
        return false;
    }
    // Si l'image est déjà assez petite, on la renvoie brute
    if ($srcWidth <= $maxWidth && $srcHeight <= $maxHeight) {
        return file_get_contents($filePath);
    }

    // Calcul du ratio de redimensionnement
    $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);
    $dstWidth = (int)($srcWidth * $ratio);
    $dstHeight = (int)($srcHeight * $ratio);

    // Création de l'image source selon son type
    switch ($srcMime) {
        case 'image/jpeg': $srcImage = imagecreatefromjpeg($filePath); break;
        case 'image/png':  $srcImage = imagecreatefrompng($filePath); break;
        case 'image/gif':  $srcImage = imagecreatefromgif($filePath); break;
        default: return false; // Format non géré
    }

    // Nouvelle image
    $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);

    // Si PNG, conserve la transparence
    if ($srcMime === 'image/png') {
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
    }

    // Redimensionne
    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

    // Sauvegarde dans une variable (BLOB)
    ob_start();
    if ($srcMime === 'image/png') {
        imagepng($dstImage);
        $mimeType = 'image/png';
    } else {
        imagejpeg($dstImage, null, 85); // Qualité 85%
        $mimeType = 'image/jpeg';
    }
    $blob = ob_get_clean();

    // Libération mémoire
    imagedestroy($srcImage);
    imagedestroy($dstImage);

    return $blob;
    }
}

?>