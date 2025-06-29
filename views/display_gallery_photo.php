<?php
require_once '../models/ModelGallery.php';
if (!isset($_GET['id'])) {
    http_response_code(404); exit;
}
$id = (int)$_GET['id'];
$model = new ModelGallery();
$photo = $model->getPhotoById($id);

if ($photo && !empty($photo['photo'])) {
    header('Content-Type: ' . $photo['mime_type']);
    // Pas de strlen ni d'autres headers inutiles
    echo $photo['photo'];
    exit;
}
http_response_code(404);
