<?php
namespace Votissimo;

/**
 * Classe Scrutin
 *
 * Représente un scrutin (vote) dans l'application Votissimo.
 *
 * @package Votissimo
 */
class Scrutin
{
    private $id;
    private $question;
    private $description;
    private $dateDebut;
    private $dateFin;
    private $createdBy;

    public function __construct($id = null, $question, $description, $dateDebut, $dateFin, $createdBy)
    {
        $this->id = $id;
        $this->question = $question;
        $this->description = $description;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->createdBy = $createdBy;
    }

    // Méthodes de gestion des scrutins (création, mise à jour, suppression, etc.)
}
