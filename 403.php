<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "403 - Forbidden";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Pääsy kielletty</h1>
</header>

<main>
    <section>
        <p>Valitettavasti sinulla ei ole oikeuksia tarkastella tätä sivua.</p>
        <p>Jos uskot tämän olevan virhe, <a href="yhteystiedot.php">ota yhteyttä</a> sivuston ylläpitäjään.</p>
        <p>Palaa <a href="index.php">etusivulle</a>.</p>
    </section>
</main>

<?php 
 include 'footer.php';
 ?>

</body>
</html>