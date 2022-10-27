<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boucle et Tableaux</title>
    <link rel="stylesheet" href="boucle.css">
</head>

<body>
    <?php
   $eleve['Robert'] = 17;
   $eleve['michel'] = 15;
   $eleve['Jean'] = 19;
   $eleve['toto'] = 12;
   $eleve['titi'] = 14;
   $eleve['steph'] = 195; 
   

  /* $count = count($eleve);

   for ($x=0; $x<$count; $x++) {
      echo "<pre>";
      echo $tab[$x];
       
   }*/
   echo "<pre>";
   print_r ($eleve);
   echo "<table border=1 bordercolor=green align=center >
    <tr><th>prénoms</th>
    <th>Notes</th></tr>";
    
    /*echo "<tr><td>array=[]</td>
    <td>array=[]</td></tr>";*/
 ?>
    <?php
$note = array("Peter"=>"35", "Ben"=>"37", "Joe"=>"43");
echo "<pre>";
foreach($note as $x => $x_value) {
  echo "Key=" . $x . ", Value=" . $x_value;
  //echo "<br>";
  echo "<tr><td> $x  </td><td>$x_value   </td></tr>";
 
}
//---------Code anthony-------pour exemple--------------------------------
/*echo "<table border=1 bordercolor=green align=center >;*/
    ?>
    <table>
        <!-- j'ouvre mon tableau et définit les titre de celules -->

        <tr>

            <td class="vert">Prenoms</td>

            <td class="vert">Notes</td>

        </tr>



        <?php

// je crée mon tableau associatif

$eleves = array("jimmy" => "18/20" , "halim" => "20/20" , "greg" => "12/20", "cedric" => "32/20" , "anthony" => "12/20", "oceane" => "oceane/20");

ksort($eleves); //ksort renvoie la valeur les "key" entre les "" au lieux de l'id de position



foreach($eleves as $nom => $notes) { // lister eleves "nom" => "notes"

echo "<tr><td>  $nom </td><td>  $notes </td></tr>"; // afficher nom => notes lister dans un tableau





};



?>
</body>

</html>