<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    header("Location: login.php");
    exit;
}

$scrutins = [
    ['id' => 1, 'question' => 'Question 1', 'date_debut' => '2025-01-01', 'date_fin' => '2025-01-02'],
    ['id' => 2, 'question' => 'Question 2', 'date_debut' => '2025-02-01', 'date_fin' => '2025-02-02']
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votissimo - Administration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Interface d'Administration</h1>
    <h2>Liste des Scrutins</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scrutins as $scrutin): ?>
                <tr>
                    <td><?= htmlspecialchars($scrutin['id']) ?></td>
                    <td><?= htmlspecialchars($scrutin['question']) ?></td>
                    <td><?= htmlspecialchars($scrutin['date_debut']) ?></td>
                    <td><?= htmlspecialchars($scrutin['date_fin']) ?></td>
                    <td>
                        <a href="edit_scrutin.php?id=<?= $scrutin['id'] ?>">Modifier</a>
                        <a href="delete_scrutin.php?id=<?= $scrutin['id'] ?>" onclick="return confirm('Supprimer ce scrutin ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
