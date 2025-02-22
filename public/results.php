<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$db = Database::getInstance()->getConnection();

// Récupérer les scrutins auxquels l'utilisateur a participé avec le détail du vote
$query = "SELECT s.id, s.question, s.date_debut, s.date_fin, s.algorithm, o.option_text
          FROM scrutins s
          JOIN votes v ON s.id = v.scrutin_id
          JOIN options o ON v.vote_data = o.id
          WHERE v.user_id = ?
          ORDER BY s.date_fin DESC";
$stmt = $db->prepare($query);
$stmt->execute([$userId]);
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
        <p>Vous n'avez participé à aucun scrutin.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Algorithme</th>
                    <th>Votre choix</th>
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
                        <td><?= htmlspecialchars($scrutin['option_text']) ?></td>
                        <td><a href="resultat_scrutin.php?id=<?= htmlspecialchars($scrutin['id']) ?>">Voir Résultat</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>

</html>