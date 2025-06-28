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

// En haut de reservation.php
$id_edit = isset($_GET['edit_resa']) ? (int)$_GET['edit_resa'] : null;
$resaData = null;
if ($id_edit) {
    $model = new ModelReservation();
    $resaData = $model->getReservationById($id_edit, $_SESSION['user']['id_users']);
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
        <link href="../css/style.css" rel="stylesheet">
    </head>
    <body>
        
        <?php require_once '../views/header.php';?>
        
        <section class="container_creer_compte">
            <div class="container_creer_compte-content">
                <h2>RÉSERVER UNE TABLE</h2>
                <p class="instructions">
                Pour réserver une table, remplissez ce formulaire.
                </p>
                
                    
                <form action="reservation.php" method="post" autocomplete="off">
                
                <!-- Si on édite une réservation, on ajoute un champ caché pour récupérer les infos de la réservation-->
                <?php if ($id_edit): ?>
                    <input type="hidden" name="edit_resa" value="<?= $id_edit ?>">
                <?php endif; ?>

                <p class="instructions">
                Pour plus de 10 personnes, contactez-nous par téléphone.
                </p>
                 <label for="couvert">Nombres de couverts</label>
                 
                <select id="couvert" name="couvert">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>"
                        <?= ($id_edit && $resaData && $resaData['couvert'] == $i) ? 'selected' : ((!$id_edit && $i == $defaultNbPersons) ? 'selected' : '') ?>
                        ><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <!-- Date de réservation -->
                <input type="date" id="date" name="date"value="<?= $id_edit && $resaData ? htmlspecialchars($resaData['reservation_date']) : $date_aujourdhui ?>" required style="width: 140px; margin-bottom: 22px;">

                <!-- Choix du service midi/soir -->
                <label for="service" style="font-weight: bolder; margin-bottom:10px;">
                    Souhaitez-vous réserver pour le midi ou le soir ?
                </label>
                <div class="radio-group" style="margin-bottom: 18px;">
                    <label>
                        <input type="radio" name="service" value="midi"
                            <?= ($id_edit && $resaData && substr($resaData['reservation_heure'], 0, 2) < 17) ? 'checked' : ((!$id_edit) ? 'checked' : '') ?>
                        > Midi
                    </label>
                    <label>
                        <input type="radio" name="service" value="soir"
                            <?= ($id_edit && $resaData && substr($resaData['reservation_heure'], 0, 2) >= 17) ? 'checked' : '' ?>
                        > Soir
                    </label>
                    <!-- Ici, j'ai fait simple : si l'heure de la réservation commence à 17h ou après, alors c’est "soir". À adapter selon les besoins. -->
                </div>

                <!-- Sélection horaire -->
                <div id="select-horaire-container" style="display:none;">
                    <label for="horaire">Horaire de réservation</label>
                    <select id="horaire" name="horaire">
                        <?php if ($id_edit && $resaData): ?>
                            <option value="<?= htmlspecialchars($resaData['reservation_heure']) ?>" selected>
                                <?= htmlspecialchars($resaData['reservation_heure']) ?>
                            </option>
                        <?php endif; ?>
                        <!-- Les autres horaires sont chargés en JS comme tu fais déjà -->
                    </select>
                </div>
                <!-- Le JavaScript qui charge les horaires peut forcer le selected, donc ce <option> sert de fallback pour afficher l'horaire si en modification.-->

                <!-- Allergies -->
                <label class="label-radio">Avez-vous des allergies à signaler ?</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="allergie" value="oui"
                            <?= ($id_edit && $resaData && !empty($resaData['allergies'])) || (!$id_edit && !empty($userAllergies)) ? 'checked' : '' ?>
                        > Oui
                    </label>
                    <label>
                        <input type="radio" name="allergie" value="non"
                            <?= ($id_edit && $resaData && empty($resaData['allergies'])) || (!$id_edit && empty($userAllergies)) ? 'checked' : '' ?>
                        > Non
                    </label>
                </div>
                

                    
                <!-- Liste des allergies -->
                    <div class="liste-allergies" id="liste-allergies"
                    style="margin-top:10px;<?= (($id_edit && $resaData && !empty($resaData['allergies'])) || (!$id_edit && !empty($userAllergies))) ? '' : 'display:none;' ?>">
                    <?php
                    $allergyList = [
                        "Arachide", "Céleri", "Crustacés", "Fruits à coques", "Gluten", "Lactose",
                        "Lupin", "Mollusque", "Moutarde", "Oeufs", "Poissons", "Sésame", "Soja", "Sulfites"
                    ];
                    foreach ($allergyList as $allergy) {
                        $checked = '';
                        if ($id_edit && $resaData && in_array($allergy, $resaData['allergies'])) {
                            $checked = 'checked';
                        } elseif (!$id_edit && in_array($allergy, $userAllergies)) {
                            $checked = 'checked';
                        }
                        echo "<label><input type=\"checkbox\" name=\"allergies[]\" value=\"$allergy\" $checked> $allergy</label><br>";
                    }
                    ?>
                </div>

                <button type="submit" class="submit-btn">SOUMETTRE</button>
                </form>
            </div>
        </section>




        <?php require_once 'footer.php';?>
        

        <?php if ($id_edit && $resaData): ?>
            <script>
                window.resaHoraireToSelect = "<?= htmlspecialchars($resaData['reservation_heure']) ?>";
            </script>
            <?php else: ?>
            <script>
                window.resaHoraireToSelect = null;
            </script>
        <?php endif; ?>



        <!-- Affiche la liste des allergies lorsque la radio allergie est = oui -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Gestion allergies : afficher/cacher la liste selon la radio
            function updateAllergiesList() {
                const allergiesOui = document.querySelector('input[name="allergie"][value="oui"]');
                const listeAllergies = document.getElementById('liste-allergies');
                if (allergiesOui && allergiesOui.checked) {
                    listeAllergies.style.display = "block";
                } else {
                    listeAllergies.style.display = "none";
                    // Décoche tout quand caché (optionnel)
                    if (listeAllergies) {
                        listeAllergies.querySelectorAll('input[type="checkbox"]').forEach(chk => chk.checked = false);
                    }
                }
            }

            // Sur changement des radios
            document.querySelectorAll('input[name="allergie"]').forEach(radio => {
                radio.addEventListener('change', updateAllergiesList);
            });

            // Toujours forcer l’affichage si une case est cochée (cas POST/édition)
            let anyChecked = false;
            document.querySelectorAll('#liste-allergies input[type="checkbox"]').forEach(function(chk){
                if(chk.checked) anyChecked = true;
            });
            if(anyChecked) {
                const radioOui = document.querySelector('input[name="allergie"][value="oui"]');
                if(radioOui) radioOui.checked = true;
                document.getElementById('liste-allergies').style.display = 'block';
            }

            // Init allergies au chargement
            updateAllergiesList();


            // --- Gestion du select horaire (création ET édition) ---

            function getSelectedService() {
                return document.querySelector('input[name="service"]:checked').value;
            }

            function fetchTimeSlots() {
                const date = document.getElementById('date').value;
                const service = getSelectedService();
                if (!date) return;

                fetch('../controllers/get_timeslots.php?date=' + encodeURIComponent(date) + '&service=' + encodeURIComponent(service))
                .then(response => response.json())
                .then(times => {
                    // console.log("DEBUG horaires :", times, "resaHoraireToSelect:", window.resaHoraireToSelect);
                    const selectHoraire = document.getElementById('horaire');
                    selectHoraire.innerHTML = '';

                    if (times.length > 0) {
                        times.forEach(function(h){
                            const opt = document.createElement('option');
                            opt.value = h;
                            opt.textContent = h;
                            // Si mode édition et horaire correspond → sélectionne-le
                            if (window.resaHoraireToSelect && h.substring(0,5) === window.resaHoraireToSelect.substring(0,5)) {
                                opt.selected = true;
                            }
                            selectHoraire.appendChild(opt);
                        });
                        document.getElementById('select-horaire-container').style.display = 'block';
                    } else {
                        selectHoraire.innerHTML = '';
                        document.getElementById('select-horaire-container').style.display = 'none';
                    }
                });
            }

            // Lancement au chargement
            fetchTimeSlots();

            document.getElementById('date').addEventListener('change', fetchTimeSlots);
            document.querySelectorAll('input[name="service"]').forEach(radio => {
                radio.addEventListener('change', fetchTimeSlots);
            });

        });
        </script>


    </body>
</html>