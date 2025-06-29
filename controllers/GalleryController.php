<?php
require_once '../models/ModelGallery.php';

class GalleryController {
    public function getAllPhotos() {
        $model = new ModelGallery();
        return $model->getAllPhotos();
    }

    public function addPhoto($titre, $photo_blob, $mime_type) {
        $model = new ModelGallery();
        return $model->addPhoto($titre, $photo_blob, $mime_type);
    }

    public function getPhotoById($id) {
        $model = new ModelGallery();
        return $model->getPhotoById($id);
    }

    public function deletePhoto($id) {
    $model = new ModelGallery();
    return $model->deletePhoto($id);
}
}
?>