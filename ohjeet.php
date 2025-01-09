<!DOCTYPE html>
<html lang="fi">
    <?php 
    $title = "Ohjeet ja tuki - Projektitori";
    $css = "css/rekisteroidy.css";
    include 'head.php'; 
    ?>

<body>

<?php include 'nav.php'; ?>

<header>
    <h1>Ohjeet ja tuki</h1>
</header>

<main>
    <section>
        <h2>Käyttöohjeet ja opastusvideot</h2>
        <p>Tervetuloa Projektitorin ohje- ja tukisivulle! Täältä löydät kattavat käyttöohjeet sekä opastusvideoita, joiden avulla pääset helposti alkuun ja opit käyttämään sivustoa tehokkaasti.</p>

        <h3>Käyttöohjeet:</h3>
        <ul>
            <li><strong>Rekisteröityminen:</strong> Katso ohjeet <a href="rekisteroidy.php">tästä</a>.</li>
            <li><strong>Projektien selaaminen:</strong> Ohjeet kuinka voit löytää ja hakea projekteja.</li>
            <li><strong>Projektin lisääminen:</strong> Ohje projektin tarjoajille projektin lisäämiseksi sivustolle.</li>
        </ul>

        <h3>Opastusvideot:</h3>
        <p>Katso alla olevat videot, jotka auttavat sinua käyttämään palvelua:</p>
        <video controls>
            <source src="rekisteroitymisvideo.mp4" type="video/mp4">
            Rekisteröitymisohje-video
        </video>
        <video controls>
            <source src="projektinhakuvideo.mp4" type="video/mp4">
            Projektien selaus ja haku -video
        </video>
    </section>

    <section class="faq">
        <h2>Usein kysytyt kysymykset (FAQ)</h2>
        <h3>1. Kuinka rekisteröidyn Projektitoriin?</h3>
        <p>Voit rekisteröityä täyttämällä rekisteröintilomakkeen <a href="rekisteroidy.html">täällä</a>. Tarvitset vain toimivan sähköpostiosoitteen ja perustiedot.</p>

        <h3>2. Miten voin hakea projektia?</h3>
        <p>Käyttäjät voivat selata ja hakea projekteja kirjautumalla sisään ja navigoimalla projektien hakusivulle. Valitse projekti, lue tarkemmat tiedot ja lähetä hakemuksesi.</p>

        <h3>3. Miten voin lisätä projektin?</h3>
        <p>Projektin tarjoajana voit lisätä projektin rekisteröitymisen ja sisäänkirjautumisen jälkeen. Täytä projektin tiedot (kuvaus, vaadittavat taidot, aikataulu jne.) ja julkaise se sivustolle.</p>
    </section>

    <section class="contact">
        <h2>Ota yhteyttä</h2>
        <p>Jos sinulla on kysyttävää tai tarvitset tukea, voit ottaa meihin yhteyttä, siirry <a href="yhteystiedot.php" class="view-more">Ota yhteyttä-sivulle &raquo;</a></p>

<!--
        <p>Jos sinulla on kysyttävää tai tarvitset tukea, voit ottaa meihin yhteyttä täyttämällä alla olevan lomakkeen.</p>

        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" id="name" name="name" required autocomplete="name" class="form-control form-control-lg">
                <label for="name"  class="label-responsive inputplaceholder">Nimi</label>
                <div class="invalid-feedback">Nimi ei ole oikeassa muodossa.</div>
            </div>
            <div class="mb-3">
                <input type="email" id="sahkoposti" name="sahkoposti" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required class="form-control form-control-lg" autocomplete="email">
                <label for="sahkoposti" class="label-responsive inputplaceholder">Sähköposti</label>
                <div class="invalid-feedback">Sähköpostiosoite ei ole oikeassa muodossa.</div>
            </div>            

            <div class="mb-3">
                <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                <label for="message" class="label-responsive inputplaceholder">Viesti</label>
            </div>

            <button type="submit" class="btn btn-primary" disabled>Lähetä</button>
        </form>
-->
    </section>
</main>

<?php 
 include 'footer.php';
 ?>

</body>
</html>