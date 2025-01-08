<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "500 - Internal Server Error";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Palvelinvirhe</h1>
</header>

<main>
    <section>
        <p>Valitettavasti palvelimella tapahtui virhe.</p>
        <p>Yritä ladata sivu uudelleen tai palaa <a href="index.php">etusivulle</a>.</p>
        <p>Jos ongelma jatkuu, voit antaa meille palautetta lähettämällä sähköpostia osoitteeseen support(at)projektitori.fi tai käyttämällä <a href="palaute.php">palautelomaketta</a>.</p>
    </section>
</main>

<?php 
 include 'footer.php';
 ?>

</body>
</html>