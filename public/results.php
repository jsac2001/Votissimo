<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

$proportionalWinner = "OptionA"; // Exemple simulé
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votissimo - Résultats</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Résultats du Scrutin</h1>
    <p>Le gagnant selon le vote proportionnel est : <strong><?= htmlspecialchars($proportionalWinner) ?></strong></p>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
