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
                    <button type="button" class="tab-btn" id="tab-reservations">La Galerie</button>
                    <button type="button" class="tab-btn" id="tab-reservations">Les Réservations</button>
                </div>
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
    </body>
</html>