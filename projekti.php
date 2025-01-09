<?php
$outputLink = "";
$outputText = "";

$hasApplied = false; // Oletetaan, ettei hakemusta ole, ennen kuin se tarkistetaan

// Aloitetaan sessio, jotta voidaan käyttää sessioita
if (!session_id()) session_start();

// Tarkistetaan onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    // Jos ei ole kirjautunut, ohjataan rekisteröitymissivulle
    $outputLink = "rekisteroidy.php";
    $outputText = "Rekisteröidy ja jätä hakemus tälle projektille";
}


if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student') {
    // Tarkistetaan, onko opiskelija jo lähettänyt hakemuksen
    $outputLink = "hakemus.php";
    $outputText = "Hae tätä projektia";

    // Yhdistetään tietokantaan ja tarkistetaan hakemukset
    $servername = "localhost";
    $username = "root";  // Vaihda käyttäjätunnukseen
    $password = "";      // Vaihda salasanaan
    $dbname = "projektitori";  // Oikea tietokanta

    $yhteys = new mysqli($servername, $username, $password, $dbname);

    // Tarkista yhteys
    if ($yhteys->connect_error) {
        die("Yhteyden muodostaminen epäonnistui: " . $yhteys->connect_error);
    }

    // Tarkistetaan, onko opiskelija jo lähettänyt hakemuksen
    $student_id = $_SESSION['user_id'];
    $project_id = $_GET['projekti'];
    $check_application_sql = "SELECT * FROM applications WHERE student_id = ? AND project_id = ?";
    $stmt_check = $yhteys->prepare($check_application_sql);
    $stmt_check->bind_param("ii", $student_id, $project_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        $hasApplied = true; // Opiskelija on jo lähettänyt hakemuksen
        $outputText = "Olet jo lähettänyt hakemuksen tälle projektille."; // Näytetään tämä viesti
    }

    // Sulje yhteys
    $yhteys->close();
}


if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student') {
    // Jos ei ole kirjautunut, ohjataan rekisteröitymissivulle
    $outputLink = "hakemus.php";
    $outputText = "Hae tätä projektia";
}

if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'provider') {
    // Jos ei ole kirjautunut, ohjataan rekisteröitymissivulle
    $outputLink = "projekti.php";
    $outputText = "Muokkaa projektia";
}

//TÄMÄ ON KESKEN
//$outputText = "Hae tätä projektia";

$tietokanta = "projektitori";
include 'debuggeri.php';


