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
                <form action="creation_compte.php" method="post" autocomplete="off">
                <input type="text" id="nom" name="nom" placeholder="Nom de famille" required>

                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>

                <input type="email" id="email" name="email" placeholder="E-mail" required>

                <input type="tel" id="tel" name="tel" placeholder="Téléphone" pattern="[0-9]{10}">
                <small>Format : 0102030405</small>

                <p class="instructions">
                Pour plus de 10 personnes, contactez-nous par téléphone.
                </p>
                 <label for="couvert">Nombres de couverts</label>
                <select id="couvert" name="couvert">
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
                    <label><input type="radio" name="allergie" value="oui"> Oui</label>
                    <label><input type="radio" name="allergie" value="non" checked> Non</label>
                </div>
                
                <button type="submit" class="submit-btn">SOUMETTRE</button>
                </form>
            </div>
        </section>




        <?php require_once 'footer.php';?>

        
    </body>
</html>