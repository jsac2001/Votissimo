<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance()->getConnection();

// Récupérer tous les scrutins
$query = "SELECT id, question, date_debut, date_fin FROM scrutins ORDER BY date_fin DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$scrutins = $stmt->fetchAll();

// Récupération des statistiques du site :

// Nombre d'utilisateurs
$query = "SELECT COUNT(*) AS user_count FROM users";
$stmt = $db->prepare($query);
$stmt->execute();
$userCount = $stmt->fetchColumn();

// Nombre de scrutins
$query = "SELECT COUNT(*) AS scrutin_count FROM scrutins";
$stmt = $db->prepare($query);
$stmt->execute();
$scrutinCount = $stmt->fetchColumn();

// Nombre de votes
$query = "SELECT COUNT(*) AS vote_count FROM votes";
$stmt = $db->prepare($query);
$stmt->execute();
$voteCount = $stmt->fetchColumn();

// Nombre d'options
$query = "SELECT COUNT(*) AS option_count FROM options";
$stmt = $db->prepare($query);
$stmt->execute();
$optionCount = $stmt->fetchColumn();

// Votes effectués ce mois-ci (en se basant sur le champ created_at)
$query = "SELECT COUNT(*) AS votes_this_month FROM votes WHERE YEAR(created_at)=YEAR(NOW()) AND MONTH(created_at)=MONTH(NOW())";
$stmt = $db->prepare($query);
$stmt->execute();
$votesThisMonth = $stmt->fetchColumn();

// Moyenne de votes par scrutin
$avgVotes = $scrutinCount > 0 ? round($voteCount / $scrutinCount, 2) : 0;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votissimo - Administration</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        table.stats-table {
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.stats-table th, table.stats-table td {
            border: 1px solid #ccc;
            padding: 8px 12px;
        }
        table.stats-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Interface d'Administration</h1>
    
    <h2>Statistiques du site</h2>
    <table class="stats-table">
        <tr>
            <th>Statistique</th>
            <th>Valeur</th>
        </tr>
        <tr>
            <td>Nombre d'utilisateurs</td>
            <td><?= htmlspecialchars($userCount) ?></td>
        </tr>
        <tr>
            <td>Nombre de scrutins</td>
            <td><?= htmlspecialchars($scrutinCount) ?></td>
        </tr>
        <tr>
            <td>Nombre de votes</td>
            <td><?= htmlspecialchars($voteCount) ?></td>
        </tr>
        <tr>
            <td>Nombre d'options</td>
            <td><?= htmlspecialchars($optionCount) ?></td>
        </tr>
        <tr>
            <td>Votes ce mois</td>
            <td><?= htmlspecialchars($votesThisMonth) ?></td>
        </tr>
        <tr>
            <td>Moyenne de votes par scrutin</td>
            <td><?= htmlspecialchars($avgVotes) ?></td>
        </tr>
    </table>

    <h2>Liste des Scrutins</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="scrutins-table">
            <?php foreach ($scrutins as $scrutin): ?>
                <tr id="scrutin-<?= $scrutin['id'] ?>">
                    <td><?= htmlspecialchars($scrutin['id']) ?></td>
                    <td><?= htmlspecialchars($scrutin['question']) ?></td>
                    <td><?= htmlspecialchars($scrutin['date_debut']) ?></td>
                    <td><?= htmlspecialchars($scrutin['date_fin']) ?></td>
                    <td>
                        <button onclick="deleteScrutin(<?= $scrutin['id'] ?>)">Supprimer</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="create_scrutin.php">Créer un nouveau scrutin</a></p>
    <p><a href="index.php">Retour à l'accueil</a></p>

    <script>
        function deleteScrutin(scrutinId) {
            if (!confirm('Supprimer ce scrutin ?')) return;

            let formData = new FormData();
            formData.append("id", scrutinId);

            fetch('delete_scrutin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Réponse JSON :", data);
                if (data.status === 'success') {
                    document.getElementById('scrutin-' + scrutinId).remove();
                    alert(data.message);
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => console.error('Erreur réseau :', error));
        }
    </script>
    <footer><?php require_once __DIR__ . '/notification.php'; ?></footer>
</body>
</html>
