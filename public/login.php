<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;
use Votissimo\User;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $db = Database::getInstance()->getConnection();
    $user = User::login($db, $username, $password);

    if ($user) {
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole()
        ];
        header("Location: index.php");
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votissimo - Connexion</title>
    <link rel="stylesheet" href="assets/login.css">
</head>

<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">Pseudonyme :</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
            <p>Pas de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
        </form>
    </div>
</body>


</html>