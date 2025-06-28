<?php
require_once '../models/ModelInfo.php';

class InfoController {
    public function getAllInfos() {
        $model = new ModelInfo();
        return $model->getAllInfos();
    }

    public function updateInfos($infosArray) {
    $model = new ModelInfo();
    $model->updateInfos($infosArray);
}
    // Tu peux ajouter d'autres méthodes pour modification/suppression etc.
}
