
<?php
session_start();
require_once '../controllers/UserController.php';
require_once '../models/Model_user.php';

$message = '';
$user_id = $_SESSION['user']['id_users'] ?? null;

require_once '../models/ModelReservation.php';
$modelReservation = new ModelReservation();
$userReservations = $modelReservation->getReservationsByUserId($user_id);

if (!$user_id) {
    header('Location: login.php');
    exit();
}

$modelUser = new ModelUser();
$userData = $modelUser->getUserById($user_id);
$userAllergies = $modelUser->getAllergiesByUserId($user_id);

// Gère la suppression ou la modification d'une réservation OU la modification utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suppression d'une réservation
    if (isset($_POST['delete_resa'])) {
        $idResa = (int)$_POST['delete_resa'];
        $modelReservation->deleteReservationById($idResa, $user_id);
        // Recharge les résas après suppression
        $userReservations = $modelReservation->getReservationsByUserId($user_id);
    }
    // Modification d'utilisateur (uniquement si les champs existent)
    elseif (
        isset($_POST['prenom'], $_POST['nom'], $_POST['email'], $_POST['nb_persons'])
    ) {
        $controller = new UserController();
        $message = $controller->updateUserInfos($_POST, $user_id, $userData['password']);
        // Recharge les infos après modif
        $userData = $modelUser->getUserById($user_id);
        $userAllergies = $modelUser->getAllergiesByUserId($user_id);
    }
}

