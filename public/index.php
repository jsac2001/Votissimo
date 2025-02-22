<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votissimo - Accueil</title>
    <link rel="stylesheet" href="assets/index.css">
    <link rel="stylesheet" href="assets/header.css">
</head>

<body>
    <header><?php require_once __DIR__ . '/template/header.php'; ?>
    </header>
    <main>
        <p>Votissimo est une plateforme de vote en ligne qui vous permet de participer, créer et consulter des scrutins.
        </p>
    </main>
    <footer><?php require_once __DIR__ . '/notification.php'; ?>
        <footer>
</body>

</html>