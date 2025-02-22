<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;
use Votissimo\User;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = 'Participant';

    $db = Database::getInstance()->getConnection();

    $user = new User(null, $username, $password, $role);
    if ($user->register($db)) {
        $_SESSION['message'] = "Inscription réussie ! Veuillez vous connecter.";
        header("Location: login.php");
        exit;
    } else {
        $error = "Erreur lors de l'inscription.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votissimo - Inscription</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Inscription</h1>
    <?php if (isset($error)) : ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="register.php">
        <label for="username">Pseudonyme :</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
</body>
</html>
