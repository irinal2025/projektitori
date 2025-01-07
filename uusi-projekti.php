<?php
$output = '';

// Muuttuja virheille
$errors = [];
$is_invalid = [];
$pattern = [];
$value = [];


$tietokanta = "projektitori";
include 'debuggeri.php';

    // Aloitetaan sessio, jotta voidaan käyttää sessioita
    if (!session_id()) session_start();

    // Tietokantayhteys
    include 'tietokantarutiinit.php';
    register_shutdown_function('debuggeri_shutdown');

    // Tarkistetaan onko käyttäjä kirjautunut sisään
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_type']) == 'provider') {
        // Jos ei ole kirjautunut, ohjataan kirjautumissivulle
        header("Location: kirjaudu.php");
        exit();
    }

    // Hakee käyttäjän tiedot tietokannasta, jos käyttäjä on kirjautunut sisään
    $user_id = $_SESSION['user_id'];
    //$query = "SELECT email, first_name, last_name, skills, work_history, portfolio, cv, user_type FROM users WHERE user_id = ?";
    $query = "SELECT email, first_name, last_name, user_type FROM users WHERE user_id = ?";
    $stmt = $yhteys->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jos käyttäjä löytyy tietokannasta, näytetään profiilitiedot
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // Jos käyttäjää ei löydy, ohjataan kirjautumissivulle
        header("Location: kirjaudu.php");
        exit();
    }

// Projektin lisääminen käsittely (POST-data)
// Projektin lisääminen käsittely (POST-data)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    // Validoi ja tallenna uusi projekti tietokantaan
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);
    $required_skills = trim($_POST['required_skills']);
    $category_id = $_POST['category'];  // Muutettu 'category' -> 'category_id'
    $skill_level = $_POST['skill_level'];
    $deadline = $_POST['deadline'];
    $application_deadline = $_POST['application_deadline'];
    $location = $_POST['location'];
    $city = trim($_POST['city']);
    $compensation = $_POST['compensation'];
    $project_duration = trim($_POST['project_duration']);
    $student_benefits = trim($_POST['student_benefits']);

    // Virheiden tarkistus (esim. kentät ei saa olla tyhjiä)
    if (empty($project_name) || empty($description) || empty($category_id) || empty($skill_level) || empty($location)) {
        $errors[] = 'Kaikki kentät ovat pakollisia.';
    } else {
        // Lisää projekti tietokantaan

        // Lisää projektin tiedot projects-tauluun
        $stmt = $yhteys->prepare("INSERT INTO projects 
        (provider_id, project_name, description, required_skills, skill_level, deadline, application_deadline, location, city, compensation, project_duration, student_benefits) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");       

        // Tarkistetaan ja käsitellään city-kenttä, jos se on tyhjä
        $city = empty($city) ? NULL : $city;

        // Varmistetaan, että deadline on oikeassa muodossa (Y-m-d)
        $deadline = date("Y-m-d", strtotime($deadline));

        $application_deadline = empty($application_deadline) ? NULL : date("Y-m-d", strtotime($application_deadline));

        // Muut tyyppimääritykset: 'i' tarkoittaa integer (provider_id, category_id), 's' tarkoittaa string (muut kentät)
        $stmt->bind_param("issssssssss", 
            $user_id, $project_name, $description, $required_skills, 
            $skill_level, $deadline, $application_deadline, $location, 
            $city, $compensation, $project_duration, $student_benefits);
            

            if ($stmt->execute()) {
                // Hae luodun projektin ID
                $project_id = $yhteys->insert_id; // mysqli_insert_id($yhteys)
            
                // Lisää kategoria project_categories-tauluun
                $stmt_category = $yhteys->prepare("INSERT INTO project_categories (project_id, category_id) VALUES (?, ?)");
                $stmt_category->bind_param("ii", $project_id, $category_id);
                $stmt_category->execute();
            
                //$output = "Projekti ja kategoria lisätty onnistuneesti!";
                $output = '<div class="output-success"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Projekti ja kategoria lisätty onnistuneesti!</div>';
            } else {
                //$output = "Virhe projektin lisäämisessä";
                $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virhe projektin lisäämisessä.</div>';
                $errors[] = "Virhe projektin lisäämisessä: " . $stmt->error;
            }
    }
}


