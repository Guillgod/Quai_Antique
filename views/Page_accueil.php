<!DOCTYPE <!DOCTYPE html>
<html lang="fr">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once 'header.php';?>
        <section class="fond">

        <H1>Le Quai Antique</H1>
        <p>Le chef Arnaud Michant et toute son équipe vous accueille dans son restaurant à Chambéry afin de vous faire découvrir les saveurs traditionnelles de la Savoie. Venez en famille ou entre amis partager une fondue Savoyarde ou délectez vous d'un bon vin local autour d'une planche de fromage.</p>
        </section>
        
        <section class="container">
            <div class="container-content">
                <h2>Du producteur au consommateur</h2>
                <p>Notre but, vous faire passer un moment mémorable. Et pour cela, nous mettons un point d'honneur sur la qualité de nos produits. En collaboration directe avec nos producteurs locaux, ayez la garantie d'avoir des produits frais et savoureux dans votre assiette.</p>
                    <div class="creation-gallery">
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