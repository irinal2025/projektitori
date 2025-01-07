<?php
$output = '';

// Aloitetaan sessio, jotta voidaan käyttää sessioita
if (!session_id()) session_start();
//$output = 'User ID: ' . $_SESSION['user_id'] . '<br>';
//$output .= 'User Type: ' . $_SESSION['user_type'] . '<br>';
//$output .= 'logged_in: ' . $_SESSION['logged_in'] . '<br>';
//$output .= 'project ID: ' . $_GET['projekti'] . '<br>';

//require 'db_connection.php'; // Yhdistä tietokantaan

$tietokanta = "projektitori";
include 'debuggeri.php';
include 'tietokantarutiinit.php';
register_shutdown_function('debuggeri_shutdown');


// Varmistetaan, että käyttäjä on kirjautunut sisään
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] != 'student') {
    //echo "Vain opiskelijat voivat hakea projekteja.";
    //header('Location: kirjaudu.php'); // Ohjataan kirjautumissivulle, jos käyttäjä ei ole kirjautunut
    //exit;
}

// Haetaan projektin ID URL:sta
if (isset($_GET['projekti'])) {
    $project_id = intval($_GET['projekti']);

    // Haetaan projektin tiedot ja kategoria tietokannasta
    $stmt = $yhteys->prepare('
        SELECT p.*, c.category_name 
        FROM projects p
        LEFT JOIN project_categories pc ON p.project_id = pc.project_id
        LEFT JOIN categories c ON pc.category_id = c.category_id
        WHERE p.project_id = ?');

    $stmt->bind_param('i', $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Tarkistetaan, onko projekti olemassa
    if ($result->num_rows === 1) {
        $project = $result->fetch_assoc();
    } else {
        echo "Projektia ei löytynyt.";
        $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Projektia ei löytynyt.</div>';
        //exit;
    }
} else {
    echo "Virheellinen projektin tunniste.";
    $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virheellinen projektin tunniste.</div>';
    //exit;
}

// Tarkistetaan, onko käyttäjä jo lähettänyt hakemuksen
$student_id = $_SESSION['user_id'];
$check_application_sql = 'SELECT * FROM applications WHERE project_id = ? AND student_id = ?';
$stmt_check = $yhteys->prepare($check_application_sql);
$stmt_check->bind_param('ii', $project_id, $student_id);
$stmt_check->execute();
$check_result = $stmt_check->get_result();

if ($check_result->num_rows > 0) {
    // Käyttäjä on jo lähettänyt hakemuksen
    $has_applied = true;
} else {
    $has_applied = false;
}

// Käsitellään hakulomakkeen lähetys
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$has_applied) {
    $student_id = $_SESSION['user_id']; // Käyttäjän ID sessionista
    $motivation = trim($_POST['motivation']); // Puhdistetaan viesti

    // Tarkistetaan, onko viesti tyhjä
    if (empty($motivation)) {
        $error = "Viesti on pakollinen.";
    } else {
        // Tallennetaan hakemus tietokantaan
        $stmt = $yhteys->prepare('INSERT INTO applications (project_id, student_id, motivation) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $project_id, $student_id, $motivation);

        if ($stmt->execute()) {
            $success = "Hakemuksesi on lähetetty onnistuneesti.";
        } else {
            $error = "Hakemuksen lähettäminen epäonnistui. Yritä myöhemmin uudelleen.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Hae tätä projektia ";
    $css = "css/forms.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Hae tätä projektia</h1>
</header>

<main>
    <?php if (!isset($_GET['project_id'])): ?>
        <div class="lomaketulokset"><?=$output?></div>
    <?php endif; ?>
    <?php if (isset($_GET['projekti'])): ?>
    <!-- Näytetään projektin tiedot -->
    <section class="project-details">
        <h2><?php echo htmlspecialchars($project['project_name']); ?></h2>
        <p><strong>Kuvaus:</strong> <?php echo htmlspecialchars($project['description']); ?></p>
        <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($project['category_name']); ?></p>
        <p><strong>Taitotaso:</strong> <?php echo htmlspecialchars($project['skill_level']); ?></p>
        <p><strong>Vaaditut taidot:</strong> <?php echo htmlspecialchars($project['required_skills']); ?></p>
        <p><strong>Sijainti:</strong> <?php echo htmlspecialchars($project['location']); ?> <?php echo htmlspecialchars($project['city'] ?? ''); ?></p>
        <p><strong>Palkkio:</strong> <?php echo htmlspecialchars($project['compensation']); ?></p>
        <p><strong>Hyödyt opiskelijalle:</strong> <?php echo htmlspecialchars($project['student_benefits']); ?></p>
    </section>
 
    <!-- Hakulomake -->
    <section class="apply-section">
    <?php if (isset($success)) { ?>
            <p class="success"><?php echo $success; ?></p>
        <?php } elseif (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <!-- Lomake näkyy vain, jos hakemusta ei ole vielä lähetetty -->
        <?php if (!$has_applied): ?>
            <form action="hakemus.php?projekti=<?php echo $project_id; ?>" method="POST">
                <div class="form-group">
                    <label for="motivation">Viesti projektin tarjoajalle:</label>
                    <textarea id="motivation" name="motivation" rows="5" required class="form-control"><?php echo isset($motivation) ? htmlspecialchars($motivation) : ''; ?></textarea>
                </div>
                <button class="submit-button btn btn-primary" type="submit">Lähetä hakemus</button>
            </form>
        <?php else: ?>
            <!-- Viesti, jos hakemus on jo lähetetty -->
            <div class="apply-section-message"><p><strong>Olet jo lähettänyt hakemuksen tälle projektille.</strong></p></div>
        <?php endif; ?>
    </section>

    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
