<?php
$palvelin = "localhost"; 
$kayttaja = "root";  // tämä on tietokannan käyttäjä, ei tekemäsi järjestelmän
$salasana = "";
$tietokanta = $tietokanta ?? "projektitori"; // (isset($tietokanta)) ? $tietokanta : "autodb";

if (strpos($_SERVER['HTTP_HOST'],"azurewebsites") !== false){
   //define("DEBUG",false);
   $debug = $_ENV['PHP_DEBUG'] ?? getenv('PHP_DEBUG');
   define("DEBUG", $debug ? true : false);
   $PALVELU = "";
   $palvelin = $_ENV['MYSQL_HOSTNAME'] ?? getenv('MYSQL_HOSTNAME');
   $kayttaja = $_ENV['MYSQL_USERNAME'] ?? getenv('MYSQL_USERNAME');
   $salasana = $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD');
   /* Mailtrap */
   $EMAIL_ADMIN = $_ENV['EMAIL_ADMIN'] ?? getenv('EMAIL_ADMIN'); 
   $username_mailtrap = $_ENV['EMAIL_USERNAME'] ?? getenv('EMAIL_USERNAME');
   $password_mailtrap = $_ENV['EMAIL_PASSWORD'] ?? getenv('EMAIL_PASSWORD');
}

$yhteys = new mysqli($palvelin, $kayttaja, $salasana, $tietokanta);

if ($yhteys->connect_error) {
   die("Yhteyden muodostaminen epäonnistui: " . $yhteys->connect_error);
   }
// echo "Yhteys muodostettu onnistuneesti!<br>";   
$yhteys->set_charset("utf8");

function mysqli_my_query($query) {
   $yhteys = $GLOBALS['yhteys']; 
   $result = false;
   try {
      $result = $yhteys->query($query); 
      /*if ($yhteys->affected_rows > 0){
         echo "<p class='alert alert-success'>Tietokantakysely onnistui.</p>";
         }
      else {
         echo "<p class='alert alert-danger'>Tietokantakysely epäonnistui.</p>";
         } */
      } 
   catch (Exception $e) {
      echo "<p class='alert alert-danger'>Virhe tietokantakyselyssä.</p>";
      debuggeri("Virhe $yhteys->errno: " . $e->getMessage());
      }
    return $result;
    }

?>
