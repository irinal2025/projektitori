<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "401 - Authorization Required";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>401 - Kirjautuminen vaaditaan</h1>
</header>

<main>
    <section>
        <p>Tämä sivu vaatii kirjautumisen. Sinulla ei ole oikeutta tarkastella tätä sisältöä ilman asianmukaista valtuutusta.</p>
        <p><a href="kirjaudu.php">Kirjaudu sisään</a> jatkaaksesi tai palaa <a href="index.php">etusivulle</a>.</p>
        <p>Jos uskot tämän olevan virhe, <a href="yhteystiedot.php">ota yhteyttä</a> sivuston ylläpitäjään.</p>
    </section>
</main>

<?php 
 include 'footer.php';
 ?>

</body>
</html>