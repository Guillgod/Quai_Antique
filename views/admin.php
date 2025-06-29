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

// Gestion de l'affichage de tous les plats : 
    require_once '../models/ModelPlat.php';
    require_once '../controllers/PlatController.php';

    $platController = new PlatController();
    $plats = $platController->getAllPlats();



// Si POST : on met à jour un menu
// MAJ groupée des menus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_menus'])) {
    if (isset($_POST['menus']) && is_array($_POST['menus'])) {
        foreach ($_POST['menus'] as $id_menu => $menuData) {
            // Vérifie si tous les champs sont vides (trim pour éviter espaces)
            $allEmpty = 
                trim($menuData['titre']) === '' &&
                trim($menuData['periode']) === '' &&
                trim($menuData['description']) === '' &&
                (trim($menuData['prix']) === '' || $menuData['prix'] === null);

            if ($allEmpty) {
                // Supprime le menu concerné
                $menuController->deleteMenu($id_menu);
            } else {
                // Sinon on met à jour normalement
                $menuController->updateMenu([
                    'id_menu' => $id_menu,
                    'titre' => $menuData['titre'],
                    'periode' => $menuData['periode'],
                    'description' => $menuData['description'],
                    'prix' => $menuData['prix']
                ]);
            }
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

// Gestion de l'ajout d'un plat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plat'])) {
    $platData = [
        'titre' => $_POST['plat_titre'] ?? '',
        'description' => $_POST['plat_description'] ?? '',
        'prix' => $_POST['plat_prix'] ?? '',
        'categorie' => $_POST['plat_categorie'] ?? '',
    ];
    $platController->addPlat($platData);
    // Recharger la liste des plats après ajout
    $plats = $platController->getAllPlats();
}

// Gestion de la mise à jour des plats et de la suppression d'un plat : 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plats'])) {
    if (isset($_POST['plats']) && is_array($_POST['plats'])) {
        foreach ($_POST['plats'] as $id_plat => $platData) {
            // Vérifie si tous les champs sont vides
            $allEmpty = 
                trim($platData['titre']) === '' &&
                trim($platData['description']) === '' &&
                (trim($platData['prix']) === '' || $platData['prix'] === null);

            if ($allEmpty) {
                $platController->deletePlat($id_plat);
            } else {
                $platController->updatePlat([
                    'id_plats' => $id_plat,
                    'titre' => $platData['titre'],
                    'description' => $platData['description'],
                    'prix' => $platData['prix'],
                    'categorie' => $platData['categorie']
                ]);
            }
        }
        // Recharge la liste après modification
        $plats = $platController->getAllPlats();
        $plats_success = "Plats modifiés avec succès !";
    }
}

// Gestion de la galerie (vide pour l'instant) sous l'onglet admin.php : 
require_once '../models/ModelGallery.php';
require_once '../controllers/GalleryController.php';

$galleryController = new GalleryController();

// Ajout d'une photo via le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo_gallery'])) {
    $titre = $_POST['gallery_titre'] ?? '';
    $photo_blob = null;
    $mime_type = null;
    if (!empty($_FILES['gallery_photo']['tmp_name'])) {
        $photo_blob = file_get_contents($_FILES['gallery_photo']['tmp_name']);
        $mime_type = mime_content_type($_FILES['gallery_photo']['tmp_name']); // <--- récupération du type MIME
    }
    if ($titre && $photo_blob && $mime_type) {
        $galleryController->addPhoto($titre, $photo_blob, $mime_type); // Passe le type mime !
        $gallery_success = "Photo ajoutée avec succès !";
    } else {
        $gallery_success = "Veuillez remplir tous les champs.";
    }
}
// Récupérer toutes les photos pour affichage
$gallery_photos = $galleryController->getAllPhotos();



