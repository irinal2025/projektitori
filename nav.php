<nav class="topnav"  id="myTopnav">
    <a href="/projektit_PHP/projektitori/" class="nav-frontpage-link">🅟🅡🅞🅙🅔🅚🅣🅘ⓉⓄⓇⒾ</a>
    <!--a href="#">Etusivu</a-->
    <a href="projektit.php">Projektit</a>
    <a href="opiskelijoille.php">Opiskelijoille</a>
    <a href="projektin-tarjoajille.php">Projektin tarjoajille</a>
    <a href="ohjeet.php">Ohjeet ja tuki</a>
    <a href="uutiset.php">Uutiset</a>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Näytetään Profiili ja Kirjaudu ulos -linkit vain jos käyttäjä on kirjautunut sisään -->
    <a href="kirjauduulos.php" class="nav-r nav-order-last">Kirjaudu ulos<span class="nav-icon icon-logout"></span></a>
    <a href="profiili.php" class="nav-r">Profiili<span class="nav-icon icon-user"></span></a>
<?php else: ?>
    <!-- Jos käyttäjä ei ole kirjautunut, ei näytetä näitä linkkejä -->
    <a href="kirjaudu.php" class="nav-r nav-order-last">Kirjaudu<span class="nav-icon icon-login"></span></a>
    <a href="rekisteroidy.php" class="nav-r">Rekisteröidy<span class="nav-icon icon-new-user"></span></a>
<?php endif; ?>


    <a href="javascript:void(0);" class="icon" onclick="myTopNavFunction()">
        <!--i class="fa fa-bars"></i-->
        <span class="nav-bar"></span>
        <span class="nav-bar"></span>
        <span class="nav-bar"></span>
    </a>
</nav>