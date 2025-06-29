<?php
require_once '../controllers/MenuController.php';
require_once '../controllers/PlatController.php';

$menuController = new MenuController();
$platController = new PlatController();

$menus = $menuController->getAllMenus();
$plats = $platController->getAllPlats();

// Regrouper les plats par catégorie
$categories = [
    'Entrée' => [],
    'Plat' => [],
    'Dessert' => [],
    'Autre' => []
];
foreach ($plats as $plat) {
    $cat = ucfirst(strtolower($plat['categorie']));
    if (!in_array($cat, ['Entrée', 'Plat', 'Dessert'])) {
        $cat = 'Autre';
    }
    $categories[$cat][] = $plat;
}
?>


<!DOCTYPE <!DOCTYPE html>
<html lang="fr">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once 'header.php';?>
        <section class="fond2">

        <H1>NOTRE CARTE</H1>
        <p>Retrouvez ici nos formules ainsi que tous nos plats, cuisinés avec amour et passion.</p>
        </section>
        
        <section class="carte-main">
            <div class="carte-bloc">
                <h2 class="carte-titre">NOS MENUS</h2>
                <?php if (!empty($menus)): ?>
                    <?php foreach ($menus as $menu): ?>
                        <div class="carte-item">
                            <div class="carte-item-titre"><?= strtoupper(htmlspecialchars($menu['titre'])) ?></div>
                            <div class="carte-item-periode"><?= htmlspecialchars($menu['periode']) ?></div>
                            <div class="carte-item-desc"><?= nl2br(htmlspecialchars($menu['description'])) ?></div>
                            <div class="carte-item-prix"><?= number_format($menu['prix'], 2, ',', ' ') ?> €</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="carte-vide">Aucun menu pour l’instant.</p>
                <?php endif; ?>
            </div>

            <div class="carte-bloc">
                <h2 class="carte-titre">NOS ENTRÉES</h2>
                <?php if (!empty($categories['Entrée'])): ?>
                    <?php foreach ($categories['Entrée'] as $plat): ?>
                        <div class="carte-item">
                            <div class="carte-item-titre"><?= strtoupper(htmlspecialchars($plat['titre'])) ?></div>
                            <div class="carte-item-desc"><?= nl2br(htmlspecialchars($plat['description'])) ?></div>
                            <div class="carte-item-prix"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="carte-vide">Aucune entrée pour l’instant.</p>
                <?php endif; ?>
            </div>

            <div class="carte-bloc">
                <h2 class="carte-titre">NOS PLATS</h2>
                <?php if (!empty($categories['Plat'])): ?>
                    <?php foreach ($categories['Plat'] as $plat): ?>
                        <div class="carte-item">
                            <div class="carte-item-titre"><?= strtoupper(htmlspecialchars($plat['titre'])) ?></div>
                            <div class="carte-item-desc"><?= nl2br(htmlspecialchars($plat['description'])) ?></div>
                            <div class="carte-item-prix"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="carte-vide">Aucun plat principal pour l’instant.</p>
                <?php endif; ?>
            </div>

            <div class="carte-bloc">
                <h2 class="carte-titre">NOS DESSERTS</h2>
                <?php if (!empty($categories['Dessert'])): ?>
                    <?php foreach ($categories['Dessert'] as $plat): ?>
                        <div class="carte-item">
                            <div class="carte-item-titre"><?= strtoupper(htmlspecialchars($plat['titre'])) ?></div>
                            <div class="carte-item-desc"><?= nl2br(htmlspecialchars($plat['description'])) ?></div>
                            <div class="carte-item-prix"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="carte-vide">Aucun dessert pour l’instant.</p>
                <?php endif; ?>
            </div>
        </section>





        <?php require_once 'footer.php';?>

        
    </body>
</html>