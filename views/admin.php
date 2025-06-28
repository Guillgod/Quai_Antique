<!-- gestion des infos restaurant -->
<?php
require_once '../models/ModelInfo.php';
require_once '../controllers/InfoController.php';

$infoController = new InfoController();

// Si POST : on met à jour la table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_infos'])) {
    $infoController->updateInfos($_POST['infos'] ?? []);
    // Recharger les infos à jour
    $restaurantInfos = $infoController->getAllInfos();
    $success_message = "Modifications enregistrées avec succès !";
} else {
    $restaurantInfos = $infoController->getAllInfos();
}
?>
<!-- gestion des menus -->
<?php
require_once '../models/ModelMenu.php';
require_once '../controllers/MenuController.php';

$menuController = new MenuController();
// Si POST : on met à jour un menu
// MAJ groupée des menus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_menus'])) {
    if (isset($_POST['menus']) && is_array($_POST['menus'])) {
        foreach ($_POST['menus'] as $id_menu => $menuData) {
            $menuController->updateMenu([
                'id_menu' => $id_menu,
                'titre' => $menuData['titre'],
                'periode' => $menuData['periode'],
                'description' => $menuData['description'],
                'prix' => $menuData['prix']
            ]);
        }
        $menu_success = "Menus modifiés avec succès !";
    }
}
    $menus = $menuController->getAllMenus();


