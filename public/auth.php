<?php
session_start();

// Vérifier si l'utilisateur veut se déconnecter
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votissimo - Authentification</title>
    <link rel="stylesheet" href="assets/auth.css">
</head>

<body>
    <div class="container">
        <h1>Bienvenue sur Votissimo</h1>
        <p>Veuillez choisir une option :</p>
        <a href="login.php"><button>Connexion</button></a>
        <a href="register.php"><button>Inscription</button></a>
    </div>
</body>


</html>