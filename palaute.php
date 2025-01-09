<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'external/Exception.php';
require 'external/PHPMailer.php';
require 'external/SMTP.php';

$output = '';


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty($_POST['feedback_text']) || strlen(trim($_POST['feedback_text'])) < 2) {
            $errors['feedback_text'] = "Palautteen on oltava vähintään 2 merkkiä pitkä.";
            $is_invalid['feedback_text'] = "is-invalid"; // Merkitään kenttä virheelliseksi
        } else {
            // Jos palaute on oikein, puhdistetaan se ennen tallennusta
            $value['feedback_text'] = trim($_POST['feedback_text']);
            $feedback_text = trim($_POST['feedback_text']);
        }

    // Etunimi::
    if (empty($_POST['name']) || strlen(trim($_POST['name'])) < 2) {
        // Virheilmoitus, jos kenttä on tyhjä tai nimi on alle 2 merkkiä pitkä
        $errors['name'] = "Nimen on oltava vähintään 2 merkkiä pitkä.";
        $is_invalid['name'] = "is-invalid"; // Merkitään kenttä virheelliseksi
    } elseif (!preg_match("/^[a-zA-ZåäöÅÄÖ\- ']{2,150}$/", $_POST['name'])) {
        // Virheilmoitus, jos etunimessä on kiellettyjä merkkejä
        $errors['name'] = "Nimessä saa olla vain kirjaimia, tavuviiva ja välilyönti.";
        $is_invalid['name'] = "is-invalid";
    } else {
        // Jos nimi on oikein, puhdistetaan se ennen tallennusta
        $value['name'] = trim($_POST['name']);
        $name = trim($_POST['name']);
        //$name = real_escape_string(trim($_POST['name']));
    }

    // Tarkistetaan onko sähköposti jo käytössä
    if (empty($_POST['email'])) {
        $errors['email'] = "Sähköpostiosoite on pakollinen.";
        $is_invalid['email'] = "is-invalid";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Anna kelvollinen sähköpostiosoite.";
        $is_invalid['email'] = "is-invalid";
    } else {
        // Puhdistetaan ja escapeataan sähköposti
        //$email = real_escape_string(trim($_POST['email']));
        $value['email'] = trim($_POST['email']);
        $email = trim($_POST['email']);
    }

    // Arvosana
    if (empty($_POST['rating'])) {
        $errors['rating'] = "Rooli on pakollinen.";
        $is_invalid['rating'] = "is-invalid";
    } else {
        $rating = trim($_POST['rating']);
    
        // Tarkista, että rooli on joko 'student' tai 'provider'
        if (!in_array($rating, ['1', '2', '3', '4', '5'])) {
            $errors['rating'] = "Valitse kelvollinen rooli (opiskelija tai projektin tarjoaja).";
            $is_invalid['rating'] = "is-invalid";
        } else {
            $value['rating'] = $rating; // Syöte on validi, joten tallennetaan se
        }
    }
        
        
        //$comments = $_POST['comments'];  // Vapaatekstikenttä
    // Jos virheitä ei ole, suoritetaan lisäys tietokantaan
    if (empty($errors)) {

        // Määritellään vastaanottaja ja otsikko
        $to = "projektitori@mailinator.com";  // Vaihda tähän oma sähköpostiosoitteesi
        $subject = "Uusi palaute verkkosivustoltasi";

        // Luo PHPMailer-olio
        $mail = new PHPMailer(true);
        
        try {
            // SMTP-asetukset
            $mail->isSMTP();                                         // Aseta käyttämään SMTP:tä
            $mail->Host       = 'smtp.gmail.com';                  // SMTP-palvelimen osoite
            $mail->SMTPAuth   = true;                                // Ota käyttöön SMTP-todennus
            $mail->Username   = 'projektitori@gmail.com';            // SMTP-käyttäjätunnus
            $mail->Password   = 'nqis vvzw rjqw vjcj';                     // SMTP-salasana
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Käytä TLS-salausta
            $mail->Port       = 587;                                // SMTP-portti

            // Merkistön asetus
            $mail->CharSet = 'UTF-8';  // Ota käyttöön UTF-8-merkistö

            // Lähettäjän tiedot
            $mail->setFrom('no-reply@example.com', 'Verkkosivusto'); // Lähettäjän sähköposti ja nimi
            $mail->addAddress($to);                                  // Vastaanottajan sähköposti

            // Viestin sisältö
            $mail->isHTML(true);                                     // Lähetetään HTML-sähköposti
            $mail->Subject = $subject;
            
            // Luo viesti HTML-muodossa
            $message = "<html><body>";
            $message .= "<h2>Uusi palaute:</h2>";
            $message .= "<p><strong>Palaute:</strong> " . nl2br(htmlspecialchars($feedback_text)) . "</p>";
            $message .= "<p><strong>Nimi:</strong> " . nl2br(htmlspecialchars($name)) . "</p>";
            $message .= "<p><strong>Sähköposti:</strong> " . nl2br(htmlspecialchars($email)) . "</p>";
            $message .= "<p><strong>Arvio:</strong> " . $rating . "/5</p>";
            
            // Lisää vapaatekstikentän sisältö
            /*if (!empty($comments)) {
                $message .= "<p><strong>Muita kommentteja:</strong> " . nl2br(htmlspecialchars($comments)) . "</p>";
            }*/

            $message .= "</body></html>";

            $mail->Body = $message;  // Aseta sähköpostin runko

            // Lähetä sähköposti
            $mail->send();
            ///echo "Kiitos palautteestasi! Palaute on lähetetty.";
            $output = '<div class="output-success"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Kiitos palautteestasi! Palaute on lähetetty.</div>';
            $value['feedback_text'] = null;
            $value['name'] = null;
            $value['email'] = null;
            $value['rating'] = null;
        } catch (Exception $e) {
            //echo "Palauteen lähettäminen epäonnistui. Virhe: {$mail->ErrorInfo}";
            $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Palauteen lähettäminen epäonnistui. Virhe: {$mail->ErrorInfo}.</div>';
        }
    }
}




