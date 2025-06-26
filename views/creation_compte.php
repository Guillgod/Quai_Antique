<!DOCTYPE <!DOCTYPE html>
<html lang="fr">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once 'header.php';?>
        <section class="fond3">

        <H1>POURQUOI SE CREER UN COMPTE CHEZ NOUS ?</H1>
        <p>Afin de faciliter vos futures réservations, vous pouvez vous créer un compte sur notre plateforme. Nous enregistrerons vos informations et les remplirons automatiquement lors de votre prochaine réservation.</p>
        </section>
        
        <section class="container_creer_compte">
            <div class="container_creer_compte-content">
                <h2>S'INSCRIRE</h2>
                <p class="instructions">
                Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
                </p>
                <form action="creation_compte.php" method="post" autocomplete="off">
                <input type="text" id="nom" name="nom" placeholder="Nom de famille" required>

                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>

                <input type="email" id="email" name="email" placeholder="E-mail" required>

                <input type="password" id="mdp" name="mdp" placeholder="Mot de passe" required>

                <input type="password" id="mdp2" name="mdp2" placeholder="Confirmation du mot de passe" required>

                <input type="tel" id="tel" name="tel" placeholder="Téléphone" pattern="[0-9]{10}">
                <small>Format : 0102030405</small>

                <label for="couvert">Nombres de couverts par défaut</label>
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