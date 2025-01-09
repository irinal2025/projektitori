<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//require '../../PHPMailer/src/Exception.php';
//require '../../PHPMailer/src/PHPMailer.php';
//require '../../PHPMailer/src/SMTP.php';

require 'external/Exception.php';
require 'external/PHPMailer.php';
require 'external/SMTP.php';

$output = '';

// Muuttuja virheille
$errors = [];
$is_invalid = [];
$pattern = [];
$value = [];


$tietokanta = "projektitori";
include 'debuggeri.php';
include 'tietokantarutiinit.php';
register_shutdown_function('debuggeri_shutdown');


// Tarkista, onko lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Etunimi::
    if (empty($_POST['first_name']) || strlen(trim($_POST['first_name'])) < 2) {
        // Virheilmoitus, jos kenttä on tyhjä tai nimi on alle 2 merkkiä pitkä
        $errors['first_name'] = "Etunimen on oltava vähintään 2 merkkiä pitkä.";
        $is_invalid['first_name'] = "is-invalid"; // Merkitään kenttä virheelliseksi
    } elseif (!preg_match("/^[a-zA-ZåäöÅÄÖ\-']{2,50}$/", $_POST['first_name'])) {
        // Virheilmoitus, jos etunimessä on kiellettyjä merkkejä
        $errors['first_name'] = "Etunimessä saa olla vain kirjaimia ja tavuviiva.";
        $is_invalid['first_name'] = "is-invalid";
    } else {
        // Jos etunimi on oikein, puhdistetaan se ennen tallennusta
        $value['first_name'] = trim($_POST['first_name']);
    }
    
    // Sukunimi:
    if (empty($_POST['last_name']) || strlen(trim($_POST['last_name'])) < 2) {
        // Virheilmoitus, jos kenttä on tyhjä tai nimi on alle 2 merkkiä pitkä
        $errors['last_name'] = "Sukunimen on oltava vähintään 2 merkkiä pitkä.";
        $is_invalid['last_name'] = "is-invalid"; // Merkitään kenttä virheelliseksi
    } elseif (!preg_match("/^[a-zA-ZåäöÅÄÖ\-']{2,50}$/", $_POST['last_name'])) {
        // Virheilmoitus, jos sukunimessä on kiellettyjä merkkejä
        $errors['last_name'] = "Sukunimessä saa olla vain kirjaimia ja tavuviiva.";
        $is_invalid['last_name'] = "is-invalid";
    } else {
        // Jos etunimi on oikein, puhdistetaan se ennen tallennusta
        $value['last_name'] = trim($_POST['last_name']);
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
        $email = $yhteys->real_escape_string(trim($_POST['email']));

        // Tarkistetaan, onko sähköpostiosoite jo käytössä
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $yhteys->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jos sähköposti löytyy, näytetään virhe
            $errors['email'] = "Sähköpostiosoite on jo käytössä.";
            $is_invalid['email'] = "is-invalid";
        } else {
            // Jos sähköposti ei ole käytössä, puhdistetaan ja tallennetaan se
            $value['email'] = $email;
        }
    }


        // Tarkista salasana
        if (empty($_POST['password'])) {
            $errors['password'] = "Salasana on pakollinen.";
            $is_invalid['password'] = "is-invalid"; // Merkitään kenttä virheelliseksi
        } else {
            $password = $_POST['password'];

            // Tarkista, että pituus on 8-20 merkkiä
            if (strlen($password) < 8 || strlen($password) > 20) {
                $errors['password'] = "Salasanan on oltava 8-20 merkkiä pitkä.";
                $is_invalid['password'] = "is-invalid"; // Merkitään kenttä virheelliseksi
            }

            // Tarkista, että sisältää kirjaimia ja numeroita
            if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $errors['password'] = "Salasanan on sisällettävä sekä kirjaimia että numeroita.";
                $is_invalid['password'] = "is-invalid"; // Merkitään kenttä virheelliseksi
            }

            // Tarkista, ettei sisällä välilyöntejä, erikoismerkkejä tai emojeja
            if (preg_match('/\s|[^A-Za-z0-9]/', $password)) {
                $errors['password'] = "Salasana ei saa sisältää välilyöntejä, erikoismerkkejä tai emojeja.";
                $is_invalid['password'] = "is-invalid"; // Merkitään kenttä virheelliseksi
            }

            // Tarkista, että vahvistus salasana on olemassa ja täsmää
            if (empty($_POST['password2'])) {
                $errors['password2'] = "Salasanan vahvistus on pakollinen.";
                $is_invalid['password2'] = "is-invalid";
            } elseif ($_POST['password'] !== $_POST['password2']) {
                // Jos salasanat eivät täsmää, näytetään virhe
                $errors['password'] = "Salasanat eivät täsmää.";
                $is_invalid['password'] = "is-invalid";
                $is_invalid['password2'] = "is-invalid";
            }

            // Jos salasana täyttää kaikki ehdot ja salasanat täsmäävät
            if (!isset($errors['password']) && !isset($errors['password2'])) {
                // Puhdistetaan salasana
                $clean_password = trim($password); // Puhdistetaan mahdolliset ympärillä olevat välilyönnit

                // Hashataan salasana turvalliseen tallennukseen
                $hashed_password = password_hash($clean_password, PASSWORD_DEFAULT);

                // Tämän jälkeen voit tallentaa $hashed_password tietokantaan
                // Esimerkki:
                // $query = "INSERT INTO users (password) VALUES ('$hashed_password')";
            }
        }


    // Rooli
    if (empty($_POST['user_type'])) {
        $errors['user_type'] = "Rooli on pakollinen.";
        $is_invalid['user_type'] = "is-invalid";
    } else {
        $user_type = $yhteys->real_escape_string($_POST['user_type']);
    
        // Tarkista, että rooli on joko 'student' tai 'provider'
        if (!in_array($user_type, ['student', 'provider'])) {
            $errors['user_type'] = "Valitse kelvollinen rooli (opiskelija tai projektin tarjoaja).";
            $is_invalid['user_type'] = "is-invalid";
        } else {
            $value['user_type'] = $user_type; // Syöte on validi, joten tallennetaan se
        }
    }
    


    // Jos virheitä ei ole, suoritetaan lisäys tietokantaan
    if (empty($errors)) {
 
         // Uniikin vahvistuskoodin luonti
         $verification_token = bin2hex(random_bytes(50));

        // Oletetaan, että yhteys tietokantaan on avattu
        //require 'database_connection.php'; ??

        // SQL-kysely käyttäjän lisäämiseksi
        $query = "INSERT INTO users 
        (email, password_hash, user_type, verification_token) 
        VALUES (?, ?, ?, ?)";

        // SQL-kysely
        $query = "INSERT INTO users 
            (user_type, first_name, last_name, email, password_hash, verification_token, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $yhteys->prepare($query);

        //Syötteiden "siistimisestä":
        $user_type = $yhteys->real_escape_string(strip_tags($_POST["user_type"]));
        $first_name = $yhteys->real_escape_string(strip_tags($_POST["first_name"]));
        $last_name = $yhteys->real_escape_string(strip_tags($_POST["last_name"]));
        $email = $yhteys->real_escape_string(strip_tags($_POST["email"]));
        $password_hash = $hashed_password;

/*
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('student', 'provider') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/

        // Tässä vaiheessa varmistamme oikeat tyypit:
        // - s = string (varchar, text, enum)
        // - i = integer (year, tinyint, smallint)
        // - d = double (decimal)
        $stmt->bind_param(
            'ssssss',  // tyyppien määritys: string, string, string, string, string
            $user_type, //rooli enum s
            $first_name,
            $last_name,
            $email,
            $password_hash,
            $verification_token
        );

        // Suorita kysely
        $stmt->execute();

        // Sähköpostivahvistuksen lähettäminen
            // SMTP-asetukset PHPMailerille
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();                                      // Käytä SMTP:ta
                $mail->Host       = 'smtp.gmail.com';                 // SMTP-palvelin (muuta omaan SMTP-palvelimeesi)
                $mail->SMTPAuth   = true;                             // Ota käyttöön SMTP-autentikointi
                $mail->Username   = 'projektitori@gmail.com';             // SMTP-käyttäjänimi (sähköpostisi)
                $mail->Password   = 'nqis vvzw rjqw vjcj';                     // SMTP-salasana (käytä sovelluskohtaisia salasanoja, jos käytät Gmailia)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Ota käyttöön TLS-salaus, ssl myös mahdollista
                $mail->Port       = 587;                              // SMTP-portti, 587 TLS:lle tai 465 SSL:lle
                
                // Merkistön asetus
                $mail->CharSet = 'UTF-8';  // Ota käyttöön UTF-8-merkistö

                // Vastaanottaja ja lähettäjä
                $mail->setFrom('projektitori@mailinator.com', 'Projektitori'); // Lähettäjän osoite
                $mail->addAddress($_POST['email']);                   // Vastaanottajan osoite

                // Sähköpostiviestin sisältö
                //$verify_url = "http://yourdomain.com/vahvistus.php?token=" . $verification_token . "&email=" . urlencode($_POST['email']);
                if (strpos($_SERVER['HTTP_HOST'],"azurewebsites") !== false){
                $verify_url = "https://lisovskajair-dpg9bxf9awh8cae5.westeurope-01.azurewebsites.net/vahvistus.php?token=" . $verification_token . "&email=" . urlencode($_POST['email']);
                }  else {
                    $verify_url = "http://yourdomain.com/projektitori/vahvistus.php?token=" . $verification_token . "&email=" . urlencode($_POST['email']);
                }
                $mail->isHTML(true);                                  // Asetetaan HTML viestin tyypiksi
                $mail->Subject = 'Vahvista sähköpostiosoitteesi';
                $mail->Body    = 'Klikkaa tätä linkkiä vahvistaaksesi sähköpostisi: <a href="' . $verify_url . '">Vahvista sähköposti</a>';
                $mail->AltBody = 'Klikkaa tätä linkkiä vahvistaaksesi sähköpostisi: ' . $verify_url; // Tekstiversio

                $mail->send();
                $output = '<div class="output-success"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Rekisteröityminen onnistui. Tarkista sähköpostisi vahvistaaksesi tilin.</div>';

                $value['user_type'] = null;
                $value['first_name'] = null;
                $value['last_name'] = null;
                $value['email'] = null;
                $value['user_type'] = null;
                $value['password'] = null;
                $value['password2'] = null;
            } catch (Exception $e) {
                $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Vahvistussähköpostin lähetys epäonnistui. Virhe: ' . $mail->ErrorInfo . '</div>';
            }


    } else {
        $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virheitä löytyi. Tarkista syötteet.</div>';
    }
    
}
?>
<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Rekisteröidy - Projektitori";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body  class="loginpage">

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Rekisteröidy</h1>
    <p>Rekisteröidy Projektitoriin ja löydä projekteja tai tekijöitä!</p>
    <p>Rekisteröityminen on ilmaista ja helppoa.</p>

    <div class="lomaketulokset"><?=$output?></div>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="projektitoriform addnewuser" novalidate>
    <!--form action="rekisteroidy.php" method="POST" class="needs-validation" novalidate-->
    <fieldset>
        <!-- Etunimi -->
        <div class="mb-3 <?= isset($value['first_name']) ? 'touched' : ''; ?>">
            <input type="text" id="first_name" name="first_name" required autocomplete="given-name" class="form-control form-control-lg <?= isset($errors['first_name']) ? 'is-invalid' : ''; ?> <?= isset($value['first_name']) ? 'touched' : ''; ?>" value="<?= isset($value['first_name']) ? htmlspecialchars($value['first_name']) : ''; ?>">
            <label for="first_name"  class="label-responsive inputplaceholder">Etunimi</label>
            <div class="invalid-feedback"><?= $errors['first_name'] ?? ""; // Näytä virheviesti jos on olemassa ?></div>
        </div>

        <!-- Sukunimi -->
        <div class="mb-3 <?= isset($value['last_name']) ? 'touched' : ''; ?>">
            <input type="text" id="last_name" name="last_name" required autocomplete="family-name" class="form-control form-control-lg <?= isset($errors['last_name']) ? 'is-invalid' : ''; ?> <?= isset($value['last_name']) ? 'touched' : ''; ?>" value="<?= isset($value['last_name']) ? htmlspecialchars($value['last_name']) : ''; ?>">
            <label for="last_name"  class="label-responsive inputplaceholder">Sukunimi</label>
            <div class="invalid-feedback"><?= $errors['last_name'] ?? ""; // Näytä virheviesti jos on olemassa ?></div>
        </div>

        <!-- Sähköposti -->
        <div class="mb-3 <?= isset($value['email']) ? 'touched' : ''; ?>">
            <!--input type="email" id="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required class="form-control form-control-lg" autocomplete="email"-->
            <input type="email" id="email" name="email" required class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : ''; ?> <?= isset($value['email']) ? 'touched' : ''; ?>" autocomplete="email" value="<?= isset($value['email']) ? htmlspecialchars($value['email']) : ''; ?>">
            <label for="email" class="label-responsive inputplaceholder">Sähköposti</label>
            <div class="invalid-feedback"><?= $errors['email'] ?? ""; // Näytä virheviesti jos on olemassa ?></div>
        </div>

        <!-- Salasana -->
        <div class="mb-3 input-group <?= isset($value['password']) ? 'touched' : ''; ?>">
            <input type="password" id="password" name="password" minlength="8" required class="form-control form-control-lg <?= isset($errors['password']) ? 'is-invalid' : ''; ?> <?= isset($value['password']) ? 'touched' : ''; ?>" autocomplete="new-password">
            <label for="password" class="label-responsive inputplaceholder">Salasana</label>
            <!-- An element to toggle between password visibility -->
            <span class="input-group-text">
                <i class="icon-eye-blocked" id="togglePassword"></i>
            </span>
            <!--div id="passwordHelpBlock" class="form-text">Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.</div-->
            <div class="invalid-feedback"><?= $errors['password'] ?? ""; // Näytä virheviesti jos on olemassa ?></div>
        </div>

        <!-- Salasana2 -->
        <div class="mb-3 input-group <?= isset($value['password2']) ? 'touched' : ''; ?>">
            <input type="password" id="password2" name="password2" minlength="8" required class="form-control form-control-lg <?= isset($errors['password2']) ? 'is-invalid' : ''; ?> <?= isset($value['password2']) ? 'touched' : ''; ?>" autocomplete="new-password">
            <label for="password2" class="label-responsive inputplaceholder">Salasana uudestaan</label>
            <!-- An element to toggle between password visibility -->
            <span class="input-group-text">
                <i class="icon-eye-blocked" id="togglePassword2"></i>
            </span>
            <div class="invalid-feedback"><?= $errors['password2'] ?? ""; // Näytä virheviesti jos on olemassa ?></div>
            <div id="passwordHelpBlock" class="form-text">Salasanasi tulee olla 8-20 merkkiä pitkä, sisältää kirjaimia ja numeroita, eikä se saa sisältää välilyöntejä, erikoismerkkejä tai emojeja.</div>
        </div>

        <!-- Roolin valinta -->
        <div class="mb-3 <?= isset($value['user_type']) ? 'touched' : ''; ?>">
            <label for="user_type">Rekisteröidy roolissa:</label>
            <select id="user_type" name="user_type"  class="form-select <?= isset($errors['user_type']) ? 'is-invalid' : ''; ?>" required>
                <!--option value="" disabled selected>Valitse</option-->  
                <option value="" disabled <?= isset($value['user_type']) ? '' : 'selected'; ?>>Valitse rooli</option>  
                <option value="student" <?= isset($value['user_type']) && $value['user_type'] == 'student' ? 'selected' : ''; ?>>Opiskelija</option>
                <option value="provider" <?= isset($value['user_type']) && $value['user_type'] == 'provider' ? 'selected' : ''; ?>>Projektin tarjoaja</option>
            </select>
            <div class="invalid-feedback"><?= $errors['user_type'] ?? ""; ?></div>
        </div>

</fieldset>
        <!-- Rekisteröidy painike -->
        <button type="submit" class="btn btn-primary">Rekisteröidy</button>
        <!--input type="submit" class="btn btn-primary" value="Rekisteröidy" disabled-->
    </form>
</div>

<?php 
 include 'footer.php';
 ?>

</body>
</html>