<?php
require_once '../controllers/GalleryController.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titres']) && is_array($_POST['titres'])) {
    $controller = new GalleryController();
    $ok = true;
    foreach ($_POST['titres'] as $id => $titre) {
        $id = (int)$id;
        $titre = trim($titre);
        if ($titre === '') continue; // ignore vides
        $res = $controller->updatePhotoTitle($id, $titre);
        if (!$res) $ok = false;
    }
    echo json_encode(['success' => $ok]);
    exit;
}
echo json_encode(['success' => false]);
exit;
?>
