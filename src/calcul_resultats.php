<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Calcul du scrutin proportionnel
 * L'option avec le plus de votes gagne.
 */
function calculProportionnel($scrutinId, $db)
{
    $query = "SELECT o.option_text, COUNT(v.id) AS vote_count
              FROM votes v
              JOIN options o ON v.vote_data = o.id
              WHERE v.scrutin_id = ?
              GROUP BY o.option_text
              ORDER BY vote_count DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$scrutinId]);
    return $stmt->fetchAll();
}

/**
 * Calcul du scrutin majoritaire
 * Une option doit avoir plus de 50% des votes pour être élue.
 * Si ce n'est pas le cas, un second tour peut être envisagé.
 */
function calculMajoritaire($scrutinId, $db)
{
    $query = "SELECT o.option_text, COUNT(v.id) AS vote_count
              FROM votes v
              JOIN options o ON v.vote_data = o.id
              WHERE v.scrutin_id = ?
              GROUP BY o.option_text
              ORDER BY vote_count DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$scrutinId]);
    $results = $stmt->fetchAll();

    // Vérification si une option a plus de 50% des voix
    $totalVotes = array_sum(array_column($results, 'vote_count'));
    foreach ($results as $result) {
        if ($result['vote_count'] > $totalVotes / 2) {
            return [$result]; // Gagnant trouvé
        }
    }

    return ["message" => "Aucun gagnant au premier tour, second tour requis."];
}

/**
 * Calcul du scrutin selon la méthode Condorcet
 * On utilise un graphe orienté pour identifier le gagnant.
 */
function calculCondorcet($scrutinId, $db)
{
    // Récupération des votes des utilisateurs sous forme de préférences
    $query = "SELECT v.user_id, v.vote_data
              FROM votes v
              WHERE v.scrutin_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$scrutinId]);
    $votes = $stmt->fetchAll();

    // Déchiffrer les classements des utilisateurs
    $duels = [];
    foreach ($votes as $vote) {
        $preferences = json_decode($vote['vote_data'], true);
        for ($i = 0; $i < count($preferences); $i++) {
            for ($j = $i + 1; $j < count($preferences); $j++) {
                $duels[$preferences[$i]][$preferences[$j]] = ($duels[$preferences[$i]][$preferences[$j]] ?? 0) + 1;
                $duels[$preferences[$j]][$preferences[$i]] = ($duels[$preferences[$j]][$preferences[$i]] ?? 0) - 1;
            }
        }
    }

    // Construire un graphe des gagnants
    $scores = [];
    foreach ($duels as $optionA => $opponents) {
        $scores[$optionA] = 0;
        foreach ($opponents as $optionB => $score) {
            if ($score > 0) {
                $scores[$optionA]++;
            }
        }
    }

    // Déterminer le gagnant (option avec le plus de victoires en duel)
    arsort($scores);
    $winner = array_key_first($scores);

    return [["option_text" => $winner, "vote_count" => $scores[$winner]]];
}