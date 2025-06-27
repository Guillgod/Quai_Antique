<?php
session_start();
require_once '../controllers/LoginController.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $mdp = $_POST['mdp'] ?? '';
    $controller = new LoginController();
    $message = $controller->login($email, $mdp);
}
?>





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
                
                <form action="login.php" method="post" autocomplete="off">

                <input type="email" id="email" name="email" placeholder="E-mail" required>

                <input type="password" id="mdp" name="mdp" placeholder="Mot de passe" required>
                <div class="compte">
                <p>Vous n'avez pas de compte ? </p>
                <a href="creation_compte.php" class="link">Créez-en un ici !</a>
                </div>
                <!-- Affiche un message d’erreur s’il existe -->
                    <?php if ($message): ?>
                        <div class="message_erreur"><?php echo $message; ?></div>
                    <?php endif; ?>
                <button type="submit" class="submit-btn">VALIDER</button>
                </form>
            </div>
        </section>




        <?php require_once 'footer.php';?>

        
    </body>
</html>