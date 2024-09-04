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
        $this->conn = new mysqli("localhost", "root", "", "classes");
        if ($this->conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $this->conn->connect_error);
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);
            if ($stmt->execute()) {
                $stmt->close();
                return "Inscription réussie. Vous pouvez maintenant vous connecter.";
            } else {
                return "Échec de l'inscription : " . $stmt->error;
            }
        } else {
            return "Erreur de préparation de la requête : " . $this->conn->error;
        }
    }

    public function login($login, $password) {
        // Préparer la requête SQL
        $sql = "SELECT id, login, email, firstname, lastname, password FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return "Erreur de préparation de la requête : " . $this->conn->error;
        }
        // Lier les paramètres et exécuter la requête
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier les résultats
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
    
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['userid'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
    
                $this->login = $user['login']; // Assigner les valeurs pour l'objet actuel
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                

                $stmt->close();
                return "Connexion réussie.";
            } else {
                $stmt->close();
                return "Mot de passe incorrect.";
            }
        } else {
            $stmt->close();
            return "Utilisateur non trouvé. Login fourni : " . htmlspecialchars($login);
        }
    }

    public function update($login, $password, $email, $firstname, $lastname) {
        if (!isset($_SESSION['login'])) {
            return "Erreur: Vous devez être connecté pour mettre à jour vos informations.";
        }
    
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        // Préparer la requête SQL pour la mise à jour
        $sql = "UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE login = ?";
        $stmt = $this->conn->prepare($sql);
    
        if ($stmt) {
            // Lier les paramètres de la requête
            $stmt->bind_param("ssssss", $login, $hashedPassword, $email, $firstname, $lastname, $_SESSION['login']);
            
            if ($stmt->execute()) {
                // Mettre à jour les informations de session
                $_SESSION['login'] = $login;
                $_SESSION['email'] = $email;
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
    
                $stmt->close();
                return "Informations mises à jour avec succès.";
            } else {
                return "Échec de la mise à jour : " . $stmt->error;
            }
        } else {
            return "Erreur de préparation de la requête : " . $this->conn->error;
        }
    }
    

    public function delete() {
        if (!isset($_SESSION['login'])) {
            return "Erreur : Vous devez être connecté pour supprimer votre compte.";
        }
    
        // Préparer la requête SQL pour supprimer l'utilisateur
        $sql = "DELETE FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);
    
        if ($stmt) {
            // Lier le login à la requête
            $stmt->bind_param("s", $_SESSION['login']);
    
            if ($stmt->execute()) {
                // Détruire la session après suppression
                session_destroy();
                $stmt->close();
                return "Compte supprimé avec succès.";
            } else {
                return "Échec de la suppression du compte : " . $stmt->error;
            }
        } else {
            return "Erreur de préparation de la requête : " . $this->conn->error;
        }
    }
    

    public function disconnect() {
        session_destroy(); // Détruire la session
        // Optionnel : Réinitialiser les attributs
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
    $sql = "SELECT * FROM utilisateurs WHERE login = ?";
    $stmt = $this->conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $infos = $result->fetch_assoc();
            $stmt->close();
            return $infos;
        } else {
            $stmt->close();
            return "Aucune information trouvée pour l'utilisateur.";
        }
    } else {
        return "Erreur de préparation de la requête : " . $this->conn->error;
    }
}


    public function getLogin() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT login FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);

        if($stmt){
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0){
                $infos = $result->fetch_assoc();
                $stmt->close();
                return $infos;
            } else {
                $stmt->close();
                return "Aucune pseudonyme trouvée pour l'utilisateur.";
            }
        } else{
            return "Erreur de préparation de la requête :". $this->conn->error;
        }
    }

    public function getEmail() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT email FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt){
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0){
                $infos = $result->fetch_assoc();
                $stmt->close();
                return $infos;
            } else {
                $stmt->close();
                return "Aucune adresse email trouvée pour cet utilisateurs.";
            }
        } else {
            return "Erreur de préparation de la requête :" . $this->conn->error;
        }
    }

    public function getFirstname() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT firstname FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);

        if($stmt){
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0){
                $infos = $result->fetch_assoc();
                $stmt->close();
                return $infos;
            } else {
                return "Aucun Prénom trouve pour cet utilisateur.";
            }
        } else {
            return "Erreur de préparation de la requête:" . $this->conn->error;
        }
    }

    public function getLastname() {
        if (!isset($_SESSION['login'])){
            return "Vous devez être connecté.";
        }
        $login = $_SESSION['login'];
        $sql = "SELECT lastname FROM utilisateurs WHERE login = ?";
        $stmt = $this->conn->prepare($sql);

        if($stmt){
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0){
                $infos = $result->fetch_assoc();
                $stmt->close();
                return $infos;
            } else {
                return "Aucun nom trouvé pour cet utilisateur.";
            }
        } else {
            return "Erreur de préparation de la requête:" . $this->conn->error;
        }
    }
}
?>
