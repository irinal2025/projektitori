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
    if (!isset($_SESSION['user_id'])) {
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

// Tietojen päivitys käsittely (POST-data)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Validoi ja päivittää käyttäjän tiedot tietokantaan

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

   // Jos virheitä ei ole, suoritetaan päivitys tietokantaan
   if (empty($errors)) {
    
        // Uniikin vahvistuskoodin luonti
        $verification_token = bin2hex(random_bytes(50));

    // Oletetaan, että yhteys tietokantaan on avattu
    //require 'database_connection.php'; ??

    // SQL-kysely käyttäjän päivittämiseksi
    $query = "UPDATE users 
    SET first_name = ?, 
    last_name = ?
    WHERE user_id = ?";

    $stmt = $yhteys->prepare($query);

        //Syötteiden "siistimisestä":
        $first_name = $yhteys->real_escape_string(strip_tags($_POST["first_name"]));
        $last_name = $yhteys->real_escape_string(strip_tags($_POST["last_name"]));
        $stmt->bind_param(
            'ssi',  // tyyppien määritys: string, string
            $first_name,
            $last_name,
            $user_id
        );

        // Suorita kysely
        $stmt->execute();

        $output = '<div class="output-success"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Tietojen päivittäminen onnistui.</div>';

        // Päivityksen jälkeen haetaan päivitetyt tiedot tietokannasta
        $query = "SELECT email, first_name, last_name, user_type FROM users WHERE user_id = ?";
        $stmt = $yhteys->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }

        //$value['user_type'] = null;
        //$value['first_name'] = null;
        //$value['last_name'] = null;
        //$value['email'] = null;

    } else {
        $output = '<div class="output-alert"><span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>Virheitä löytyi. Tarkista syötteet.</div>';
    }

}

?>

<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Profiilisivu - Projektitori";
    $css = "css/projektit.css";
    include 'head.php'; 
?>
<body>
<?php include 'nav.php'; ?>


<header>
    <!-- Profiilisivu projektin tarjoajille -->
    <?php if ($user['user_type'] == 'provider'): ?>
         <h1>Projektin tarjoajan profiilisivu</h1>
    <?php endif; ?>
    <!-- Profiilisivu opiskelijoille -->
    <?php if ($user['user_type'] == 'student'): ?>
        <h1>Opiskelijan profiilisivu</h1>
    <?php endif; ?>
</header>

<main>
    <div class="lomaketulokset"><?=$output?></div>

        <section class="profile-section">
            
            <form action="profiili.php" method="POST">

        <!-- Etunimi -->
        <div class="mb-3">
            <label for="first_name" class="label-responsive">Etunimi:</label>
            <input type="text" id="first_name" name="first_name" required autocomplete="given-name" class="form-control form-control-lg <?= isset($errors['first_name']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($user['first_name']); ?>" >
            <div class="invalid-feedback"><?= $errors['first_name'] ?? ""; // Näytä virheviesti jos on olemassa ?></div>
        </div>

        <!-- Sukunimi -->
        <div class="mb-3">
            <label for="last_name"  class="label-responsive">Sukunimi:</label>
            <input type="text" id="last_name" name="last_name" required autocomplete="family-name" class="form-control form-control-lg <?= isset($errors['last_name']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($user['last_name']); ?>" >
            <div class="invalid-feedback"><?= $errors['last_name'] ?? ""; ?></div>
        </div>

        <!-- Sähköposti -->
        <div class="mb-3">
            <label for="email"  class="label-responsive">Sähköposti:</label>
            <input type="text" id="email" name="email" required disabled autocomplete="family-name" class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($user['email']); ?>" >
            <div class="invalid-feedback"><?= $errors['email'] ?? ""; ?></div>
        </div>

             <?php if ($user['user_type'] == 'student'): ?>
                <!--
                <div class="mb-3">
                    <label for="skills">Osaaminen:</label>
                    <textarea id="skills" name="skills" rows="4" placeholder="Kirjoita osaamistietosi..." class="form-control" ><?= isset($user['skills']) ? htmlspecialchars($user['skills']) : ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="work-history">Työhistoria:</label>
                    <textarea id="work-history" name="work_history" rows="4" placeholder="Lisää työhistoriasi..." class="form-control" ><?= isset($user['work_history']) ? htmlspecialchars($user['work_history']) : ''; ?></textarea>
                </div>
                <div class="mb-3">                
                    <label for="linkedin">LinkedIn-profiilisi julkinen URL</label>
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="linkedin">https://www.linkedin.com/in/</span>
                    </div>
                    <input type="text" class="form-control" id="linkedin">
                </div>
                <div class="mb-3">
                    <label for="portfolio">Portfolio-linkit:</label>
                    <input type="text" id="portfolio" name="portfolio" value="<?= isset($user['portfolio']) ? htmlspecialchars($user['portfolio']) : ''; ?>" placeholder="Anna portfoliolinkit..." class="form-control" >
                </div>
                <div class="mb-3">
                    <label for="cv">CV-tiedosto (URL tai lataa PDF):</label>
                    <input type="text" id="cv" name="cv" value="<?= isset($user['cv']) ? htmlspecialchars($user['cv']) : ''; ?>" placeholder="Lisää CV tai linkki tiedostoon..." class="form-control" >
                </div>
             -->
            <?php endif; ?>



            <?php if ($user['user_type'] == 'provider'): ?>
                <!--
                <label for="company-name">Yrityksen nimi:</label>
                <input type="text" id="company-name" name="company_name" required>
                -->
            <?php endif; ?>


                <button class="submit-button btn btn-primary" type="submit">Tallenna muutokset</button>
            </form>
        </section>

