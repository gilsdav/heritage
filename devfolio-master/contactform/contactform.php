<?php
if(isset($_POST['email'])) {
 
    // EDIT THE 2 LINES BELOW AS REQUIRED
    $email_to = "heritage.restobar@gmail.com";
    $email_subject = "Heritage - ";
 
    function died($error) {
        // your error code can go here
        echo "Nous sommes désolés, mais des erreurs ont été détectées dans le formulaire que vous avez envoyé. ";
        echo "Ces erreurs apparaissent ci-dessous.<br /><br />";
        echo $error."<br /><br />";
        echo "S'il vous plaît corriger ces erreurs.<br /><br />";
        die();
    }
 
 
    // validation expected data exists
    if( !isset($_POST['name']) ||
        !isset($_POST['email']) ||
        !isset($_POST['subject']) ||
        !isset($_POST['message'])) {
        died('Des données sont manquantes. Veuillez entrer toutes les informations demandées.');       
    }
 
     
 
    $name = $_POST['name']; // required
    $email_from = $_POST['email']; // required
    $email_subject = $email_subject . $_POST['subject']; // required
    $message = $_POST['message']; // required
 
    $error_message = "";
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
 
  if(!preg_match($email_exp,$email_from)) {
    $error_message .= 'L\'adresse e-mail que vous avez entrée ne semble pas être valide.<br />';
  }
 
    $string_exp = "/^[A-Za-z .'-]+$/";
 
  if(!preg_match($string_exp,$name)) {
    $error_message .= 'Le nom que vous avez entré ne semble pas être valide.<br />';
  }
 
  if(strlen($message) < 2) {
    $error_message .= 'Le message que vous avez entré ne semble pas être valide.<br />';
  }
 
  if(strlen($error_message) > 0) {
    died($error_message);
  }
 
    $email_message = "Details du formulaire ci-dessous.\n\n";
 
     
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
 
    $email_message .= "Nom: ".clean_string($name)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
    $email_message .= "Message: ".clean_string($message)."\n";
 
// create email headers
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();/* . "\r\n" .
'Content-Type: text/html; charset=UTF-8';*/
// @mail($email_to, $email_subject, $email_message, $headers);
if(mail($email_to,$email_subject,$email_message,$headers)){
    $statusMsg = 'OK';
    $msgClass = 'succdiv';
} else {
    $statusMsg = 'La soumission de votre demande de contact a échoué. Veuillez réessayer.';
    $msgClass = 'errordiv';
}
echo $statusMsg
?>

<?php
}
?>