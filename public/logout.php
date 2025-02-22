<?php
session_start(); // Démarrer la session si elle existe

// Supprimer toutes les variables de session
$_SESSION = array();

// Si un cookie de session existe, le supprimer aussi
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Redirection vers la page de connexion avec un message
header("Location: login.php?logout=success");
exit();