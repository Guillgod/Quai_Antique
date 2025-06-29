<?php
require_once '../controllers/GalleryController.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $controller = new GalleryController();
    $result = $controller->deletePhoto($id);
    echo json_encode(['success' => $result]);
    exit;
}
echo json_encode(['success' => false]);
exit;
