<?php
namespace Votissimo;

use PDO;

/**
 * Classe User
 *
 * Représente un utilisateur de l'application Votissimo.
 *
 * @package Votissimo
 */
class User
{
    private $id;
    private $username;
    private $password;
    private $role;

    public function __construct($id = null, $username, $password, $role = 'Visiteur')
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function register(PDO $pdo)
    {
        $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $pdo->prepare($sql);
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $this->role);
        return $stmt->execute();
    }

    public static function login(PDO $pdo, $username, $password)
    {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userData && password_verify($password, $userData['password'])) {
            return new self($userData['id'], $userData['username'], $userData['password'], $userData['role']);
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRole()
    {
        return $this->role;
    }
}
