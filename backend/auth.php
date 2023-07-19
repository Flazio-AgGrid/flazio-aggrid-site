<?php
namespace auth;

require_once 'db.php';

/**
 * Récupère les informations de tous les utilisateurs depuis la base de données.
 *
 * @return array | bool Les informations de tous les utilisateurs au format JSON.
 */
function get_alluserinfo()
{
    $alluserinfo = \db\get_alluserinfo();
    $result      = array();

    if ($alluserinfo) {

        while ($row = $alluserinfo->fetch_assoc()) {
            // Convertir le statut en chaîne de caractères lisible
            $lastconnection = $row['lastconnection'];
            $userId         = $row['id'];
            $status         = "";
            switch ($row['status']) {
                case 0:
                    $status = 'connected';
                    break;
                case 1:
                    $status = 'offline';
                    break;
                case 2:
                    $status = 'actived';
                    break;
                case 3:
                    $status = 'disabled';
                    break;
            }

            // Les variables $status et $lastconnection ne sont pas utilisées ici, donc elles peuvent être supprimées.
            array_push($result, array("userId" => $userId, "username" => $row['username'], "status" => array("titleStatus" => $status, "idStatus" => $row['status']), "lastConnection" => $lastconnection));
        }


        // Retourner les données au format JSON
        return $result;
    }
    else {
        return false;
    }

}

/**
 * Fonction de connexion utilisateur.
 *
 * @param string $username Nom d'utilisateur saisi.
 * @param string $password Mot de passe saisi.
 * @return void
 */
function login($username, $password)
{
    $userinfo = \db\get_username($username);

    if ($userinfo && $row = $userinfo->fetch_assoc()) {
        $hashedPasswordFromDB = $row['password'];
        $userId               = $row['id'];

        // Vérifier le mot de passe saisi avec le mot de passe haché de la base de données
        if (password_verify($password, $hashedPasswordFromDB)) {
            $token = generateAuthToken();
            // Générer un jeton d'authentification unique
            $authToken = array("id" => $userId, "token" => trim($token));
            // Enregistrer le jeton d'authentification
            if (saveAuthToken($userId, $token)) {
                // Enregistrer le jeton d'authentification dans un cookie
                setcookie('authToken', json_encode($authToken), time() + 3600, '/');

                modifiedStatus($userId, 0);

                // Authentification réussie
                $_SESSION['authenticated'] = true;
                header('Location: ../index.php');
                exit;
            }
        }
    }

    // Le nom d'utilisateur ou le mot de passe est incorrect ou une erreur s'est produite
    //header('Location: ./erreur.php');
    exit;
}

/**
 * Vérifie si l'utilisateur est connecté en vérifiant le cookie d'authentification.
 *
 * @return bool Renvoie true si l'utilisateur est connecté, sinon false.
 */
function checkLogin()
{
    // Vérifier si le cookie d'authentification existe
    if (isset($_COOKIE['authToken'])) {
        // \db\checkOnline();
        // Décoder le cookie d'authentification pour obtenir les données du jeton
        $authTokenCookie = json_decode($_COOKIE['authToken'], true);

        // Vérifier si les données du jeton sont valides et non vides
        if (
            validateAuthToken($authTokenCookie['id'], $authTokenCookie['token'])
        ) {
            // Utiliser les données du jeton pour vérifier si le jeton est valide dans la base de données
            \db\keepAlive($authTokenCookie['id']);
            return true;
        }
        else {
            // Si les données du jeton sont invalides ou vides, déconnecter l'utilisateur
            logout();
            return false;
        }
    }
    else {
        // Le cookie d'authentification n'existe pas, l'utilisateur doit se connecter
        logout();
        return false;
    }
}

/**
 * Déconnecte l'utilisateur en supprimant le jeton d'authentification et le cookie correspondant.
 */
function logout()
{
    if (isset($_COOKIE['authToken'])) {
        // Supprimer le jeton d'authentification de votre système
        $authToken = json_decode($_COOKIE['authToken'], true);
        removeAuthToken($authToken['id']);
    }
    // Supprimer le cookie d'authentification
    setcookie('authToken', '', time() - 3600, '/');

    if (session_status() === PHP_SESSION_ACTIVE) {
        // Détruire la session en cours
        session_destroy();
    }


    // Réinitialiser les données de session
    $_SESSION = array();
    header('Location: ../auth/erreur.php');
}

/**
 * Génère un jeton d'authentification unique.
 *
 * @return string Le jeton d'authentification généré.
 */
function generateAuthToken()
{
    $token = bin2hex(random_bytes(32)); // Génère une chaîne aléatoire de 32 caractères hexadécimaux
    return $token;
}

/**
 * Enregistre le jeton d'authentification dans votre système.
 *
 * @param int    $userId     L'ID de l'utilisateur.
 * @param string $authToken  Le jeton d'authentification.
 * @return bool              Renvoie true si l'enregistrement du jeton a réussi, sinon false.
 */
function saveAuthToken($userId, $authToken)
{
    if (\db\saveAuthToken($userId, $authToken)) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Vérifie si le jeton d'authentification est valide et correspond à celui enregistré dans votre système.
 *
 * @param int    $userId     L'ID de l'utilisateur.
 * @param string $authToken  Le jeton d'authentification.
 * @return bool              Renvoie true si le jeton d'authentification est valide, sinon false.
 */
function validateAuthToken($userId, $authToken)
{
    return \db\validateAuthToken($userId, $authToken);
}

/**
 * Supprime le jeton d'authentification de votre système.
 *
 * @param int $userId  L'ID de l'utilisateur.
 * @return bool        Renvoie true si la suppression du jeton a réussi, sinon false.
 */
function removeAuthToken($userId)
{
    return \db\saveAuthToken($userId, NULL);
}

function modifiedStatus($userId, $idStatus)
{
    return \db\modifiedStatus($userId, $idStatus);
}

function modifiedUsername($userId, $idStatus)
{
    return \db\modifiedUsername($userId, $idStatus);
}

function modifiedPassword($userId, $password)
{
    return \db\modifiedPassword($userId, $password);
}

function modifiedRole($userId, $role)
{
    return \db\modifiedRole($userId, $role);
}

function registerUser($username, $password)
{
    // Chiffrer le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Appeler la fonction pour enregistrer l'utilisateur dans la base de données
    return \db\set_register($username, $hashedPassword);
}

function deleteUser($userId)
{
    return \db\deleteUser($userId);

}

?>