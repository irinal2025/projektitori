<?php
$output = '';
$modal = '';

// Muuttuja virheille
$errors = [];
$is_invalid = [];
$value = [];

// Tietokantayhteys
$tietokanta = "projektitori";
include 'debuggeri.php';
include 'tietokantarutiinit.php';
register_shutdown_function('debuggeri_shutdown');


// Aloitetaan sessio, jotta voidaan käyttää sessioita
if (!session_id()) session_start();

// Tarkistetaan onko käyttäjä kirjautunut sisään
if (isset($_SESSION['user_id'])) {
    // Jos ei ole kirjautunut, ohjataan kirjautumissivulle
    header("Location: kirjauduulos.php");
    exit();
}

// Tarkista, onko lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Sähköpostikenttä tyhjä ja onko sähköpostiosoite kelvollinen
    if (empty($_POST['email'])) {
        $errors['email'] = "Sähköpostiosoite on pakollinen.";
        $is_invalid['email'] = "is-invalid"; // Merkitään kenttä virheelliseksi
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Anna kelvollinen sähköpostiosoite.";
        $is_invalid['email'] = "is-invalid"; // Merkitään kenttä virheelliseksi
    } else {
        $value['email'] = $yhteys->real_escape_string(trim($_POST['email']));
    }

    // Tarkistetaan, että salasana täyttää vaatimukset
    if (empty($_POST['password'])) {
        $errors['password'] = "Salasana on pakollinen.";
        $is_invalid['password'] = "is-invalid"; // Merkitään kenttä virheelliseksi
    } else {
        $password = $_POST['password'];

        // Tarkistetaan, että salasana täyttää perusvaatimukset, mutta ei kerrota tarkemmin virheistä
        if (strlen($password) < 8 || strlen($password) > 20 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || preg_match('/\s|[^A-Za-z0-9]/', $password)) {
            $errors['email'] = " ";
            $errors['password'] = " ";
            $is_invalid['email'] = "is-invalid"; // Merkitään kenttä virheelliseksi
            $is_invalid['password'] = "is-invalid"; // Merkitään kenttä virheelliseksi
            $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virheellinen sähköposti tai salasana.</div>';
        } else {
            // Puhdistetaan salasana ennen tarkistusta
            $clean_password = trim($password);
        }
    }  


    // Jos ei ole virheitä, tarkistetaan käyttäjän tiedot tietokannasta
    if (empty($errors)) {
        // SQL-kyselyt käyttäjän tarkistamista varten
        $query = "SELECT user_id, password_hash, user_type, email_verified FROM users WHERE email = ?";
        $stmt = $yhteys->prepare($query);
        $stmt->bind_param('s', $_POST['email']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Käyttäjä löytyy
            $stmt->bind_result($user_id, $hashed_password, $user_type, $email_verified);
            $stmt->fetch();
        
            if ($email_verified == 0) {
                $output = '<div class="output-alert">Vahvista sähköpostiosoitteesi ennen sisäänkirjautumista.</div>';
            } elseif (password_verify($clean_password, $hashed_password)) {        
            // Vertaillaan salasanoja
                // Kirjautuminen onnistui
                session_start();

                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $value['email'];
                $_SESSION['user_type'] = $user_type;  // käyttäjän rooli (esim. 'student' tai 'provider')
                $_SESSION['logged_in'] = true;        // Asetetaan 'logged_in' true arvoksi
                
                
                // Redirect käyttäjän ohjaus eteenpäin
                header("Location: profiili.php"); 
                exit();
            } else {
                // Salasana virheellinen
                $errors['email'] = " ";
                $errors['password'] = " ";
                $is_invalid['email'] = "is-invalid";
                $is_invalid['password'] = "is-invalid";
                $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virheellinen sähköposti tai salasana. <a href="resetpassword.php">Unohtuiko salasana?</a></div>';
            }
        } else {
            // Käyttäjää ei löydy, kehotetaan rekisteröitymään
            //$errors['email'] = "";
            //$is_invalid['email'] = "is-invalid";
            $output = '<div class="output-info"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Sähköpostiosoitetta ei löytynyt. <a href="rekisteroidy.php">Luo tili</a>.</div>';
            
        }
    }

    // Jos virheitä on, näytetään ne
    /*if (!empty($errors)) {
        $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virheellinen sähköposti tai salasana.</div>';
    }*/

}


?>
<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "Kirjaudu - Projektitori";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body class="loginpage">

<?php include 'nav.php'; ?>


<div id="container" class="container conta2iner">
<h1>Kirjaudu sisään</h1>

<div class="lomaketulokset"><?=$output?></div>
<!--form method="POST" class="needs-validation" novalidate-->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="projektitoriform loginform" novalidate>
  <fieldset>
        <!-- Sähköposti -->
        <div class="mb-3">
            <input type="email" id="email" name="email" required class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : ''; ?>" autocomplete="email">
            <label for="email" class="label-responsive inputplaceholder">Sähköposti</label>
            <div class="invalid-feedback"><?= $errors['email'] ?? ""; ?></div>
        </div>

        <!-- Salasana -->
        <div class="mb-3 input-group">
            <input type="password" id="password" name="password" minlength="8" required class="form-control form-control-lg <?= isset($errors['password']) ? 'is-invalid' : ''; ?>" autocomplete="current-password">
            <label for="password" class="label-responsive inputplaceholder">Salasana</label>
            <!-- An element to toggle between password visibility -->
            <span class="input-group-text">
                <i class="icon-eye-blocked" id="togglePassword"></i>
            </span>
            <div class="invalid-feedback"><?= $errors['password'] ?? ""; ?></div>
        </div>
   </fieldset>
        <!-- Kirjaudu sisään painike -->
        <button type="submit" class="btn btn-primary">Kirjaudu sisään</button>
    </form>

    <p><a href="unohdettu_salasana.php">Unohditko salasanan?</a></p>
    <p>Ei ole vielä tiliä? <a href="rekisteroidy.php">Rekisteröidy täällä</a></p>

    </div>


 <?php 
 include 'footer.php';
 ?>


</body>
</html>