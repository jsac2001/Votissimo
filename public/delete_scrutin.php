<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';

use Votissimo\Database;

// Activer l'affichage des erreurs pour déboguer
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Administrateur') {
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
    exit;
}

// Vérifier si un ID est fourni en POST
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de scrutin invalide']);
    exit;
}

$scrutinId = (int) $_POST['id'];
$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();

    // Supprimer les votes liés
    $stmt = $db->prepare("DELETE FROM votes WHERE scrutin_id = ?");
    $stmt->execute([$scrutinId]);

    // Supprimer les options liées
    $stmt = $db->prepare("DELETE FROM options WHERE scrutin_id = ?");
    $stmt->execute([$scrutinId]);

    // Supprimer le scrutin
    $stmt = $db->prepare("DELETE FROM scrutins WHERE id = ?");
    $stmt->execute([$scrutinId]);

    $db->commit();

    echo json_encode(['status' => 'success', 'message' => 'Scrutin supprimé avec succès']);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>