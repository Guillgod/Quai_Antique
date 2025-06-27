
<?php
session_start();
require_once '../controllers/UserController.php';
require_once '../models/Model_user.php';

$message = '';
$user_id = $_SESSION['user']['id_users'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

$modelUser = new ModelUser();
$userData = $modelUser->getUserById($user_id);
$userAllergies = $modelUser->getAllergiesByUserId($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();
    $message = $controller->updateUserInfos($_POST, $user_id, $userData['password']);
    // Recharge les infos après modif
    $userData = $modelUser->getUserById($user_id);
    $userAllergies = $modelUser->getAllergiesByUserId($user_id);
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
        <?php require_once 'header.php';?>
         
        
        <section class="container_creer_compte">
            <div class="container_creer_compte-content">
                <h2>Vos informations</h2>
                <p class="instructions">
                Vous pouvez modifier vos informations personnelles ou consulter vos réservations. 
                </p>
                <form action="user_space.php" method="post" autocomplete="off">
                <input type="text" id="nom" name="nom" placeholder="Nom de famille" value="<?= htmlspecialchars($userData['nom'] ?? '') ?>" required>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?= htmlspecialchars($userData['prenom'] ?? '') ?>" required>
                <input type="email" id="email" name="email" placeholder="E-mail" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
                <input type="password" id="mdp" name="mdp" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)" autocomplete="new-password">
                <input type="password" id="mdp2" name="mdp2" placeholder="Confirmer le nouveau mot de passe (laisser vide pour ne pas changer)" autocomplete="new-password">
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
                <button type="submit" class="submit-btn">MODIFIER</button>
                </form>
            </div>
        </section>




        <?php require_once 'footer.php';?>

    <!-- Affiche la liste des allergies lorsque la radio allergie est = oui -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateAllergiesList() {
                const allergiesOui = document.querySelector('input[name="allergie"][value="oui"]');
                const listeAllergies = document.getElementById('liste-allergies');
                if (allergiesOui.checked) {
                    listeAllergies.style.display = "block";
                } else {
                    listeAllergies.style.display = "none";
                    // Décoche tout quand caché (optionnel)
                    listeAllergies.querySelectorAll('input[type="checkbox"]').forEach(chk => chk.checked = false);
                }
            }
            // Sur changement des radios
            document.querySelectorAll('input[name="allergie"]').forEach(radio => {
                radio.addEventListener('change', updateAllergiesList);
            });
            // Init au chargement (utile si "Oui" pré-coché)
            updateAllergiesList();
        });
        </script>
    </body>
</html>