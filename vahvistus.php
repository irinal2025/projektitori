<?php
$output = '';

if (isset($_GET['token']) && isset($_GET['email'])) {
    // Tietokantayhteys
    $tietokanta = "projektitori";
    include 'tietokantarutiinit.php';

    $token = $_GET['token'];
    $email = $_GET['email'];

    // Tarkistetaan, että token ja sähköposti täsmäävät tietokannassa
    $query = "SELECT user_id FROM users WHERE email = ? AND verification_token = ?";
    $stmt = $yhteys->prepare($query);
    $stmt->bind_param('ss', $email, $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        // Token on validi, päivitetään käyttäjän tilin status
        $update_query = "UPDATE users SET email_verified = 1, verification_token = NULL WHERE email = ?";
        $update_stmt = $yhteys->prepare($update_query);
        $update_stmt->bind_param('s', $email);
        $update_stmt->execute();

        echo "Sähköpostiosoite on vahvistettu! Voit nyt kirjautua sisään.";
        $output = 'Sähköpostiosoite on vahvistettu! Voit nyt kirjautua sisään.';
    } else {
        echo "Vahvistuslinkki on virheellinen tai vanhentunut.";
        $output = 'Vahvistuslinkki on virheellinen tai vanhentunut.';
    }
} else {
    echo "Virheellinen pyyntö.";
    $output = 'Virheellinen pyyntö.';
}
?>


<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Sähköpostin vahvistus - Projektitori";
    include 'head.php'; 
?>
<body>
<?php include 'nav.php'; ?>

<header>
<h1>Sähköpostin vahvistus</h1>
</header>

<main>
<div class="lomaketulokset"><?=$output?></div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
