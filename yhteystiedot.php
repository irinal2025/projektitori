<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';

$output = '';


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty($_POST['contact_message']) || strlen(trim($_POST['contact_message'])) < 2) {
            $errors['contact_message'] = "Palautteen on oltava vähintään 2 merkkiä pitkä.";
            $is_invalid['contact_message'] = "is-invalid"; // Merkitään kenttä virheelliseksi
        } else {
            // Jos palaute on oikein, puhdistetaan se ennen tallennusta
            $value['contact_message'] = trim($_POST['contact_message']);
            $contact_message = trim($_POST['contact_message']);
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
            $message .= "<h2>Uusi viesti:</h2>";
            $message .= "<p><strong>Viesti:</strong> " . nl2br(htmlspecialchars($contact_message)) . "</p>";
            $message .= "<p><strong>Nimi:</strong> " . nl2br(htmlspecialchars($name)) . "</p>";
            $message .= "<p><strong>Sähköposti:</strong> " . nl2br(htmlspecialchars($email)) . "</p>";
            $message .= "</body></html>";

            $mail->Body = $message;  // Aseta sähköpostin runko

            // Lähetä sähköposti
            $mail->send();
            ///echo "Kiitos viestistäsi! Viesti on lähetetty.";
            $output = '<div class="output-success"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Kiitos viestistäsi! Viesti on lähetetty.</div>';
            $value['contact_message'] = null;
            $value['name'] = null;
            $value['email'] = null;
        } catch (Exception $e) {
            //echo "Viestin lähettäminen epäonnistui. Virhe: {$mail->ErrorInfo}";
            $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Viestin lähettäminen epäonnistui. Virhe: {$mail->ErrorInfo}.</div>';
        }
    }
}




?>

<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Yhteystiedot - Projektitori";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
?>
<body class="feedbackpage">
<?php include 'nav.php'; ?>

<!--
<header>
    <h1>Yhteystiedot</h1>
</header>
-->

<div class="container">

<main>
    <section>
        <h1>Ota meihin yhteyttä</h1>
        <!--h2>Ota meihin yhteyttä</h2-->
        <p>Jos sinulla on kysyttävää tai tarvitset lisätietoja palveluistamme, täytä alla oleva lomake tai lähetä sähköpostia osoitteeseen:</p>
        <p><strong>sahkoposti(at)projektitori.fi</strong></p>

        <p>Tiimimme on täällä auttamassa kaikissa palveluihimme liittyvissä kysymyksissä. Täytä lomake, niin palaamme sinulle mahdollisimman pian.</p>
        <p>Jos haluat antaa palautetta palveluistamme, siirry <a href="palaute.php" class="view-more">palautesivulle &raquo;</a></p>
        <div class="lomaketulokset"><?=$output?></div>
        <!--form method="POST" class="needs-validation" novalidate-->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="projektitoriform loginform" novalidate>

          <div class="mb-3 <?= isset($value['name']) ? 'touched' : ''; ?>">
              <input type="text" id="name" name="name" required autocomplete="name" class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : ''; ?> <?= isset($value['name']) ? 'touched' : ''; ?>" value="<?= isset($value['name']) ? htmlspecialchars($value['name']) : ''; ?>">
              <label for="name" class="label-responsive inputplaceholder">Nimi</label>
              <div class="invalid-feedback"><?= $errors['name'] ?? ""; ?></div>
          </div>

            <!-- Sähköposti -->
            <div class="mb-3 <?= isset($value['email']) ? 'touched' : ''; ?>">
                <input type="email" id="email" name="email" required class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : ''; ?> <?= isset($value['email']) ? 'touched' : ''; ?>" autocomplete="email" value="<?= isset($value['email']) ? htmlspecialchars($value['email']) : ''; ?>">
                <label for="email" class="label-responsive inputplaceholder">Sähköposti</label>
                <div class="invalid-feedback"><?= $errors['email'] ?? ""; ?></div>
            </div>

          <div class="mb-3 <?= isset($value['contact_message']) ? 'touched' : ''; ?>">
              <textarea name="contact_message" class="form-control <?= isset($errors['contact_message']) ? 'is-invalid' : ''; ?> <?= isset($value['contact_message']) ? 'touched' : ''; ?>" id="contact_message" rows="5" required><?= isset($value['contact_message']) ? htmlspecialchars($value['contact_message']) : ''; ?></textarea>
              <label for="contact_message" class="form-label inputplaceholder">Viesti</label>
              <div class="invalid-feedback"><?= $errors['contact_message'] ?? ""; ?></div>
          </div>

            <button type="submit" class="btn btn-primary">Lähetä viesti</button>
        </form>
    </section>
</main>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
