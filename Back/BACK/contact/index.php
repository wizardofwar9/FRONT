<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <form method="post" class="form1" action="contact.php">
            <!-- ENVOIE DU FORMULAIRE A FAIRE-->
            <div class="strokes2">
                <div class="stroke3"></div>
                <h3 class="formtitle">CONTACT</h3>
                <div class="stroke4"></div>
            </div>
            <div class="inputs">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1" >Nom :</span>
                    <input type="text" name="fc01" id="fc1"  class="form-control" placeholder="Nom *" aria-label="Username"
                        aria-describedby="basic-addon1" required>
                    <small></small>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon2">E-mail :</span>
                    <input type="email" name="fc02" id="fc2"  class="form-control" placeholder="E-mail *"
                        aria-label="Username" aria-describedby="basic-addon1" required>
                    <small></small>
                </div>
                <div class="input-group mb-3 ">
                    <span class="input-group-text" id="basic-addon3">Objet :</span>
                    <input type="text" name="fc03" id="fc3" class="form-control" placeholder="Objet *" aria-label="Username"
                        aria-describedby="basic-addon1" required>
                    <small></small>                                
                </div>
                <div id="input-group" class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon4">Message :</span>
                    <textarea class="form-control" rows="10" name="message" id="message" required></textarea>
                </div>
            </div>
            <input type="submit" id="btnSend" class="btn" value="Envoyer">
        </form>
    <?php 
    $name = $_POST['fc01'];
    $email = $_POST['fc02'];
    $objet = $_POST['fc03'];
    $message = $_POST['message'];
    $formcontent="From: $name \n Message: $message";
    $recipient = "decottignies.jimmy@hotmail.fr";
    $subject = "Contact Form";
    $mailheader = "From: $email \r\n";
    mail($recipient, $subject, $formcontent, $mailheader) or die("Error!");
    echo "Nous reviendrons vers vous dans les plus brefs délais !";
    var_dump($_POST)
    ?>
<!-- BESOIN DE SECURISER LE FORM -->
        </body>
</html>