<?php
// Aloitetaan sessio
session_start();

// Tuhoamme kaikki istuntotiedot
session_destroy();

// Ohjataan käyttäjä kirjautumissivulle
header("Location: kirjaudu.php");
exit();
?>