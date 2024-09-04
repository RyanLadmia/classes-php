<?php

class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $conn;

    public function __construct() {
        $this->connect(); // Connexion à la base de données
    }

    // Méthode pour établir la connexion à la base de données
    private function connect() {
        $dsn = "mysql:host=localhost;dbname=classes;charset=utf8";
        $username = "root";
        $password = "";

        try {
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("La connexion à la base de données a échoué : " . $e->getMessage());
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            
            if ($stmt->execute()) {
                return "Inscription réussie. Vous pouvez maintenant vous connecter.";
            } else {
                return "Échec de l'inscription.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function login($login, $password) {
        $sql = "SELECT id, login, email, firstname, lastname, password FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['userid'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];

                $this->login = $user['login']; 
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];

                return "Connexion réussie.";
            } else {
                return "Mot de passe incorrect ou utilisateur non trouvé.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if (!isset($_SESSION['login'])) {
            return "Erreur: Vous devez être connecté pour mettre à jour vos informations.";
        }
    
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        $sql = "UPDATE utilisateurs SET login = :login, password = :password, email = :email, firstname = :firstname, lastname = :lastname WHERE login = :currentLogin";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':currentLogin', $_SESSION['login']);
            
            if ($stmt->execute()) {
                $_SESSION['login'] = $login;
                $_SESSION['email'] = $email;
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
    
                return "Informations mises à jour avec succès.";
            } else {
                return "Échec de la mise à jour.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function delete() {
        if (!isset($_SESSION['login'])) {
            return "Erreur : Vous devez être connecté pour supprimer votre compte.";
        }
    
        $sql = "DELETE FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $_SESSION['login']);
            
            if ($stmt->execute()) {
                session_destroy();
                return "Compte supprimé avec succès.";
            } else {
                return "Échec de la suppression du compte.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function disconnect() {
        session_destroy();
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function isConnected() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    public function getAllInfos() {
        if (!isset($_SESSION['login'])) {
            return "Erreur : Vous devez être connecté.";
        }

        $login = $_SESSION['login'];
        $sql = "SELECT * FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $infos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($infos) {
                return $infos;
            } else {
                return "Aucune information trouvée pour l'utilisateur.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function getLogin() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT login FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $infos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($infos) {
                return $infos['login'];
            } else {
                return "Aucun pseudonyme trouvé pour l'utilisateur.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function getEmail() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT email FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $infos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($infos) {
                return $infos['email'];
            } else {
                return "Aucune adresse email trouvée pour cet utilisateur.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function getFirstname() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT firstname FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $infos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($infos) {
                return $infos['firstname'];
            } else {
                return "Aucun prénom trouvé pour cet utilisateur.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function getLastname() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT lastname FROM utilisateurs WHERE login = :login";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $infos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($infos) {
                return $infos['lastname'];
            } else {
                return "Aucun nom trouvé pour cet utilisateur.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}
?>
