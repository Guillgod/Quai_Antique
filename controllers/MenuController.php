<?php
require_once '../models/ModelMenu.php';

class MenuController {
    public function getAllMenus() {
        $model = new ModelMenu();
        return $model->getAllMenus();
    }
    public function addMenu($data) {
        $model = new ModelMenu();
        return $model->addMenu(
            $data['titre'],
            $data['periode'],
            $data['description'],
            $data['prix']
        );
    }

    public function updateMenu($data) {
    $model = new ModelMenu();
    return $model->updateMenu(
        $data['id_menu'],
        $data['titre'],
        $data['periode'],
        $data['description'],
        $data['prix']
    );
}
}