$menus = $menuController->getAllMenus();
$menu_success = "";
// Si POST : on ajoute un nouveau menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu'])) {
    $addResult = $menuController->addMenu([
        'titre' => $_POST['titre'] ?? '',
        'periode' => $_POST['periode'] ?? '',
        'description' => $_POST['description'] ?? '',
        'prix' => $_POST['prix'] ?? '',
    ]);
    if ($addResult) {
        $menu_success = "Menu ajouté avec succès !";
        $menus = $menuController->getAllMenus(); // Recharger la liste
    } else {
        $menu_success = "Erreur lors de l'ajout du menu.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/style.css" rel="stylesheet">
    </head>
    <body>
    
        <?php require_once '../views/header.php';?>
        
        
         
        <section class="container_creer_compte3">
            <div class="container_creer_compte-content3">
                <div class="tabs-container">
                    <button type="button" class="tab-btn active" id="tab-infos">Infos Restaurant</button>
                    <button type="button" class="tab-btn" id="tab-menus">Les Menus</button>
                    <button type="button" class="tab-btn" id="tab-galerie">La Galerie</button>
                    <button type="button" class="tab-btn" id="tab-reservations">Les Réservations</button>
                </div>
                <!-- infos restaurant -->
                <div id="content-infos">
                    <h2>Infos Restaurant</h2>
                    <p class="instructions">
                        Bonjour Chef ! Vous pouvez modifier les informations du restaurant.
                    </p>

                    <?php if (!empty($success_message)): ?>
                        <div style="color:green; text-align:center; margin-bottom:15px;">
                            <?= $success_message ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div id="horaires-error" style="color:red; text-align:center; margin-bottom:10px; display:none;"></div>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Jour</th>
                                    <th>Midi<br><small class="admin-th-small">Début - Fin</small></th>
                                    <th>Soir<br><small class="admin-th-small">Début - Fin</small></th>
                                    <th>Nb convives max</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($restaurantInfos as $info): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($info['day']) ?>
                                        <input type="hidden" name="infos[<?= $info['id_day'] ?>][id_day]" value="<?= $info['id_day'] ?>">
                                    </td>
                                    <td>
                                        <input type="time" name="infos[<?= $info['id_day'] ?>][opening_morning]" value="<?= htmlspecialchars($info['opening_morning']) ?>" required>
                                        -
                                        <input type="time" name="infos[<?= $info['id_day'] ?>][closure_morning]" value="<?= htmlspecialchars($info['closure_morning']) ?>" required>
                                    </td>
                                    <td>
                                        <input type="time" name="infos[<?= $info['id_day'] ?>][opening_night]" value="<?= htmlspecialchars($info['opening_night']) ?>" required>
                                        -
                                        <input type="time" name="infos[<?= $info['id_day'] ?>][closure_night]" value="<?= htmlspecialchars($info['closure_night']) ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="infos[<?= $info['id_day'] ?>][nb_max_persons]" value="<?= htmlspecialchars($info['nb_max_persons']) ?>" required style="width:70px;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="update_infos" class="submit-btn" style="margin-top:15px;">VALIDER</button>
                    </form>
                </div>

                <!-- Menus -->
                <div id="content-menus" style="display:none;">
                    <h2>Menus du restaurant</h2>
                    <?php if ($menu_success): ?>
                        <div style="color:green;text-align:center;"><?= $menu_success ?></div>
                    <?php endif; ?>
                    
                        <tbody>
                            <?php foreach ($menus as $menu): ?>
                            <tr>
                                <form method="post">
                                    <table class="admin-table" style="margin-bottom:20px;">
                                        <thead>
                                            <tr>
                                                <th>Titre</th>
                                                <th>Période</th>
                                                <th>Description</th>
                                                <th>Prix</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($menus as $menu): ?>
                                            <tr>
                                                <td>
                                                    <input type="text" name="menus[<?= $menu['id_menu'] ?>][titre]" value="<?= htmlspecialchars($menu['titre']) ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="menus[<?= $menu['id_menu'] ?>][periode]" value="<?= htmlspecialchars($menu['periode']) ?>" required>
                                                </td>
                                                <td>
                                                    <textarea name="menus[<?= $menu['id_menu'] ?>][description]" required><?= htmlspecialchars($menu['description']) ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="menus[<?= $menu['id_menu'] ?>][prix]" value="<?= htmlspecialchars($menu['prix']) ?>" required>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="update_menus" class="submit-btn" style="margin-top:15px; margin-bottom:70px;">VALIDER</button>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h2>Ajouter un nouveau menu</h2>
                    <form method="post">
                        <input type="text" name="titre" placeholder="Titre du menu" required>
                        <input type="text" name="periode" placeholder="Période (ex : Midi, Soir, Semaine...)" required>
                        <textarea name="description" placeholder="Description du menu" required></textarea>
                        <input type="number" step="0.01" min="0" name="prix" placeholder="Prix (€)" required>
                        <button type="submit" name="add_menu" class="submit-btn" style="margin-top:10px;">AJOUTER</button>
                    </form>
                </div>
                <!-- Gallerie (vide pour l'instant) -->
                <div id="content-galerie" style="display:none;">
                    <h2>Galerie à venir...</h2>
                </div>
                <!-- Réservations (vide pour l'instant) -->
                <div id="content-reservations" style="display:none;">
                    <h2>Réservations à venir...</h2>
                </div>
            </div>
        </section>



        <?php require_once 'footer.php';?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="post"]');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let error = '';
        const rows = form.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const day = row.querySelector('td:first-child').textContent.trim();
            // Le bon sélecteur pour chaque champ du jour
            const opening_morning = row.querySelector('input[name$="[opening_morning]"]').value;
            const closure_morning = row.querySelector('input[name$="[closure_morning]"]').value;
            const opening_night = row.querySelector('input[name$="[opening_night]"]').value;
            const closure_night = row.querySelector('input[name$="[closure_night]"]').value;

            // Validation midi
            if (opening_morning && closure_morning && opening_morning > closure_morning) {
                error = "L'horaire d'ouverture du midi doit être inférieur ou égal à l'horaire de fermeture (jour : " + day + ")";
            }
            // Validation soir
            if (opening_night && closure_night && opening_night > closure_night) {
                error = "L'horaire d'ouverture du soir doit être inférieur ou égal à l'horaire de fermeture (jour : " + day + ")";
            }
        });
        if (error) {
            e.preventDefault();
            const errorDiv = document.getElementById('horaires-error');
            errorDiv.textContent = error;
            errorDiv.style.display = "block";
            window.scrollTo({ top: errorDiv.offsetTop-100, behavior: 'smooth' });
            return false;
        }
    });
});
    </script>

    <!-- Gère l'interaction des onglets (voir tabs-container) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = [
            {btn: 'tab-infos', content: 'content-infos'},
            {btn: 'tab-menus', content: 'content-menus'},
            {btn: 'tab-galerie', content: 'content-galerie'},
            {btn: 'tab-reservations', content: 'content-reservations'},
        ];
        // Cacher tout sauf le 1er tab
        tabs.forEach((tab, i) => {
            const btn = document.getElementById(tab.btn);
            const content = document.getElementById(tab.content);
            if (btn && content) {
                if (i === 0) {
                    btn.classList.add('active');
                    content.style.display = 'block';
                } else {
                    btn.classList.remove('active');
                    content.style.display = 'none';
                }
                btn.addEventListener('click', function() {
                    tabs.forEach(t => {
                        const b = document.getElementById(t.btn);
                        const c = document.getElementById(t.content);
                        if (b) b.classList.remove('active');
                        if (c) c.style.display = 'none';
                    });
                    btn.classList.add('active');
                    content.style.display = 'block';
                });
            }
        });
    });
    </script>

    
    </body>
</html>