<!-- Profiilisivu opiskelijoille -->
<?php if ($user['user_type'] == 'student'): ?>
        <section class="profile-section">
            <h2>Omat hakemukset</h2>
            <!--p>Ei avoimia hakemuksia tällä hetkellä.</p-->
            <?php
        // Haetaan opiskelijan hakemukset
        $stmt = $yhteys->prepare("SELECT a.application_id, a.motivation, p.project_name, p.location, p.deadline 
                                  FROM applications a 
                                  JOIN projects p ON a.project_id = p.project_id 
                                  WHERE a.student_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <div class="own-projects-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="own-projects-item">
                        <div><h3>Projekti:</strong> <?php echo htmlspecialchars($row['project_name']); ?></h3></div>
                        <div><strong>Viesti:</strong> <?php echo htmlspecialchars($row['motivation']); ?></div>
                        <div><strong>Sijainti:</strong> <?php echo htmlspecialchars($row['location']); ?></div>
                        <div><strong>Deadline:</strong> <?php echo date("j.m.Y", strtotime($row['deadline'])); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Ei avoimia hakemuksia tällä hetkellä.</p>
        <?php endif; ?>
        </section>
        <!--section class="profile-section">
            <h2>Omat projektit</h2>
            <p>Sinulla ei ole omia projekteja.</p>
        </section-->
<?php endif; ?>

<!-- Profiilisivu projektin tarjoajille -->
<?php if ($user['user_type'] == 'provider'): ?>
        <!-- Omat projektit -->
        <section class="my-projects-section">
            <h2>Omat projektit</h2>
                    <!-- Uuden projektin lisäämislinkki -->
        <a href="uusi-projekti.php" class='view-more'>Lisää uusi projekti »</a>

            <?php
            // Hakee käyttäjän tiedot tietokannasta, jos käyttäjä on kirjautunut sisään
            $user_id = $_SESSION['user_id'];
            // Hae kaikki projektit, jotka kuuluvat kirjautuneelle providerille
            //$stmt = $yhteys->prepare("SELECT project_name, description, status, category, skill_level, deadline, location FROM projects WHERE provider_id = ?");

            $stmt = $yhteys->prepare("SELECT 
                    p.project_id, 
                    p.project_name, 
                    p.description, 
                    p.status, 
                    c.category_name,  -- Tämä tulee 'categories'-taulusta
                    p.skill_level, 
                    p.deadline, 
                    p.location
                FROM projects p
                JOIN project_categories pc ON p.project_id = pc.project_id
                JOIN categories c ON pc.category_id = c.category_id
                WHERE p.provider_id = ?
            ");

            //$stmt->bind_param("i", $user['user_id']);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0): ?>
                <!--<div class="d-flex">
                    <div class="p-2">Projektin nimi</div>
                    <div class="p-2">Kuvaus</div>
                    <div class="p-2">Status</div>
                    <div class="p-2">Kategoria</div>
                    <div class="p-2">Taitotaso</div>
                    <div class="p-2">Deadline</div>
                    <div class="p-2">Työskentelytapa</div>
                </div>-->
                
                <ul class="project-list">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li class="project-item">
                            <h3><?php echo htmlspecialchars($row['project_name']); ?></h3>
                            <p>
                                <?php
                                // Haetaan hakemukset tälle projektille
                                $stmt2 = $yhteys->prepare("SELECT application_id FROM applications WHERE project_id = ?");
                                $stmt2->bind_param("i", $row['project_id']);
                                $stmt2->execute();
                                $app_result = $stmt2->get_result();
                                if ($app_result->num_rows > 0) {
                                    echo '<strong>Hakemukset:</strong> <span>' . $app_result->num_rows . '</span>';
                                } else {
                                    echo '<strong>Hakemukset:</strong> <span>Ei hakemuksia</span>';
                                }
                                ?>
                                </p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                                <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($row['category_name']); ?></p>
                                <p><strong>Taitotaso:</strong> <?php echo htmlspecialchars($row['skill_level']); ?></p>
                                <p><strong>Deadline:</strong> <?php echo date("j.m.Y", strtotime($row['deadline'])); ?></p>
                                <p><strong>Työskentelytapa:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                <p><strong>Kuvaus:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                <p><a href="projekti.php?projekti=<?php echo htmlspecialchars($row['project_id']); ?>" class="view-more">Katso lisää »</a>
                                    </p>

                           </li>
                        <?php endwhile; ?>
                        </ul>
            <?php else: ?>
                <p>Sinulla ei ole vielä projekteja.</p>
            <?php endif; ?>
        </section>
 
        <?php endif; ?>
</main>


<?php include 'footer.php'; ?>

</body>
</html>