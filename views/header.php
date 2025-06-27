<?php
if (session_status() === PHP_SESSION_NONE) session_start();
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
    <header>
        <div class="header_content">
            <nav>
                <button id="burger-btn" class="burger-btn" aria-label="Ouvrir le menu">
                    <span class="burger-bar"></span>
                    <span class="burger-bar"></span>
                    <span class="burger-bar"></span>
                </button>
                <ul class="menu" id="nav-menu">
                    <button class="close-btn" id="close-menu-btn" aria-label="Fermer le menu">&times;</button>
                    <li><a href="../views/Page_accueil.php">Accueil</a></li>
                    <li><a href="../views/notre_carte.php">Notre carte</a></li>
                    <li><a href="../views/gallerie.php">Galerie</a></li>
                    <li><a href="../views/reservation.php">Réserver une table</a></li>
                    <?php if(isset($_SESSION['user'])): ?>
                        <li><a href="../views/user_space.php">Mon compte</a></li>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['user'])): ?>
                        <li><a href="../views/login.php">Se connecter</a></li>
                    <?php else: ?>
                        <li><a href="../controllers/logout.php">Déconnexion</a></li>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1): ?>
                        <li><a href="#">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <script>
        const burgerBtn = document.getElementById('burger-btn');
        const navMenu = document.getElementById('nav-menu');
        const backdrop = document.getElementById('sidebar-backdrop');

        function toggleMenu() {
            navMenu.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('open');
        }

        // Clic sur burger
        burgerBtn.addEventListener('click', toggleMenu);

        // Clic sur le fond sombre pour fermer
        if (backdrop) {
            backdrop.addEventListener('click', toggleMenu);
        }

        // Fermer le menu avec la croix
        const closeMenuBtn = document.getElementById('close-menu-btn');
        if (closeMenuBtn) {
            closeMenuBtn.addEventListener('click', toggleMenu);
        }
        </script>
    </body>
</html>