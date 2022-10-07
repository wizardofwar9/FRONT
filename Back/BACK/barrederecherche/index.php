<?php
$bdd = new PDO('mysql:host=localhost;dbname=voitures;', 'root',  '');
$allvoitures = $bdd->query('SELECT * FROM voiture ORDER BY id_Voiture DESC ');
if(isset($_GET['s']) AND !empty($_GET['s'])){
    $recherche = htmlspecialchars($_GET['s']);
    $allvoitures = $bdd->query('SELECT Marques FROM voiture WHERE Marques LIKE "%'.$recherche.'%" ORDER BY id_Voiture DESC');
}


?>
<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher des données</title>
</head>

<body>
    <form method="GET">
        <input type="search" name="s" placeholder="Rechercher des données">
        <input type="submit" name="env">
    </form>
    <section class="afficher_marque">
        <?php
        if($allvoitures->rowcount() > 0){
            while($Marque = $allvoitures->fetch()){
               ?>
        <p><?=$Marque['Marques']; ?>
        </p>

        <?php
         } }else{ ?>
        <p>Aucune Marque trouvée</p>
        <?php
    } 
    ?>
    </section>
</body>

</html>