// Tarkista, onko project_id asetettu
if (isset($_GET['projekti'])) {
    $project_id = $_GET['projekti'];
    if (isset($_SESSION['user_id'])) {
        $outputLink .= "?projekti=" . $project_id;
    }

    // Yhdistä tietokantaan
    $servername = "localhost";
    $username = "root";  // Vaihda käyttäjätunnukseen
    $password = "";      // Vaihda salasanaan
    $dbname = "projektitori";  // Oikea tietokanta

    $yhteys = new mysqli($servername, $username, $password, $dbname);

    // Tarkista yhteys
    if ($yhteys->connect_error) {
        die("Yhteyden muodostaminen epäonnistui: " . $yhteys->connect_error);
    }

    // Hae projektin tiedot
    $sql = "
        SELECT p.project_name, p.description, p.skill_level, p.deadline, p.application_deadline, p.location, p.compensation, p.project_duration, p.provider_name, p.required_skills, p.student_benefits, c.category_name AS category_name
        FROM projects p
        LEFT JOIN project_categories pc ON p.project_id = pc.project_id
        LEFT JOIN categories c ON pc.category_id = c.category_id
        WHERE p.project_id = ?
    ";

    $stmt = $yhteys->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Haetaan tulokset
        $project = $result->fetch_assoc();
    } else {
        echo "<p>Projektia ei löytynyt.</p>";
        exit;
    }

    // Hakee hakemukset, jos käyttäjä on tarjoaja
    $applications = [];
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'provider') {
        $applications_sql = "SELECT a.application_id, a.motivation, u.first_name, u.last_name , u.email 
                              FROM applications a
                              JOIN users u ON a.student_id = u.user_id
                              WHERE a.project_id = ?";
        $stmt_applications = $yhteys->prepare($applications_sql);
        $stmt_applications->bind_param("i", $project_id);
        $stmt_applications->execute();
        $app_result = $stmt_applications->get_result();

        while ($row = $app_result->fetch_assoc()) {
            $applications[] = $row;
        }
    }


    // Sulje yhteys
    $yhteys->close();
} else {
    echo "<p>Projektia ei valittu.</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Projektin tiedot: " . $project['project_name'] . " - Projektitori";
    include 'head.php'; 
?>
<body>

<?php include 'nav.php'; ?>

    <header>
        <h1>Projekti: <?php echo $project['project_name']; ?></h1>
    </header>
<main>
    <section>
        <h2>Projektin tiedot</h2>
        <p><strong>Kategoria:</strong> <?php echo $project['category_name']; ?></p>
        <p><strong>Taitotaso:</strong> <?php echo $project['skill_level']; ?></p>
        <p><strong>Deadline:</strong> <?php echo date("j.m.Y", strtotime($project['deadline'])); ?></p>
        <p><strong>Hakemusten viimeinen jättöpäivämäärä:</strong> <?php 
if (!empty($project['application_deadline'])) {
    echo date("j.m.Y", strtotime($project['application_deadline']));
} else {
    echo "Hakemusten jättöaikaa ei ole määritetty.";
}
?></p>
        <p><strong>Projektin tarjoaja:</strong> <?php echo $project['provider_name']; ?></p>
        <p><strong>Sijainti:</strong> <?php echo $project['location']; ?></p>
        <p><strong>Palkkio:</strong> <?php echo $project['compensation'] == 'kyllä' ? 'Kyllä' : 'Ei'; ?></p>

        <h3>Tehtävän kuvaus</h3>
        <p><?php echo $project['description']; ?></p>

        <h3>Vaadittavat taidot</h3>
        <ul>
            <?php
            // Tässä kohtaa oletetaan, että vaaditut taidot on tallennettu pilkulla eroteltuina
            $skills = explode(',', $project['required_skills']);
            foreach ($skills as $skill) {
                echo "<li>" . trim($skill) . "</li>";
            }
            ?>
        </ul>

        <h3>Projektin kesto ja aikataulu</h3>
        <p>
            Projektin arvioitu kesto on <?php echo $project['project_duration']; ?>, ja sen tulee olla valmis 
            <?php echo date("j.m.Y", strtotime($project['deadline'])); ?> mennessä.
        </p>

        <h3>Hyödyt opiskelijalle</h3>
        <ul>
            <?php
            // Tässä kohtaa oletetaan, että hyödyt on tallennettu pilkulla eroteltuina
            $benefits = explode(',', $project['student_benefits']);
            foreach ($benefits as $benefit) {
                echo "<li>" . trim($benefit) . "</li>";
            }
            ?>
        </ul>


    <?php if (!$hasApplied && !isset($_SESSION['user_type'])): ?>
        <a href="<?=$outputLink?>" class="read-more"><?=$outputText?> »</a>
    <?php elseif (!$hasApplied && isset($_SESSION['user_type']) && $_SESSION['user_type'] != 'provider'): ?>
        <a href="<?=$outputLink?>" class="read-more"><?=$outputText?> »</a>
    <?php elseif ($hasApplied): ?>
        <p><strong>Olet jo lähettänyt hakemuksen tälle projektille.</strong></p>
    <?php endif; ?>


<!-- Näytetään hakemukset tarjoajalle -->
<?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'provider'): ?>
    <section class="project-applications">
        <h2>Hakemukset projektiin</h2>
        <?php if (count($applications) > 0): ?>
            <div class="project-applications-list">
                <?php foreach ($applications as $app): ?>
                    <div class="project-applications-item">
                        <div><strong>Opiskelija:</strong>
                            <?php echo htmlspecialchars($app['first_name']) . " " . htmlspecialchars($app['last_name']); ?>
                        </div>
                        <div><strong>Sähköposti:</strong> 
                            <?php echo htmlspecialchars($app['email']); ?>
                        </div>
                        <div><strong>Viesti:</strong>
                            <?php echo htmlspecialchars($app['motivation']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Ei hakemuksia tälle projektille.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>

    </section>
</main>
<?php include 'footer.php'; ?>

</body>
</html>