// Gestion de la mise à jour des réservations depuis la page admin.php

    // Gestion suppression pour l'admin 
    require_once '../models/ModelReservation.php';
    require_once '../models/Model_user.php';
    $modelReservation = new ModelReservation();
    $dateFiltre = $_GET['reservation_date'] ?? date('Y-m-d');

    // 1. Pour afficher la liste (remplace tout ce qui touche au prepare/execute direct)
    $resaList = $modelReservation->getReservationsByDateWithUser($dateFiltre);

    // 2. Pour la suppression admin (remplace l’appel direct à $modelReservation->conn)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_resa_admin'])) {
        $idResa = (int)$_POST['delete_resa_admin'];
        try {
            $modelReservation->deleteReservationByIdAdmin($idResa);
            header('Location: admin.php?reservation_date=' . urlencode($_POST['reservation_date']));
            exit;
        } catch (Exception $e) {
            echo '<div style="color:red;text-align:center;">Erreur suppression : ' . htmlspecialchars($e->getMessage()) . '</div>';
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
                    <button type="button" class="tab-btn" id="tab-plats">La Carte</button>
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
                                                    <input type="text" name="menus[<?= $menu['id_menu'] ?>][titre]" value="<?= htmlspecialchars($menu['titre']) ?>" >
                                                </td>
                                                <td>
                                                    <input type="text" name="menus[<?= $menu['id_menu'] ?>][periode]" value="<?= htmlspecialchars($menu['periode']) ?>" >
                                                </td>
                                                <td>
                                                    <textarea name="menus[<?= $menu['id_menu'] ?>][description]" required><?= htmlspecialchars($menu['description']) ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="menus[<?= $menu['id_menu'] ?>][prix]" value="<?= htmlspecialchars($menu['prix']) ?>" >
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="update_menus" class="submit-btn" style="margin-top:15px; margin-bottom:70px;">VALIDER</button>
                                </form>
                            </tr>
                            
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
                <div id="content-carte" style="display:none;">
                     <!-- Les Plats -->
                    <h2 style="margin-top:40px;color:#bf902b;">Les plats de la carte</h2>
                     
                <?php if (!empty($plats_success)): ?>
                    <div style="color:green;text-align:center;"><?= $plats_success ?></div>
                <?php endif; ?>

                <form method="post" style="margin-bottom:32px;">
                <?php
                // Toujours la même logique de regroupement par catégorie
                $categories = [
                    'Entrée' => [],
                    'Plat' => [],
                    'Dessert' => [],
                ];
                foreach ($plats as $plat) {
                    $cat = ucfirst(strtolower($plat['categorie']));
                    if (!in_array($cat, ['Entrée', 'Plat', 'Dessert'])) {
                        $cat = 'Autres';
                        if (!isset($categories[$cat])) $categories[$cat] = [];
                    }
                    $categories[$cat][] = $plat;
                }
                foreach ($categories as $cat => $platsCat) {
                    if (count($platsCat) === 0) continue;
                    echo "<h2 style='margin-top:32px;'>LES " . strtoupper($cat) . "S</h2>";
                    foreach ($platsCat as $plat) {
                        echo "<div style='margin-bottom:18px;'>";
                        echo '<input type="text" name="plats['.$plat['id_plats'].'][titre]" value="'.htmlspecialchars($plat['titre']).'" placeholder="Titre" > ';
                        echo '<textarea name="plats['.$plat['id_plats'].'][description]" >'.htmlspecialchars($plat['description']).'</textarea> ';
                        echo '<input type="number" step="0.01" min="0" name="plats['.$plat['id_plats'].'][prix]" value="'.htmlspecialchars($plat['prix']).'" placeholder="Prix (€)" > ';
                        echo '<select name="plats['.$plat['id_plats'].'][categorie]" required>';
                        foreach (['Entrée', 'Plat', 'Dessert'] as $option) {
                            $selected = ($plat['categorie'] == $option) ? 'selected' : '';
                            echo '<option value="'.$option.'" '.$selected.'>'.$option.'</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                    }
                }
                ?>
                    <button type="submit" name="update_plats" class="submit-btn" style="margin-top:15px;">ENREGISTRER</button>
                </form>
                    <h2 style="margin-top:40px;">Ajouter un nouveau plat</h2>
                    <form method="post" style="margin-bottom: 32px;">
                        <input type="text" name="plat_titre" placeholder="Titre du plat" required>
                        <textarea name="plat_description" placeholder="Description du plat" required></textarea>
                        <input type="number" step="0.01" min="0" name="plat_prix" placeholder="Prix (€)" required>
                        <select name="plat_categorie" required>
                            <option value="">Catégorie</option>
                            <option value="Entrée">Entrée</option>
                            <option value="Plat">Plat</option>
                            <option value="Dessert">Dessert</option>
                        </select>
                        <button type="submit" name="add_plat" class="submit-btn" style="margin-top:10px;">AJOUTER</button>
                    </form>
                </div>

                

                <!-- Galerie du restaurant -->
                <div id="content-galerie" style="display:none;">
                    <h2>Galerie du restaurant</h2>
                    <?php if (!empty($gallery_success)): ?>
                        <div style="color:green;text-align:center;"><?= $gallery_success ?></div>
                    <?php endif; ?>

                    <!-- Formulaire global de mise à jour des titres -->
                    <form id="gallery-title-form">
                        <div class="creation-gallery2" style="margin-bottom:0px;">
                            <?php
                            if (!empty($gallery_photos)) {
                                foreach ($gallery_photos as $photo) {
                                    echo '<div>';
                                    echo '<div class="creation-img-wrapper">';
                                    // Bouton suppression d'image
                                    echo '<button class="delete-photo-btn" data-id="'.$photo['id_gallery'].'" title="Supprimer cette photo"></button>';
                                    echo '<img src="display_gallery_photo.php?id=' . $photo['id_gallery'] . '" alt="' . htmlspecialchars($photo['titre']) . '" class="creation-image">';
                                    echo '<div class="creation-alt"></div>';
                                    echo '</div>'; // fin de wrapper

                                    // Champ titre éditable
                                    echo '<input type="text" name="titres['.$photo['id_gallery'].']" value="'.htmlspecialchars($photo['titre']).'" style="font-weight:bold;font-size:1.09em;color:#2b2b2b;padding:2px 8px;border-radius:4px;border:1px solid #ccc;width:350px;margin:8px auto 0;display:block;text-align:center;">';

                                    echo '</div>';
                                }
                            } else {
                                echo '<div style="text-align:center;color:#888;font-size:1.1em;">Aucune photo en galerie pour l’instant.</div>';
                            }
                            ?>
                        </div>

                        <!-- Le bouton ENREGISTRER global -->
                        <div style="text-align:center;margin-top:0px;">
                            <button type="submit" class="submit-btn" style="padding:9px 35px;font-size:1.08em;">ENREGISTRER</button>
                            <span id="gallery-titles-feedback" style="margin-left:14px;font-size:1em;"></span>
                        </div>
                    </form>

                    <h2 style="margin-top:32px;">Ajouter une photo à la galerie</h2>
                    <form method="post" enctype="multipart/form-data" class="form-gallery-add" style="margin-bottom: 32px; max-width:440px;">
                        <input type="text" name="gallery_titre" placeholder="Titre de la photo" style="margin-top:30px" required>
                        <input type="file" name="gallery_photo" accept="image/*" required>
                        <button type="submit" name="add_photo_gallery" class="submit-btn">AJOUTER</button>
                    </form>
                </div>
                
                <!-- Onglet LES RÉSERVATIONS -->
                <div id="content-reservations" style="display:none;">
                    <h2>LES RÉSERVATIONS</h2>
                    <form method="get" action="admin.php" style="margin-bottom:18px; width:100%; max-width:380px; margin:0 auto;" class="form-gallery-add">
                        <label for="resa-date" style="margin-bottom:4px;">Sélectionnez une date :</label>
                        <input type="date" id="resa-date" name="reservation_date" value="<?= htmlspecialchars($_GET['reservation_date'] ?? date('Y-m-d')) ?>">
                        <button type="submit" class="submit-btn">VOIR</button>
                    </form>
                    <?php
                    require_once '../models/ModelReservation.php';
                    require_once '../models/Model_user.php';
                    $modelReservation = new ModelReservation();
                    $dateFiltre = $_GET['reservation_date'] ?? date('Y-m-d');
                    $resaList = $modelReservation->getReservationsByDateWithUser($dateFiltre);
                    ?>
                    <?php if (empty($resaList)): ?>
                        <p style="margin-top:14px;color:#999;text-align:center;">Aucune réservation ce jour-là.</p>
                    <?php else: ?>
                        <table class="reservations-table" style="margin-top:18px;">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Téléphone</th>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Couvert(s)</th>
                                    <th>Allergie(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resaList as $resa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($resa['nom']) ?></td>
                                    <td><?= htmlspecialchars($resa['prenom']) ?></td>
                                    <td><?= htmlspecialchars($resa['tel']) ?></td>
                                    <td>
                                        <?php
                                        $d = DateTime::createFromFormat('Y-m-d', $resa['reservation_date']);
                                        echo $d ? $d->format('d-m-Y') : htmlspecialchars($resa['reservation_date']);
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars(substr($resa['reservation_heure'], 0, 5)) ?></td>
                                    <td><?= htmlspecialchars($resa['couvert']) ?></td>
                                    <td><?= htmlspecialchars($resa['allergies'] ?: 'Aucune') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="7" style="padding:0;background:transparent;">
                                        <div class="resa-actions">
                                            <!-- Modifier -->
                                            <form method="get" action="reservation.php" style="display:inline;">
                                                <input type="hidden" name="edit_resa" value="<?= $resa['id_reservations'] ?>">
                                                <button type="submit" class="btn-edit">Modifier</button>
                                                <input type="hidden" name="from_admin" value="1">
                                            </form>
                                            <!-- Supprimer (en tant qu'admin : pas besoin d'user_id) -->
                                            <form method="post" action="admin.php" style="display:inline;" onsubmit="return confirm('Supprimer cette réservation ?');">
                                                <input type="hidden" name="delete_resa_admin" value="<?= $resa['id_reservations'] ?>">
                                                <input type="hidden" name="reservation_date" value="<?= htmlspecialchars($dateFiltre) ?>">
                                                <button type="submit" class="btn-delete">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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
            {btn: 'tab-plats', content: 'content-carte'}, // Utilise le même contenu que les menus
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
    <!-- Suppression d'une image dans Gallerie photo -->
    <script>
            document.querySelectorAll('.creation-img-wrapper').forEach(function(wrapper) {
                var img = wrapper.querySelector('img');
                var altText = img.getAttribute('alt');
                var altDiv = wrapper.querySelector('.creation-alt');
                altDiv.textContent = altText;
            });
            </script>

            <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-photo-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm("Confirmer la suppression de cette photo ?")) return;
                var id = this.getAttribute('data-id');
                var wrapper = this.closest('.creation-img-wrapper');
                fetch('delete_gallery_photo.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Supprime visuellement la photo
                        wrapper.parentNode.remove(); // ou wrapper.remove() selon ta structure
                    } else {
                        alert("Erreur lors de la suppression.");
                    }
                });
            });
        });
    });
    </script>

<!-- Gère la modification des titres des photos en live -->
    <script>
    document.getElementById('gallery-title-form').addEventListener('submit', function(e){
        e.preventDefault();
        var form = this;
        var feedback = document.getElementById('gallery-titles-feedback');
        var formData = new FormData(form);

        fetch('update_gallery_titles.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                feedback.textContent = "Tous les titres ont été enregistrés !";
                feedback.style.color = "green";
                setTimeout(() => { feedback.textContent = ""; }, 2000);
            } else {
                feedback.textContent = "Erreur lors de la sauvegarde !";
                feedback.style.color = "red";
            }
        })
        .catch(() => {
            feedback.textContent = "Erreur technique !";
            feedback.style.color = "red";
        });
    });
    </script>
    </body>
</html>