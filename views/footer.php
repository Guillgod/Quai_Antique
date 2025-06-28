
<?php
require_once '../controllers/InfoController.php';
$infoController = new InfoController();
$restaurantInfos = $infoController->getAllInfos();
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
    <footer class="footer">
    <div class="footer-hours">
        <?php foreach ($restaurantInfos as $info): ?>
            <?php
                $jour = htmlspecialchars($info['day']);
                $formatH = function($h) { return $h ? substr($h, 0, 5) : ''; };
                $om = $formatH($info['opening_morning']);
                $cm = $formatH($info['closure_morning']);
                $on = $formatH($info['opening_night']);
                $cn = $formatH($info['closure_night']);

                // Si matin = 00:00-00:00 → pas d'affichage matin
                $matin = ($om === "00:00" && $cm === "00:00") ? "" : ($om && $cm ? "$om - $cm" : "");
                // Si soir = 00:00-00:00 → pas d'affichage soir
                $soir = ($on === "00:00" && $cn === "00:00") ? "" : ($on && $cn ? "$on - $cn" : "");

                // Prépare l'affichage final
                if (empty($matin) && empty($soir)) {
                    $heures = "Fermé";
                } elseif (!empty($matin) && !empty($soir)) {
                    $heures = "$matin | $soir";
                } elseif (!empty($matin)) {
                    $heures = "$matin";
                } else {
                    $heures = "$soir";
                }
            ?>
            <p>
                <strong><?= $jour ?> :</strong>
                <span><?= $heures ?></span>
            </p>
        <?php endforeach; ?>
    </div>
        <h2>CONTACTEZ-NOUS :</h2>
        <div class="footer-contact">
            <p>Téléphone : 04 79 85 00 00</p>
            <p>Email : quai-antique@gmail.com</p>
        </div>
        <ul class="footer-links">
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">Politique de confidentialité</a></li>
        </ul>
        <p class="footer-copy">&copy; 2023 Le Quai Antique. Tous droits réservés.</p>
    </div>
    </footer>
    </body>
</html>