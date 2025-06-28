<?php
require_once '../models/ModelPlat.php';

class PlatController {
    public function getAllPlats() {
        $model = new ModelPlat();
        return $model->getAllPlats();
    }

    public function addPlat($data) {
        $model = new ModelPlat();
        return $model->addPlat(
            $data['titre'],
            $data['description'],
            $data['prix'],
            $data['categorie']
        );
    }

    public function updatePlat($data) {
    $model = new ModelPlat();
    return $model->updatePlat(
        $data['id_plats'],
        $data['titre'],
        $data['description'],
        $data['prix'],
        $data['categorie']
    );
    }
    public function deletePlat($id_plat) {
        $model = new ModelPlat();
        return $model->deletePlat($id_plat);
    }
}