<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "404 - page not found";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Hakemaasi sivua ei löytynyt</h1>
</header>

<main>
    <section>
        <p>Jos etsit tiettyä projektia, kokeile <a href="projektit.php">hakua</a>.</p>
        <p>Voit antaa meille myös <a href="palaute.php">palautetta</a> palvelustamme.</p>
        <p><a href="index.php">Palaa etusivulle</a></p>
    </section>
</main>

<?php 
 include 'footer.php';
 ?>

</body>
</html>