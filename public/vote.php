<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

$db = Database::getInstance()->getConnection();

// Gestion des requêtes AJAX pour récupérer les options
if (isset($_GET['scrutin_id'])) {
    header('Content-Type: application/json');
    $scrutinId = $_GET['scrutin_id'];
    $query = "SELECT id, option_text FROM options WHERE scrutin_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$scrutinId]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($options);
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Récupérer les scrutins en cours
$query = "SELECT id, question FROM scrutins WHERE date_debut <= NOW() AND date_fin >= NOW() ORDER BY date_fin ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$scrutins = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scrutinId = $_POST['scrutin_id'] ?? null;
    $voteData = $_POST['vote_data'] ?? null;
    $voteMethod = $_POST['vote_method'] ?? 'proportionnel';

    if ($scrutinId && $voteData) {
        // Vérifier si l'utilisateur a déjà voté pour ce scrutin
        $checkQuery = "SELECT id FROM votes WHERE user_id = ? AND scrutin_id = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([$userId, $scrutinId]);
        $existingVote = $stmt->fetch();

        if (!$existingVote) {
            // Insérer le vote dans la base de données
            $query = "INSERT INTO votes (user_id, scrutin_id, vote_method, vote_data) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$userId, $scrutinId, $voteMethod, $voteData]);
            $_SESSION['message'] = "Votre vote a été pris en compte.";
        } else {
            $_SESSION['message'] = "Vous avez déjà voté pour ce scrutin.";
        }
    } else {
        $_SESSION['message'] = "Erreur : données de vote manquantes.";
    }

    header("Location: results.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Voter</title>
    <script>
        function loadOptions(scrutinId) {
            if (scrutinId) {
                fetch('vote.php?scrutin_id=' + scrutinId)
                    .then(response => response.json())
                    .then(data => {
                        let optionsContainer = document.getElementById('options-container');
                        optionsContainer.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(option => {
                                let input = `<input type='radio' name='vote_data' value='${option.id}' required> ${option.option_text}<br>`;
                                optionsContainer.innerHTML += input;
                            });
                        } else {
                            optionsContainer.innerHTML = '<p>Aucune option disponible.</p>';
                        }
                    });
            }
        }
    </script>
</head>

<body>
    <h1>Votez pour un scrutin</h1>
    <?php if (!empty($scrutins)): ?>
        <form action="vote.php" method="POST">
            <label for="scrutin_id">Sélectionnez un scrutin :</label>
            <select name="scrutin_id" required onchange="loadOptions(this.value)">
                <option value="">-- Choisissez un scrutin --</option>
                <?php foreach ($scrutins as $scrutin): ?>
                    <option value="<?= htmlspecialchars($scrutin['id']) ?>">
                        <?= htmlspecialchars($scrutin['question']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="options-container">
                <p>Sélectionnez un scrutin pour voir les options.</p>
            </div>

            <input type="hidden" name="vote_method" value="proportionnel">
            <button type="submit">Soumettre</button>
        </form>
    <?php else: ?>
        <p>Aucun scrutin en cours.</p>
    <?php endif; ?>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>

</html>