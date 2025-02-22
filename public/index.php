<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votissimo - Accueil</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur Votissimo</h1>
        <nav>
            <ul>
                <li><a href="register.php">S'inscrire</a></li>
                <li><a href="login.php">Se connecter</a></li>
                <li><a href="create_scrutin.php">Créer un Scrutin</a></li>
                <li><a href="vote.php">Participer à un Scrutin</a></li>
                <li><a href="results.php">Résultats</a></li>
                <li><a href="admin.php">Administration</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <p>Votissimo est une plateforme de vote en ligne qui vous permet de participer, créer et consulter des scrutins.</p>
    </main>
</body>
</html>
