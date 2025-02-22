<?php
namespace Votissimo;

/**
 * Classe Vote
 *
 * Représente un vote réalisé par un utilisateur pour un scrutin.
 *
 * @package Votissimo
 */
class Vote
{
    private $id;
    private $userId;
    private $scrutinId;
    private $voteMethod;
    private $voteData;

    public function __construct($id = null, $userId, $scrutinId, $voteMethod, $voteData)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->scrutinId = $scrutinId;
        $this->voteMethod = $voteMethod;
        $this->voteData = $voteData;
    }

    // Méthodes de gestion des votes (enregistrement, récupération, etc.)
}
