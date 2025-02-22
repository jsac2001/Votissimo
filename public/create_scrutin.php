<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Organisateur', 'Administrateur'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question    = trim($_POST['question'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dateDebut   = trim($_POST['date_debut'] ?? '');
    $dateFin     = trim($_POST['date_fin'] ?? '');
    $createdBy   = $_SESSION['user']['id'];

    // Ici, instanciez et enregistrez le Scrutin via la classe Scrutin
    $_SESSION['message'] = "Scrutin créé avec succès.";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votissimo - Créer un Scrutin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Créer un Scrutin</h1>
    <form method="POST" action="create_scrutin.php">
        <label for="question">Question :</label>
        <input type="text" id="question" name="question" required>
        
        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="date_debut">Date de début :</label>
        <input type="datetime-local" id="date_debut" name="date_debut" required>
        
        <label for="date_fin">Date de fin :</label>
        <input type="datetime-local" id="date_fin" name="date_fin" required>
        
        <button type="submit">Créer le scrutin</button>
    </form>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