// Suppression du compte utilisateur
if (isset($_POST['delete_account'])) {
    $modelUser->deleteUser($user_id);
    // Détruit la session, déconnecte et redirige vers la page d'accueil (ou login)
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit();
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
        
        
        <section class="container_creer_compte">
            

            <div class="container_creer_compte-content">
                <div class="tabs-container">
                    <button type="button" class="tab-btn active" id="tab-infos">Vos informations</button>
                    <button type="button" class="tab-btn" id="tab-reservations">Vos réservations</button>
                </div>
                <div id="content-infos">
                    <h2>Vos informations</h2>
                    <p class="instructions">
                    Vous pouvez modifier vos informations personnelles ou consulter vos réservations. 
                    </p>
                    <form action="user_space.php" method="post" autocomplete="off">
                        <input type="text" id="nom" name="nom" placeholder="Nom de famille" value="<?= htmlspecialchars($userData['nom'] ?? '') ?>" required>
                        <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?= htmlspecialchars($userData['prenom'] ?? '') ?>" required>
                        <input type="email" id="email" name="email" placeholder="E-mail" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
                        <input class="input-petit" type="password" id="mdp" name="mdp" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)" autocomplete="new-password">
                        <input class="input-petit" type="password" id="mdp2" name="mdp2" placeholder="Confirmer le nouveau mot de passe (laisser vide pour ne pas changer)" autocomplete="new-password">
                        <input type="tel" id="tel" name="tel" placeholder="Téléphone" pattern="[0-9]{10}" value="<?= htmlspecialchars($userData['tel'] ?? '') ?>">
                        <small>Format : 0102030405</small>

                        <label for="nb_persons">Nombres de couverts par défaut</label>
                        <select id="nb_persons" name="nb_persons">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= ($i == ($userData['nb_persons'] ?? 2)) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>

                        <label class="label-radio">Allergies notifiées ?</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="allergie" value="oui" <?= !empty($userAllergies) ? 'checked' : '' ?>> Oui
                            </label>
                            <label>
                                <input type="radio" name="allergie" value="non" <?= empty($userAllergies) ? 'checked' : '' ?>> Non
                            </label>
                        </div>
                        <div class="liste-allergies" id="liste-allergies" style="margin-top:10px;<?= !empty($userAllergies) ? '' : 'display:none;' ?>">
                            <?php
                            $allergyList = [
                                "Arachide", "Céleri", "Crustacés", "Fruits à coques", "Gluten", "Lactose",
                                "Lupin", "Mollusque", "Moutarde", "Oeufs", "Poissons", "Sésame", "Soja", "Sulfites"
                            ];
                            foreach ($allergyList as $allergy) {
                                $checked = in_array($allergy, $userAllergies) ? 'checked' : '';
                                echo "<label><input type=\"checkbox\" name=\"allergies[]\" value=\"$allergy\" $checked> $allergy</label><br>";
                            }
                            ?>
                        </div>
                        
                        <?php if ($message): ?>
                            <div style="color:red;text-align:center;margin-bottom:10px;"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <div class="resa-actions">
                            <button type="submit" class="submit-btn">MODIFIER</button>
                            <button type="submit" class="submit-btn2" name="delete_account" onclick="return confirm('Voulez-vous vraiment supprimer votre compte ? Cette action est irréversible.');" style="margin-left: 10px;">SUPPRIMER</button>
                        </div>
                    </form>
                </div>

                <div id="content-reservations" style="display:none;">
                    <h2>Vos réservations</h2>
                    <?php if (empty($userReservations)): ?>
                        <p>Aucune réservation effectuée.</p>
                    <?php else: ?>
                        <table class="reservations-table" style="border-collapse:collapse;margin-top:15px;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Couvert(s)</th>
                                    <th>Allergie(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userReservations as $resa): ?>
                                    <tr>
                                        <td>
                                            <?php
                                                $d = DateTime::createFromFormat('Y-m-d', $resa['reservation_date']);
                                                echo $d ? $d->format('d-m-Y') : htmlspecialchars($resa['reservation_date']);
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars(substr($resa['reservation_heure'],0,5)) ?></td>
                                        <td><?= htmlspecialchars($resa['couvert']) ?></td>
                                        <td><?= htmlspecialchars($resa['allergies'] ?: 'Aucune') ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding:0;background:transparent;">
                                            <div class="resa-actions">
                                                <!-- Modifier -->
                                                <form method="get" action="reservation.php" style="display:inline;">
                                                    <input type="hidden" name="edit_resa" value="<?= $resa['id_reservations'] ?>">
                                                    <button type="submit" class="btn-edit">Modifier</button>
                                                </form>
                                                <!-- Supprimer -->
                                                <form method="post" action="user_space.php" style="display:inline;" onsubmit="return confirm('Supprimer cette réservation ?');">
                                                    <input type="hidden" name="delete_resa" value="<?= $resa['id_reservations'] ?>">
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

    <!-- Affiche la liste des allergies lorsque la radio allergie est = oui -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
    // Tabs logic
            const btnInfos = document.getElementById('tab-infos');
            const btnReservations = document.getElementById('tab-reservations');
            const contentInfos = document.getElementById('content-infos');
            const contentResa = document.getElementById('content-reservations');

            btnInfos.addEventListener('click', function() {
                btnInfos.classList.add('active');
                btnReservations.classList.remove('active');
                contentInfos.style.display = 'block';
                contentResa.style.display = 'none';
            });
            btnReservations.addEventListener('click', function() {
                btnInfos.classList.remove('active');
                btnReservations.classList.add('active');
                contentInfos.style.display = 'none';
                contentResa.style.display = 'block';
            });

            // Allergies: déjà présent, garde-le !
            function updateAllergiesList() {
                const allergiesOui = document.querySelector('input[name="allergie"][value="oui"]');
                const listeAllergies = document.getElementById('liste-allergies');
                if (allergiesOui.checked) {
                    listeAllergies.style.display = "block";
                } else {
                    listeAllergies.style.display = "none";
                    listeAllergies.querySelectorAll('input[type="checkbox"]').forEach(chk => chk.checked = false);
                }
            }
            document.querySelectorAll('input[name="allergie"]').forEach(radio => {
                radio.addEventListener('change', updateAllergiesList);
            });
            updateAllergiesList();
        });
        </script>
    </body>
</html>