<?php
session_start();
require_once '../controllers/ReservationController.php';

$date_aujourdhui = date('Y-m-d');
$defaultNbPersons = 2;
$userAllergies = [];

if (isset($_SESSION['user'])) {
    $controller = new ReservationController();
    $userData = $controller->getUserDefaultData();
    if ($userData) {
        $defaultNbPersons = $userData['nb_persons'];
        $userAllergies = $userData['allergies']; // tableau de types d'allergie
    }
}

// GESTION SOUMISSION FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ReservationController();
    $controller->submitReservation($_POST);
}
?>




<?php
$date_aujourdhui = date('Y-m-d');
?>

<!DOCTYPE <!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Réserver une table</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        
        <?php require_once 'header.php';?>
        
        <section class="container_creer_compte">
            <div class="container_creer_compte-content">
                <h2>RÉSERVER UNE TABLE</h2>
                <p class="instructions">
                Pour réserver une table, remplissez ce formulaire.
                </p>
                <form action="reservation.php" method="post" autocomplete="off">
                

                <p class="instructions">
                Pour plus de 10 personnes, contactez-nous par téléphone.
                </p>
                 <label for="couvert">Nombres de couverts</label>
                <!-- <select id="couvert" name="couvert">
                    <option value="1">1</option>
                    <option value="2" selected>2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select> -->
                <select id="couvert" name="couvert">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>" <?= ($i == $defaultNbPersons) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <!-- Date de réservation -->
                <input type="date" id="date" name="date" value="<?php echo $date_aujourdhui; ?>" required style="width: 140px; margin-bottom: 22px;">

                <!-- Choix du service midi/soir -->
                <label for="service" style="font-weight: bolder; margin-bottom:10px;">
                    Souhaitez-vous réserver pour le midi ou le soir ?
                </label>
                <div class="radio-group" style="margin-bottom: 18px;">
                    <label><input type="radio" name="service" value="midi" checked> Midi</label>
                    <label><input type="radio" name="service" value="soir"> Soir</label>
                </div>

               

                <label class="label-radio">Avez-vous des allergies à signaler ?</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="allergie" value="oui" 
                                <?= (!empty($userAllergies)) ? 'checked' : '' ?>>
                            Oui
                        </label>
                        <label>
                            <input type="radio" name="allergie" value="non" 
                                <?= (empty($userAllergies)) ? 'checked' : '' ?>>
                            Non
                        </label>
                    </div>
                <!-- Liste des allergies -->
                    <div class="liste-allergies" id="liste-allergies"
                        style="margin-top:10px;<?= (!empty($userAllergies)) ? '' : 'display:none;' ?>">
                        <label><input type="checkbox" name="allergies[]" value="Arachide" <?= in_array('Arachide', $userAllergies) ? 'checked' : '' ?>> Arachide</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Céleri" <?= in_array('Céleri', $userAllergies) ? 'checked' : '' ?>> Céleri</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Crustacés" <?= in_array('Crustacés', $userAllergies) ? 'checked' : '' ?>> Crustacés</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Fruits à coques" <?= in_array('Fruits à coques', $userAllergies) ? 'checked' : '' ?>> Fruits à coques</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Gluten" <?= in_array('Gluten', $userAllergies) ? 'checked' : '' ?>> Gluten</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Lactose" <?= in_array('Lactose', $userAllergies) ? 'checked' : '' ?>> Lactose</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Lupin" <?= in_array('Lupin', $userAllergies) ? 'checked' : '' ?>> Lupin</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Mollusque" <?= in_array('Mollusque', $userAllergies) ? 'checked' : '' ?>> Mollusque</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Moutarde" <?= in_array('Moutarde', $userAllergies) ? 'checked' : '' ?>> Moutarde</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Oeufs" <?= in_array('Oeufs', $userAllergies) ? 'checked' : '' ?>> Oeufs</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Poissons" <?= in_array('Poissons', $userAllergies) ? 'checked' : '' ?>> Poissons</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Sésame" <?= in_array('Sésame', $userAllergies) ? 'checked' : '' ?>> Sésame</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Soja" <?= in_array('Soja', $userAllergies) ? 'checked' : '' ?>> Soja</label><br>
                        <label><input type="checkbox" name="allergies[]" value="Sulfites" <?= in_array('Sulfites', $userAllergies) ? 'checked' : '' ?>> Sulfites</label>
                    </div>
                <button type="submit" class="submit-btn">SOUMETTRE</button>
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

            // *** Toujours forcer l’affichage si une case est cochée (cas POST) ***
            let anyChecked = false;
            document.querySelectorAll('#liste-allergies input[type="checkbox"]').forEach(function(chk){
                if(chk.checked) anyChecked = true;
            });
            if(anyChecked) {
                document.querySelector('input[name="allergie"][value="oui"]').checked = true;
                document.getElementById('liste-allergies').style.display = 'block';
            }

            // Init au chargement (utile si "Oui" pré-coché)
            updateAllergiesList();
        });
        </script>
    </body>
</html>