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
         
        
        <section class="container_creer_compte2">
            <div class="container_creer_compte-content">
                <h2>SE CONNECTER</h2>
                <p class="instructions">
                Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
                </p>
                <form action="login.php" method="post" autocomplete="off">

                <input type="email" id="email" name="email" placeholder="E-mail" required>

                <input type="password" id="mdp" name="mdp" placeholder="Mot de passe" required>
                <div class="compte">
                <p>Vous n'avez pas de compte ? </p>
                <a href="creation_compte.php" class="link">Créez-en un ici !</a>
                </div>

                <button type="submit" class="submit-btn">VALIDER</button>
                </form>
            </div>
        </section>




        <?php require_once 'footer.php';?>

        
    </body>
</html>