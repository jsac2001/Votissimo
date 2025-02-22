<?php
namespace Votissimo;

use PDO;
use PDOException;

/**
 * Classe Database
 *
 * Gère la connexion à la base de données MySQL via PDO.
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct($host, $user, $password, $dbname)
    {
        require_once __DIR__ . '/../config/config.php';
        if (!defined('DB_HOST')) {
            die('La constante DB_HOST n\'est pas définie. Vérifiez config/config.php.');
        }
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8";
        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            require_once __DIR__ . '/../config/config.php';
            self::$instance = new self(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
