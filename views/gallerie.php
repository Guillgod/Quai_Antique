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
        <section class="fond4">

        <H1>Galerie</H1>
        
        </section>
        
        <section class="container2">
            <div class="container-content2">
                <h2>AUTOUR DE LA CUISINE</h2>
                <p>Notre but, vous faire passer un moment mémorable. Et pour cela, nous mettons un point d'honneur sur la qualité de nos produits. En collaboration directe avec nos producteurs locaux, ayez la garantie d'avoir des produits frais et savoureux dans votre assiette.</p>
                    <div class="creation-gallery2">
                        <div class="creation-img-wrapper">
                            <img src="../upload/photocreation1.png" alt="La fondue savoyarde" class="creation-image">
                            <div class="creation-alt"></div>
                        </div>
                        <div class="creation-img-wrapper">
                            <img src="../upload/photocreation2.png" alt="Le farçon façon Michant" class="creation-image">
                            <div class="creation-alt"></div>
                        </div>
                        <div class="creation-img-wrapper">
                            <img src="../upload/photocreation3.png" alt="Les rissoles aux noix jambon et raclette" class="creation-image">
                            <div class="creation-alt"></div>
                        </div>
                        <div class="creation-img-wrapper">
                            <img src="../upload/photocreation1.png" alt="La fondue savoyarde" class="creation-image">
                            <div class="creation-alt"></div>
                        </div>
                        <div class="creation-img-wrapper">
                            <img src="../upload/photocreation2.png" alt="Le farçon façon Michant" class="creation-image">
                            <div class="creation-alt"></div>
                        </div>
                        <div class="creation-img-wrapper">
                            <img src="../upload/photocreation3.png" alt="Les rissoles aux noix jambon et raclette" class="creation-image">
                            <div class="creation-alt"></div>
                        </div>
                    </div>
            </div>
        </section>





        <?php require_once 'footer.php';?>

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