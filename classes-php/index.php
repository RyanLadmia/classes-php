<?php
require_once 'User.php';
session_start();
$msg = '';
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] !== null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    
    if (isset($_POST['register_button'])) {
        $msg = $user->register($_POST['login'], $_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
    } elseif (isset($_POST['login_submit'])) {
        $result = $user->login($_POST['login'], $_POST['password']);
        if ($result === "Connexion réussie.") {
            $isLoggedIn = true;
            $_SESSION['login'] = $_POST['login'];
            $msg = $result;
        } else {
            $msg = $result;
        }
    } elseif (isset($_POST['update_button'])) {
        $msg = $user->update($_POST['login'], $_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
    } elseif (isset($_POST['logout_button'])) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $isLoggedIn = false;
        $msg = "Vous avez été déconnecté.";
    } elseif (isset($_POST['delete_button'])) {
        $showConfirmationButtons = true;
    } elseif (isset($_POST['confirm_delete'])) {
        $msg = $user->delete();
        $isLoggedIn = false;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $msg = "Votre compte a bien été supprimé.";
    } elseif (isset($_POST['cancel_delete'])) {
        $msg = "Suppression annulée.";
        $showConfirmationButtons = false;

    } elseif (isset($_POST['all_datas'])) {
        $infos = $user->getAllInfos();
        if ($infos) {
            $msg = "Vos informations :<br>Pseudo: {$infos['login']}<br>Email: {$infos['email']}<br>Prénom: {$infos['firstname']}<br>Nom: {$infos['lastname']}";
        } else {
            $msg = "Impossible de récupérer les informations.";
        }
    } elseif (isset($_POST['get_the_login'])) {
        $infos = $user->getLogin();
        if ($infos){
            $msg = "Votre pseudonyme :<br> {$infos['login']}";
        } else {
            $msg = "Impossible de récupérer le pseudonyme.";
        }
    } elseif (isset($_POST['get_the_email'])){
        $infos = $user->getEmail();
        if ($infos){
            $msg = "Votre adresse email : <br> {$infos['email']}";
        } else {
            $msg = "Impossible de récupérer l'adresse email.";
        }
    } elseif (isset($_POST['get_the_firstname'])){
        $infos = $user->getFirstname();
        if ($infos){
            $msg = "Votre prénom : <br> {$infos['firstname']}";
        } else {
            $msg = "Impossible de récupérer le prénom.";
        }
    } elseif (isset($_POST['get_the_lastname'])){
        $infos = $user->getLastname();
        if ($infos){
            $msg = "Votre nom : <br> {$infos['lastname']}";
        } else {
            $msg = "Impossible de récupérer le nom.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes PHP - Connexion et Inscription</title>
    <style>
        .form-container { display: none; }
        .active { display: block; }
    </style>
</head>
<body>

    <?php if (!$isLoggedIn): ?>
        <div>
            <button onclick="showForm('login-form')">Connexion</button>
            <button onclick="showForm('register-form')">Inscription</button>
        </div>

        <div id="login-form" class="form-container">
            <h2>Connexion</h2>
            <form method="post">
                <label for="login">Pseudonyme</label><br>
                <input type="text" name="login" placeholder="Login" required><br><br>
                <label for="password">Mot de passe</label><br>
                <input type="password" name="password" placeholder="Mot de passe" required><br><br>
                <button type="submit" name="login_submit">Se connecter</button>
            </form>
        </div>

        <div id="register-form" class="form-container">
            <h2>Inscription</h2>
            <form method="post">
                <label for="login">Pseudonyme</label><br>
                <input type="text" name="login" placeholder="Login" required><br><br>
                <label for="email">Adresse email</label><br>
                <input type="email" name="email" placeholder="Email" required><br><br>
                <label for="password">Mot de passe</label><br>
                <input type="password" name="password" placeholder="Mot de passe" required><br><br>
                <label for="firstname">Prénom</label><br>
                <input type="text" name="firstname" placeholder="Prénom" required><br><br>
                <label for="lastname">Nom</label><br>
                <input type="text" name="lastname" placeholder="Nom" required><br><br>
                <button type="submit" name="register_button">S'inscrire</button>
            </form>
        </div>

    <?php else: ?>
        <div>
            <h2>Bienvenue, <?= htmlspecialchars($_SESSION['login']); ?></h2>
            <form method="post">
                <label for="login">Nouveau Pseudonyme</label><br>
                <input type="text" name="login" placeholder="Login" required><br><br>
                <label for="email">Nouvelle Adresse email</label><br>
                <input type="email" name="email" placeholder="Email" required><br><br>
                <label for="password">Nouveau Mot de passe</label><br>
                <input type="password" name="password" placeholder="Mot de passe" required><br><br>
                <label for="firstname">Nouveau Prénom</label><br>
                <input type="text" name="firstname" placeholder="Prénom" required><br><br>
                <label for="lastname">Nouveau Nom</label><br>
                <input type="text" name="lastname" placeholder="Nom" required><br><br>
                <button type="submit" name="update_button">Mettre à jour</button>
            </form><br>

            <form method="post">
                <button type="submit" name="logout_button">Se déconnecter</button>
            </form><br>

            <!-- Bouton pour récupérer toutes les infos -->
            <form method="post">
                <button type="submit" name="all_datas">Récupérer toutes les infos</button>
            </form><br>

            <!-- Bouton pour récupérer le login/pseudonyme -->
            <form method="post">
                <button type="submit" name="get_the_login">Récupérer le pseudonyme</button>
            </form><br>

            <!-- Bouton pour récupérer l'email -->
             <form method="post">
                <button type="submit" name="get_the_email">Récupérer l'adresse email</button>
             </form><br>

             <!-- Bouton pour récupérer le prénom -->
              <form method="post">
                <button type ="submit" name="get_the_firstname">Récupérer le prénom</button>
              </form><br>

              <!-- Bouton pour récupérer le nom -->
               <form method="post">
                    <button type="submit" name="get_the_lastname">Récupérer le nom</button>
               </form><br>

            <!-- Bouton de suppression de compte -->
            <?php if (!isset($showConfirmationButtons) || !$showConfirmationButtons): ?>
                <form method="post">
                    <button type="submit" name="delete_button">Supprimer mon compte</button>
                </form>
            <?php else: ?>
                <form method="post">
                    <p>Êtes-vous sûr de vouloir supprimer votre compte ?</p>
                    <button type="submit" name="confirm_delete">Oui</button>
                    <button type="submit" name="cancel_delete">Non</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($msg) echo "<p>$msg</p>"; ?>
    <script>
        function showForm(formId) {
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('register-form').classList.remove('active');
            document.getElementById(formId).classList.add('active');
        }
    </script>
</body>
</html>
