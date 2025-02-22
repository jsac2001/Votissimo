<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance()->getConnection();

// Récupérer les scrutins auxquels l'utilisateur a participé
$query = "SELECT id, question, date_debut, date_fin, algorithm
          FROM scrutins
          WHERE created_by = ?
          ORDER BY date_fin DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user']['id']]);
$scrutins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votissimo - Vos Scrutins</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <h1>Vos Scrutins</h1>
    <?php if (empty($scrutins)): ?>
        <p>Vous n'avez créé aucun scrutin.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Algorithme</th>
                    <th>Résultat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scrutins as $scrutin): ?>
                    <tr>
                        <td><?= htmlspecialchars($scrutin['question']) ?></td>
                        <td><?= htmlspecialchars($scrutin['date_debut']) ?></td>
                        <td><?= htmlspecialchars($scrutin['date_fin']) ?></td>
                        <td><?= htmlspecialchars($scrutin['algorithm']) ?></td>
                        <td><a href="resultat_scrutin.php?id=<?= htmlspecialchars($scrutin['id']) ?>">Voir Résultat</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="index.php">Retour à l'accueil</a></p>
    <footer><?php require_once __DIR__ . '/notification.php'; ?><footer>
</body>
</html>