<?php
namespace Votissimo;

/**
 * Classe VotingAlgorithm
 *
 * Contient les méthodes statiques pour traiter les différents algorithmes de vote :
 * - Vote proportionnel
 * - Vote majoritaire
 * - Vote Condorcet
 *
 * @package Votissimo
 */
class VotingAlgorithm
{
    public static function countProportionalVotes(array $votes): array
    {
        $counts = [];
        foreach ($votes as $vote) {
            if (!isset($counts[$vote])) {
                $counts[$vote] = 0;
            }
            $counts[$vote]++;
        }
        return $counts;
    }

    public static function getProportionalWinner(array $votes): ?string
    {
        $counts = self::countProportionalVotes($votes);
        if (empty($counts)) {
            return null;
        }
        arsort($counts);
        $winners = array_keys($counts);
        return $winners[0];
    }

    public static function getMajorityWinner(array $votes): ?string
    {
        $totalVotes = count($votes);
        if ($totalVotes === 0) {
            return null;
        }
        $counts = self::countProportionalVotes($votes);
        $threshold = floor($totalVotes / 2) + 1;
        foreach ($counts as $option => $count) {
            if ($count >= $threshold) {
                return $option;
            }
        }
        return null;
    }

    public static function getCondorcetWinner(array $ballots): ?string
    {
        $candidates = [];
        foreach ($ballots as $ballot) {
            foreach ($ballot as $rank) {
                if (is_array($rank)) {
                    foreach ($rank as $candidate) {
                        $candidates[$candidate] = true;
                    }
                } else {
                    $candidates[$rank] = true;
                }
            }
        }
        $candidates = array_keys($candidates);
        $n = count($candidates);
        if ($n === 0) {
            return null;
        }

        $pairwise = [];
        foreach ($candidates as $c1) {
            foreach ($candidates as $c2) {
                if ($c1 !== $c2) {
                    $pairwise[$c1][$c2] = 0;
                }
            }
        }

        foreach ($ballots as $ballot) {
            $ranking = [];
            $rank = 1;
            foreach ($ballot as $entry) {
                if (is_array($entry)) {
                    foreach ($entry as $candidate) {
                        $ranking[$candidate] = $rank;
                    }
                } else {
                    $ranking[$entry] = $rank;
                }
                $rank++;
            }
            foreach ($candidates as $c1) {
                foreach ($candidates as $c2) {
                    if ($c1 === $c2) {
                        continue;
                    }
                    $r1 = $ranking[$c1] ?? PHP_INT_MAX;
                    $r2 = $ranking[$c2] ?? PHP_INT_MAX;
                    if ($r1 < $r2) {
                        $pairwise[$c1][$c2]++;
                    } elseif ($r1 > $r2) {
                        $pairwise[$c1][$c2]--;
                    }
                }
            }
        }

        foreach ($candidates as $candidate) {
            $isWinner = true;
            foreach ($candidates as $other) {
                if ($candidate === $other) {
                    continue;
                }
                if ($pairwise[$candidate][$other] <= 0) {
                    $isWinner = false;
                    break;
                }
            }
            if ($isWinner) {
                return $candidate;
            }
        }
        return null;
    }
}
