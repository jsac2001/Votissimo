<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Organisateur', 'Administrateur'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question    = trim($_POST['question'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dateDebut   = trim($_POST['date_debut'] ?? '');
    $dateFin     = trim($_POST['date_fin'] ?? '');
    $algorithm   = trim($_POST['algorithm'] ?? 'proportionnel');
    $options     = $_POST['options'] ?? [];
    $createdBy   = $_SESSION['user']['id'];

    if (empty($question) || empty($description) || empty($dateDebut) || empty($dateFin) || empty($options)) {
        $_SESSION['error'] = "Tous les champs sont requis, y compris les options.";
        header("Location: create_scrutin.php");
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        
        $stmt = $db->prepare("INSERT INTO scrutins (question, description, date_debut, date_fin, created_by, algorithm) 
                              VALUES (:question, :description, :date_debut, :date_fin, :created_by, :algorithm)");
    
        $stmt->execute([
            ':question'    => $question,
            ':description' => $description,
            ':date_debut'  => $dateDebut,
            ':date_fin'    => $dateFin,
            ':created_by'  => $createdBy,
            ':algorithm'   => $algorithm
        ]);
        $scrutinId = $db->lastInsertId();
    
        if (!$scrutinId) {
            throw new Exception("Failed to insert scrutin.");
        } else {
            echo "Scrutin created successfully! ID: " . $scrutinId . "<br>";
        }
    
        $stmtOptions = $db->prepare("INSERT INTO options (scrutin_id, option_text) VALUES (:scrutin_id, :option_text)");
        foreach ($options as $option) {
            if (!empty(trim($option))) {
                $stmtOptions->execute([
                    ':scrutin_id' => $scrutinId,
                    ':option_text' => trim($option)
                ]);
                echo "Inserted option: " . $option . "<br>";
            }
        }
    
        $db->commit();
        $_SESSION['message'] = "Scrutin créé avec succès. Partagez ce lien: scrutin.php?id=$scrutinId";
        header("Location: index.php");
        exit;
    
    } catch (PDOException $e) {
        $db->rollBack();
        die("SQL Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Scrutin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Créer un Scrutin</h1>
    <form method="POST" action="create_scrutin.php">
        <label for="question">Question :</label>
        <input type="text" id="question" name="question" required>
        
        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="date_debut">Date de début :</label>
        <input type="datetime-local" id="date_debut" name="date_debut" required>
        
        <label for="date_fin">Date de fin :</label>
        <input type="datetime-local" id="date_fin" name="date_fin" required>
        
        <label for="algorithm">Méthode de vote :</label>
        <select id="algorithm" name="algorithm">
            <option value="proportionnel">Proportionnel</option>
            <option value="majoritaire">Majoritaire</option>
            <option value="condorcet">Condorcet</option>
        </select>
        
        <label>Options de vote :</label>
        <div id="options-container">
            <input type="text" name="options[]" required>
        </div>
        <button type="button" onclick="addOption()">Ajouter une option</button>
        
        <button type="submit">Créer le scrutin</button>
    </form>
    <p><a href="index.php">Retour à l'accueil</a></p>

    <script>
        function addOption() {
            let container = document.getElementById("options-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "options[]";
            input.required = true;
            container.appendChild(document.createElement("br"));
            container.appendChild(input);
        }
    </script>
</body>
</html>