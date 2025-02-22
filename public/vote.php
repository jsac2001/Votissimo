<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voteData = $_POST['vote_data'] ?? null;
    $voteMethod = $_POST['vote_method'] ?? 'proportionnel';
    
    // Enregistrez le vote via la classe Vote
    $_SESSION['message'] = "Votre vote a été pris en compte.";
    header("Location: results.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votissimo - Participer à un Scrutin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Participer à un Scrutin</h1>
    <form method="POST" action="vote.php">
        <fieldset>
            <legend>Choisissez votre option :</legend>
            <label>
                <input type="radio" name="vote_data" value="OptionA" required>
                Option A
            </label>
            <label>
                <input type="radio" name="vote_data" value="OptionB">
                Option B
            </label>
            <label>
                <input type="radio" name="vote_data" value="OptionC">
                Option C
            </label>
        </fieldset>
        <input type="hidden" name="vote_method" value="proportionnel">
        <button type="submit">Valider mon vote</button>
    </form>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
