<!DOCTYPE html>
<html lang="fi">
<?php 
    $title = "Omat projektit  - Projektitori";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
?>
<body>
<?php include 'nav.php'; ?>
    <style>
        /*main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }*/
        .projects-section {
            margin-bottom: 30px;
        }
        .project-card {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        .project-card h3 {
            margin: 0;
        }
        .project-status {
            font-weight: bold;
            color: #28a745;
        }
        .project-status.pending {
            color: #ffc107;
        }
        .project-status.rejected {
            color: #dc3545;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #218838;
            color: #fff;
        }
    </style>

    <header>
        <h1>Omat projektit</h1>
    </header>

    <main>
        <!-- Omat projektit opiskelijoille -->
        <section class="projects-section">
            <h2>Seuraa hakemia projekteja (Opiskelijoille)</h2>
            <div class="project-card">
                <h3>Web-kehitysprojekti #1</h3>
                <p>Taitovaatimukset: HTML, CSS, JavaScript</p>
                <p>Hakemuksen tila: <span class="project-status">Hyväksytty</span></p>
                <a href="project-details.php?id=1" class="button">Näytä tarkemmat tiedot</a>
            </div>
            <div class="project-card">
                <h3>UI-suunnitteluprojekti</h3>
                <p>Taitovaatimukset: Figma, UX-suunnittelu</p>
                <p>Hakemuksen tila: <span class="project-status pending">Odottaa</span></p>
                <a href="project-details.php?id=2" class="button">Näytä tarkemmat tiedot</a>
            </div>
            <div class="project-card">
                <h3>Graafinen suunnitteluprojekti #5</h3>
                <p>Taitovaatimukset: Adobe Illustrator, Photoshop</p>
                <p>Hakemuksen tila: <span class="project-status rejected">Hylätty</span></p>
                <a href="project-details.php?id=3" class="button">Näytä tarkemmat tiedot</a>
            </div>
        </section>

        <!-- Omat projektit projektin tarjoajille -->
        <section class="projects-section">
            <h2>Hallitse omia projektejasi (Projektin tarjoajille)</h2>
            <div class="project-card">
                <h3>Ohjelmointiprojekti</h3>
                <p>Hakemuksia: 12</p>
                <p>Tilanne: 3 valittu, 2 hylätty</p>
                <a href="manage-project.php?id=1" class="button">Hallitse projektia</a>
            </div>
            <div class="project-card">
                <h3>Graafinen suunnitteluprojekti</h3>
                <p>Hakemuksia: 8</p>
                <p>Tilanne: Odottaa</p>
                <a href="manage-project.php?id=2" class="button">Hallitse projektia</a>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>