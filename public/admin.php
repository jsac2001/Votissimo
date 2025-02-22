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
                    console.log("Réponse JSON :", data); // Debugging
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
    <footer><?php require_once __DIR__ . '/notification.php'; ?><footer>
</body>
</html>