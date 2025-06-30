<?php
require_once '../controllers/GalleryController.php';
$galleryController = new GalleryController();
$gallery_photos = $galleryController->getAllPhotos();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Galerie</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<?php require_once 'header.php';?>
<section class="fond4">
    <H1>Galerie</H1>
</section>
<section class="container2">
    <div class="container-content2">
        <h2>AUTOUR DE LA CUISINE</h2>
        <p>Notre but, vous faire passer un moment mémorable. Et pour cela, nous mettons un point d'honneur sur la qualité de nos produits. En collaboration directe avec nos producteurs locaux, ayez la garantie d'avoir des produits frais et savoureux dans votre assiette.</p>
        <div class="creation-gallery2">
            <?php if (empty($gallery_photos)): ?>
                <div style="text-align:center;color:#888;font-size:1.1em;">Aucune photo en galerie pour l’instant.</div>
            <?php else: ?>
                <?php foreach ($gallery_photos as $photo): ?>
                    <div class="creation-img-wrapper">
                        <img src="display_gallery_photo.php?id=<?= $photo['id_gallery'] ?>"
                             alt="<?= htmlspecialchars($photo['titre']) ?>"
                             class="creation-image">
                        <div class="creation-alt"></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <section class="carte-main">
            <div class="carte-bloc">
                <p>L'un de ces plats vous tente ? Venez le goûter !</p>
                <a href="reservation.php" class="submit-btn" style="display:inline-block;text-align:center;text-decoration:none;">RÉSERVER</a>
            </div>
        </section>
    </div>
</section>
<?php require_once 'footer.php';?>

<!-- Affichage des légendes des images à l'hover.  -->
<script>
document.querySelectorAll('.creation-img-wrapper').forEach(function(wrapper) {
    var img = wrapper.querySelector('img');
    var altText = img.getAttribute('alt');
    var altDiv = wrapper.querySelector('.creation-alt');
    altDiv.textContent = altText;
});
</script>
</body>
</html>