<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

$db = Database::getInstance()->getConnection();

// AJAX endpoint to load options and algorithm for a given scrutin
if (isset($_GET['scrutin_id'])) {
    header('Content-Type: application/json');
    $scrutinId = $_GET['scrutin_id'];
    
    // Get the algorithm type for the scrutin
    $query = "SELECT algorithm FROM scrutins WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$scrutinId]);
    $scrutin = $stmt->fetch();
    $algorithm = $scrutin ? $scrutin['algorithm'] : 'proportionnel';
    
    // Retrieve the options
    $query = "SELECT id, option_text FROM options WHERE scrutin_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$scrutinId]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['algorithm' => $algorithm, 'options' => $options]);
    exit;
}

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// Retrieve active scrutins (voting sessions)
$query = "SELECT id, question FROM scrutins WHERE date_debut <= NOW() AND date_fin >= NOW() ORDER BY date_fin ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$scrutins = $stmt->fetchAll();

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scrutinId = $_POST['scrutin_id'] ?? null;
    $voteData = $_POST['vote_data'] ?? null; // Either a single option id or a comma‐separated ranking list
    $voteMethod = $_POST['vote_method'] ?? 'proportionnel';

    if ($scrutinId && $voteData) {
        // Check if the user has already voted for this scrutin
        $checkQuery = "SELECT id FROM votes WHERE user_id = ? AND scrutin_id = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([$userId, $scrutinId]);
        $existingVote = $stmt->fetch();

        if (!$existingVote) {
            if ($voteMethod === 'condorcet') {
                // For Condorcet, store the ranking order in both vote_data and vote_order
                $query = "INSERT INTO votes (user_id, scrutin_id, vote_method, vote_data, vote_order) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$userId, $scrutinId, $voteMethod, $voteData, $voteData]);
            } else {
                // For proportionnel/majoritaire, vote_data holds the selected option and vote_order is null
                $query = "INSERT INTO votes (user_id, scrutin_id, vote_method, vote_data, vote_order) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$userId, $scrutinId, $voteMethod, $voteData, null]);
            }
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
    <style>
        /* Simple styling for the sortable list */
        #sortable-list {
            list-style-type: none;
            padding: 0;
            width: 300px;
        }
        #sortable-list li {
            padding: 8px;
            margin: 4px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            cursor: move;
        }
        #sortable-list li button {
            margin-left: 10px;
        }
    </style>
    <script>
        function loadOptions(scrutinId) {
            if (scrutinId) {
                fetch('vote.php?scrutin_id=' + scrutinId)
                    .then(response => response.json())
                    .then(data => {
                        let optionsContainer = document.getElementById('options-container');
                        optionsContainer.innerHTML = '';
                        if (data.options.length > 0) {
                            if (data.algorithm === 'condorcet') {
                                // Create a sortable list for ranking options
                                let list = document.createElement('ul');
                                list.id = 'sortable-list';
                                data.options.forEach(option => {
                                    let li = document.createElement('li');
                                    li.setAttribute('data-option-id', option.id);
                                    li.innerHTML = option.option_text +
                                        " <button type='button' onclick='moveUp(this)'>↑</button>" +
                                        " <button type='button' onclick='moveDown(this)'>↓</button>";
                                    list.appendChild(li);
                                });
                                optionsContainer.appendChild(list);
                                
                                // Create and append hidden input to store the ordering
                                let hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'vote_data';
                                hiddenInput.id = 'vote_data';
                                optionsContainer.appendChild(hiddenInput);
                                
                                // Now update the hidden input with the initial order
                                updateVoteData();
                                
                                // Also update vote_method if needed
                                document.getElementById('vote_method').value = 'condorcet';
                            } else {
                                // Create radio buttons for single selection
                                data.options.forEach(option => {
                                    let radio = document.createElement('input');
                                    radio.type = 'radio';
                                    radio.name = 'vote_data';
                                    radio.value = option.id;
                                    radio.required = true;
                                    optionsContainer.innerHTML += radio.outerHTML + " " + option.option_text + "<br>";
                                });
                                document.getElementById('vote_method').value = data.algorithm;
                            }
                        } else {
                            optionsContainer.innerHTML = '<p>Aucune option disponible.</p>';
                        }
                    });
            }
        }

        // Functions to move items up or down in the list
        function moveUp(button) {
            let li = button.parentElement;
            let prev = li.previousElementSibling;
            if (prev) {
                li.parentElement.insertBefore(li, prev);
                updateVoteData();
            }
        }
        function moveDown(button) {
            let li = button.parentElement;
            let next = li.nextElementSibling;
            if (next) {
                li.parentElement.insertBefore(next, li);
                updateVoteData();
            }
        }
        // Updates the hidden input with the current order (as comma-separated option IDs)
        function updateVoteData() {
            let list = document.getElementById('sortable-list');
            let voteDataInput = document.getElementById('vote_data');
            let orderedIds = [];
            if (list) {
                list.querySelectorAll('li').forEach(li => {
                    orderedIds.push(li.getAttribute('data-option-id'));
                });
            }
            if(voteDataInput) {
                voteDataInput.value = orderedIds.join(',');
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

            <!-- Hidden input to specify the vote method (will be set by JavaScript) -->
            <input type="hidden" name="vote_method" id="vote_method" value="proportionnel">
            <button type="submit">Soumettre</button>
        </form>
    <?php else: ?>
        <p>Aucun scrutin en cours.</p>
    <?php endif; ?>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