?>

<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Palaute - Projektitori";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
?>
<body class="feedbackpage">
<?php include 'nav.php'; ?>


<div class="container">


    <h1>Anna meille palautetta!</h1>
    <p>Vaikka emme voi vastata jokaiseen palautteeseen erikseen, jokainen palaute käsitellään huolellisesti ja välitetään oikeille asiantuntijoille. Näin varmistamme, että asiakkaidemme ideat ja toiveet otetaan huomioon palvelujemme kehittämisessä.</p>
    <p>Luemme jokaisen saamamme palautteen, mutta emme vastaa niihin suoraan. Jos sinulla on kysymyksiä, lähetä meille viesti sosiaalisessa mediassa:</p>
    <div class="socialmediaicons">
            <!-- font awesome icons -->
            <a href="#" class="fa fa-facebook"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"/></svg></a>
            <a href="#" class="fa fa-x-twitter"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg></a>
            <a href="#" class="fa fa-instagram"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg></a>
            <a href="#" class="fa fa-tiktok"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/></svg></a>
          </div>

<div class="lomaketulokset"><?=$output?></div>
<!--form method="POST" class="needs-validation" novalidate-->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="projektitoriform loginform" novalidate>
  <fieldset>

    <div class="mb-3 <?= isset($value['feedback_text']) ? 'touched' : ''; ?>">
        <textarea name="feedback_text" class="form-control <?= isset($errors['feedback_text']) ? 'is-invalid' : ''; ?> <?= isset($value['feedback_text']) ? 'touched' : ''; ?>" id="feedback_text" rows="5" required><?= isset($value['feedback_text']) ? htmlspecialchars($value['feedback_text']) : ''; ?></textarea>
        <label for="feedback_text" class="form-label inputplaceholder">Palautteesi</label>
        <div class="invalid-feedback"><?= $errors['feedback_text'] ?? ""; ?></div>
    </div>

    <!-- Arvosana -->
    <div class="mb-3 <?= isset($value['rating']) ? 'touched' : ''; ?>">
        <label for="rating">Arvioi palvelu (1-5):</label>
        <select id="rating" name="rating"  class="form-select form-select-lg <?= isset($errors['rating']) ? 'is-invalid' : ''; ?>" required>
            <!--option value="" disabled selected>Valitse</option-->  
            <option value="" disabled <?= !isset($value['rating']) ? 'selected' : ''; ?>>Valitse</option>  
            <option value="1" <?= isset($value['rating']) && $value['rating'] == '1' ? 'selected' : ''; ?>>1 - Huono</option>
            <option value="2" <?= isset($value['rating']) && $value['rating'] == '2' ? 'selected' : ''; ?>>2</option>
            <option value="3" <?= isset($value['rating']) && $value['rating'] == '3' ? 'selected' : ''; ?>>3 - Hyvä</option>
            <option value="4" <?= isset($value['rating']) && $value['rating'] == '4' ? 'selected' : ''; ?>>4</option>
            <option value="5" <?= isset($value['rating']) && $value['rating'] == '5' ? 'selected' : ''; ?>>5 - Erinomainen</option>
        </select>
        <div class="invalid-feedback"><?= $errors['rating'] ?? ""; ?></div>
    </div>


        <!-- Sähköposti -->
        <div class="mb-3 <?= isset($value['email']) ? 'touched' : ''; ?>">
            <input type="email" id="email" name="email" required class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : ''; ?> <?= isset($value['email']) ? 'touched' : ''; ?>" autocomplete="email" value="<?= isset($value['email']) ? htmlspecialchars($value['email']) : ''; ?>">
            <label for="email" class="label-responsive inputplaceholder">Sähköposti</label>
            <div class="invalid-feedback"><?= $errors['email'] ?? ""; ?></div>
        </div>

    <div class="mb-3 <?= isset($value['name']) ? 'touched' : ''; ?>">
        <input type="text" id="name" name="name" required autocomplete="name" class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : ''; ?> <?= isset($value['name']) ? 'touched' : ''; ?>" value="<?= isset($value['name']) ? htmlspecialchars($value['name']) : ''; ?>">
        <label for="name" class="label-responsive inputplaceholder">Nimi</label>
        <div class="invalid-feedback"><?= $errors['name'] ?? ""; ?></div>
    </div>
    
  </fieldset>
    
  <button type="submit" class="btn btn-primary">Lähetä palaute</button>
</form>

</div>

<?php include 'footer.php'; ?>
</body>
</html>