?>

<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Profiilisivu - Projektitori";
    $css = "css/projektit.css";
    $js = "js/uusi-projekti.js";
    include 'head.php'; 
?>
<body>
<?php include 'nav.php'; ?>

    <!-- Profiilisivu projektin tarjoajille -->
    <?php if ($user['user_type'] == 'provider'): ?>
        <header>
         <h1>Lisää uusi projekti</h1>
        </header>
    <main>
        <div class="lomaketulokset"><?=$output?></div>
        </section>
        <!-- Uuden projektin lisäämislomake -->
        <section class="add-project-section">
            <form action="uusi-projekti.php" method="POST">

            <div class="form-group">
                <label for="project_name">Projektin nimi:</label>
                <input type="text" id="project_name" name="project_name" required  class="form-control form-control-notouched">
            </div>

            <div class="form-group">
                <label for="description">Projektin kuvaus:</label>
                <textarea id="description" name="description" rows="4" required class="form-control form-control-notouched"></textarea>
            </div>
            <div class="form-group">
                <label for="required_skills">Tarvittavat taidot:</label>
                <input type="text" id="required_skills" name="required_skills" class="form-control form-control-notouched">
                <small id="required_skillsHelp" class="form-text text-muted">Kirjoita pilkulla erotettu luettelo.</small>
            </div>
            <div class="form-group">
                <label for="category">Kategoria:</label>
                <select id="category" name="category" required class="form-control form-control-notouched">
                    <option value="1">Ohjelmointi</option>
                    <option value="2">Web-kehitys</option>
                    <option value="3">Graafinen suunnittelu</option>
                    <option value="4">UI/UX-suunnittelu</option>
                </select>
            </div>
            <div class="form-group">
                <label for="skill_level">Vaadittu taitotaso:</label>
                <select id="skill_level" name="skill_level" required class="form-control form-control-notouched">
                    <option value="Aloittelija">Aloittelija</option>
                    <option value="Keskitaso">Keskitaso</option>
                    <option value="Edistynyt">Edistynyt</option>
                </select>
            </div>
            <div class="form-group">
                <label for="deadline">Projektin deadline:</label>
                <input type="date" id="deadline" name="deadline" class="form-control form-control-notouched">
            </div>
            <div class="form-group">
                <label for="application_deadline ">Hakemusten viimeinen jättöpäivämäärä:</label>
                <input type="date" id="application_deadline " name="application_deadline " class="form-control form-control-notouched">
            </div>
            <div class="form-group">
                <label for="location">Työskentelytapa:</label>
                <select id="location" name="location" required class="form-control form-control-notouched" onchange="toggleCityField()">
                    <option value="Etätyö">Etätyö</option>
                    <option value="Hybridityö">Hybridityö</option>
                    <option value="Lähityö">Lähityö</option>
                </select>
            </div>
                <div id="city-container" class="form-group">
                    <label for="city">Paikkakunta:</label>
                    <input type="text" id="city" name="city" class="form-control form-control-notouched">
            </div>
            <div class="form-group">
                <label for="compensation">Onko projekti palkallinen?</label>
                <select id="compensation" name="compensation" required class="form-control form-control-notouched">
                    <option value="kyllä">Kyllä</option>
                    <option value="ei">Ei</option>
                </select>
            </div>
            <div class="form-group">
                <label for="project_duration">Projektin kesto:</label>
                <input type="text" id="project_duration" name="project_duration" class="form-control form-control-notouched">
                <small id="project_durationHelp" class="form-text text-muted">Projektin kesto voi olla esimerkiksi 2 viikkoa, 4 kuukautta tai muu vastaava aikaväli.</small>
            </div>
            <div class="form-group">
                <label for="student_benefits">Opiskelijan edut:</label>
                <textarea id="student_benefits" name="student_benefits" rows="4" class="form-control form-control-notouched"></textarea>
                <small id="student_benefitsHelp" class="form-text text-muted">Kirjoita pilkulla erotettu luettelo.</small>
            </div>
                <button class="submit-button btn btn-primary" type="submit" name="add_project">Lisää projekti</button>
            </form>
        </section>

        <?php endif; ?>
    </main>




<?php include 'footer.php'; ?>
</body>
</html>
