<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "400 - Bad request";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Virheellinen pyyntö</h1>
</header>

<main>
    <section>
        <p>Pyytämäsi sivu on virheellinen tai puutteelliset tiedot on lähetetty palvelimelle.</p>
        <p>Varmista, että URL-osoite on oikein ja yritä uudelleen.</p>
        <p>Palaa <a href="index.php">etusivulle</a> tai <a href="yhteystiedot.php">ota yhteyttä</a> ylläpitoon, jos ongelma jatkuu.</p>
    </section>
</main>

<?php 
 include 'footer.php';
 ?>

</body>
</html>