<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/calcul_resultats.php';

use Votissimo\Database;

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance()->getConnection();

// Vérifier si un ID de scrutin est fourni
$scrutinId = $_GET['id'] ?? null;
if (!$scrutinId) {
    die("ID de scrutin manquant.");
}

// Récupérer les détails du scrutin
$query = "SELECT id, question, date_debut, date_fin, algorithm FROM scrutins WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$scrutinId]);
$scrutin = $stmt->fetch();

if (!$scrutin) {
    die("Scrutin introuvable.");
}

// Sélection de la méthode de calcul en fonction de l'algorithme du scrutin
$methodUsed = ucfirst($scrutin['algorithm']); // Rendre la méthode lisible
switch ($scrutin['algorithm']) {
    case 'proportionnel':
        $results = calculProportionnel($scrutinId, $db);
        break;
    case 'majoritaire':
        $results = calculMajoritaire($scrutinId, $db);
        break;
    case 'condorcet':
        $results = calculCondorcet($scrutinId, $db);
        break;
    default:
        die("Algorithme inconnu.");
}

// Récupérer toutes les options disponibles pour ce scrutin avec les votes associés
$query = "SELECT o.option_text, COUNT(v.id) AS vote_count
          FROM options o
          LEFT JOIN votes v ON o.id = v.vote_data AND v.scrutin_id = ?
          WHERE o.scrutin_id = ?
          GROUP BY o.option_text
          ORDER BY vote_count DESC";
$stmt = $db->prepare($query);
$stmt->execute([$scrutinId, $scrutinId]);
$allOptions = $stmt->fetchAll();

// Vérifier s'il y a eu des votes
$totalVotes = array_sum(array_column($allOptions, 'vote_count'));
$noVotes = $totalVotes === 0;

// Déterminer l'option gagnante
$winningOption = !$noVotes && !empty($allOptions) ? $allOptions[0]['option_text'] : "Aucune option gagnante";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Résultats du scrutin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <h1>Résultats du scrutin : <?= htmlspecialchars($scrutin['question']) ?></h1>
    <p>Période de vote : du <?= htmlspecialchars($scrutin['date_debut']) ?> au
        <?= htmlspecialchars($scrutin['date_fin']) ?>
    </p>
    <p><strong>Méthode de calcul utilisée :</strong> <?= htmlspecialchars($methodUsed) ?></p>

    <?php if ($noVotes): ?>
        <p>Aucun vote n'a été enregistré pour ce scrutin.</p>
    <?php else: ?>
        <?php if ($scrutin['algorithm'] === 'condorcet'): ?>
            <h2>Classement des options selon Condorcet :</h2>
            <ol>
                <?php foreach ($results as $result): ?>
                    <li><?= htmlspecialchars($result['option_text']) ?> (Score : <?= htmlspecialchars($result['score']) ?>)</li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Option</th>
                        <th>Nombre de votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allOptions as $option): ?>
                        <tr>
                            <td><?= htmlspecialchars($option['option_text']) ?></td>
                            <td><?= htmlspecialchars($option['vote_count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h2>Option gagnante :</h2>
            <p><strong><?= htmlspecialchars($winningOption) ?></strong></p>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="results.php">Retour à vos scrutins</a></p>
</body>

</html>