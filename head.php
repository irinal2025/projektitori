<?php 
// Aloitetaan sessio, jotta voimme käyttää istuntotietoja
if (!session_id()) session_start();
?>

<head>
  <!--meta name="description" content="Webpage description goes here"-->
  <?php
    if (isset($description) && !empty($description)) {
        // Jos $description on asetettu ja ei ole tyhjä
        echo '<meta name="description" content="' . htmlspecialchars($description) . '">';
    } else {
        // Jos $description ei ole asetettu tai on tyhjä
        echo '<meta name="description" content="Projektitori yhdistää opiskelijat ja projektien tarjoajat. Löydä projekteja ohjelmoinnista, web-kehityksestä ja suunnittelusta.">';
    }
  ?>
  <meta charset="utf-8"> 
  
  <title><?= $title; ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Irina Lisovskaja">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

  
  <link rel="stylesheet" href="css/projektitori.css">
<?php if (isset($css)) echo "<link rel='stylesheet' href='$css'>"; ?>

 
  <link rel="icon" type="image/x-icon" href="img/favicon.ico">
  <link rel="icon" href="img/favicon.ico">
  <link rel="icon" href="img/favicon-32x32.png" sizes="32x32">
  <link rel="icon" href="img/favicon-192x192.png" sizes="192x192">
  <link rel="icon" href="img/favicon-16x16.png" sizes="16x16">
  <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
</head>