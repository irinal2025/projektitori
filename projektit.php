<?php 
$output = '';
$selectoutput = '';

// Aloitetaan sessio, jotta voidaan käyttää sessioita
if (!session_id()) session_start();
 
// Muuttuja virheille ja syötteille
$errors = [];
$is_invalid = [];
$value = [];

// Tietokantayhteys
$tietokanta = "projektitori";
include 'debuggeri.php';
include 'tietokantarutiinit.php';
register_shutdown_function('debuggeri_shutdown');


// Tarkista lomake ja käsittele syöte
$project_name = isset($_GET['project_name']) ? trim($_GET['project_name']) : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

// Virheiden tarkistus projektin nimelle
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['project_name']) && !empty($project_name)) {
        if (strlen($project_name) < 2) {
            $errors['project_name'] = "Projektin nimi tulee olla vähintään 2 merkkiä pitkä.";
            $is_invalid['project_name'] = "is-invalid"; // Virheellinen kenttä
        }
        $value['project_name'] = $project_name; // Tallenna syötteen arvo
    } elseif (empty($project_name)) {
        $value['project_name'] = NULL; // Tyhjä arvo
    }

    // Virheiden tarkistus kategorian valinnalle
    if (isset($_GET['category_id']) && empty($category_id)) {
        $errors['category_id'] = "Valitse kategoria.";
        $is_invalid['category_id'] = "is-invalid"; // Virheellinen kenttä
    } else {
        $value['category_id'] = $category_id; // Tallenna kategorian arvo
    }
}

// Hae kategoriat
$sql = "SELECT category_id, category_name FROM categories";
$result = $yhteys->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selectoutput .= "<option value='" . $row['category_id'] . "' " . ($category_id == $row['category_id'] ? "selected" : "") . ">" . $row['category_name'] . "</option>";
    }
} else {
    $selectoutput = "<option value=''>Ei kategorioita saatavilla</option>";
}

// Valmistele kysely ja parametrit
if (isset($_GET['project_name']) && !empty($project_name) && isset($_GET['category_id']) && !empty($category_id)) {
    // Jos molemmat kentät on täytetty, ilmoita virhe
    $output = "<p>Voit hakea projekteja vain nimellä tai kategoriassa, mutta ei molemmilla.</p>";
} elseif (isset($_GET['project_name']) && !empty($project_name)) {
    // Etsi projekteja vain nimellä
    $sql = "SELECT p.project_id, p.project_name, p.description, c.category_name AS category, p.skill_level, p.deadline, p.location
            FROM projects p
            LEFT JOIN project_categories pc ON p.project_id = pc.project_id
            LEFT JOIN categories c ON pc.category_id = c.category_id
            WHERE p.project_name LIKE ?";

    $stmt = $yhteys->prepare($sql);
    $searchTerm = "%" . $project_name . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
} elseif (isset($_GET['category_id']) && !empty($category_id)) {
    // Etsi projekteja vain kategoriassa
    $sql = "SELECT p.project_id, p.project_name, p.description, c.category_name AS category, p.skill_level, p.deadline, p.location
            FROM projects p
            LEFT JOIN project_categories pc ON p.project_id = pc.project_id
            LEFT JOIN categories c ON pc.category_id = c.category_id
            WHERE c.category_id = ?";

    $stmt = $yhteys->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
} elseif (isset($_GET['show_all'])) {
    // Näytä kaikki projektit, jos "Näytä kaikki" -painiketta on painettu
    $sql = "SELECT p.project_id, p.project_name, p.description, c.category_name AS category, p.skill_level, p.deadline, p.location
            FROM projects p
            LEFT JOIN project_categories pc ON p.project_id = pc.project_id
            LEFT JOIN categories c ON pc.category_id = c.category_id";

    $stmt = $yhteys->prepare($sql);
    $stmt->execute();
} else {
    // Näytä oletuksena kaikki projektit, jos mitään hakukriteeriä ei ole annettu
    $sql = "SELECT p.project_id, p.project_name, p.description, c.category_name AS category, p.skill_level, p.deadline, p.location
            FROM projects p
            LEFT JOIN project_categories pc ON p.project_id = pc.project_id
            LEFT JOIN categories c ON pc.category_id = c.category_id";

    $stmt = $yhteys->prepare($sql);
    $stmt->execute();
}

// Suoritetaan kysely vain, jos se on määritelty
if ($stmt) {
    $result = $stmt->get_result();

    // Näytä hakutulokset
    if ($result->num_rows > 0) {
        $output = "<ul class='project-list'>";
        while ($row = $result->fetch_assoc()) {
            $output .= "<li class='project-item'>";
            $output .= "<h3>" . htmlspecialchars($row['project_name']) . "</h3>";
            $output .= "<p><strong>Kategoria:</strong> " . htmlspecialchars($row['category']) . "</p>";
            $output .= "<p><strong>Taitotaso:</strong> " . htmlspecialchars($row['skill_level']) . "</p>";
            $output .= "<p><strong>Deadline:</strong> " . date("j.m.Y", strtotime($row['deadline'])) . "</p>";
            $output .= "<p><strong>Kuvaus:</strong> " . htmlspecialchars($row['description']) . "</p>";
            $output .= "<a href='projekti.php?projekti=" . $row['project_id'] . "' class='view-more'>Katso lisää »</a>";
            $output .= "</li>";
        }
        $output .= "</ul>";
    } else {
        $output = "<p>Ei löytynyt projekteja.</p>";
    }
}

// Sulje yhteys ja lopeta
if ($stmt) {
    $stmt->close();
}
$yhteys->close();
?>

<!DOCTYPE html>
<html lang="fi">
<?php
$title = "Projektit - Projektitori";
$css = "css/forms.css";
include 'head.php'; 
?>
<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Avoimet projektit</h1>
</header>

<div class="container">
    <!-- Hakulomake ja kategoriahaku -->
    <div class="search-bar">
        <!-- Lomake: Projektin nimen haku -->
        <form action="projektit.php" method="get" class="projectform form-inline">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" id="project_name" name="project_name" class="form-control form-control-lg <?= isset($errors['project_name']) ? 'is-invalid' : ''; ?>" minlength="2">
                        <label for="project_name" class="label-responsive inputplaceholder">Hae projekti nimellä</label>
                        <div class="invalid-feedback"><?= $errors['project_name'] ?? ""; ?></div>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Hae</button>
                </div>
            </div>
        </form>

        <!-- Lomake: Kategorian haku -->
        <form action="projektit.php" method="get" class="projectform form-inline">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <select name="category_id" id="category" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : ''; ?>">
                        <option value="" disabled selected>Valitse kategoria</option>
                        <?= $selectoutput ?>
                    </select>
                    <div class="invalid-feedback"><?= $errors['category_id'] ?? ""; ?></div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Näytä projektit</button>
                </div>
            </div>
        </form>
        
        <!-- Lomake: Näytä kaikki projektit -->
        <form action="projektit.php" method="get">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="hidden" name="show_all" value="1">
                    <button type="submit" class="btn btn-secondary">Näytä kaikki projektit</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tulokset -->
    <?= $output ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
