<?php
namespace auth;

require_once 'db.php';

/**
 * Récupère les informations de tous les utilisateurs depuis la base de données.
 *
 * @return string Les informations de tous les utilisateurs au format JSON.
 */
function get_alluserinfo()
{
    $alluserinfo = \db\get_alluserinfo();
    $result      = array();


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
                $status = 'deactivated';
                break;
        }

        // Les variables $status et $lastconnection ne sont pas utilisées ici, donc elles peuvent être supprimées.

        array_push($result, array("userId" => $userId, "username" => $row['username'], "status" => $status, "lastConnexion" => $lastconnection));
    }

    // Retourner les données au format JSON
    return json_encode(array("data" => $result));
}

// Lorsque l'utilisateur se connecte avec succès
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

    if ($userinfo) {
        while ($row = $userinfo->fetch_assoc()) {
            $hashedPasswordFromDB = $row['password'];
            $userId               = $row['id'];

            if (password_verify($password, $hashedPasswordFromDB)) {
                // Authentification réussie
                $_SESSION['authenticated'] = true;

                $token = generateAuthToken();

                // Générer un jeton d'authentification unique
                $authToken = array("id" => $userId, "token" => $token);

                // Enregistrer le jeton d'authentification dans un cookie
                setcookie('authToken', json_encode($authToken), time() + 3600, '/');
                // Enregistrer le jeton d'authentification
                saveAuthToken($userId, $token);

                header('Location: ../index.php');
                exit;
            }
        }
    }
    else {
        echo 'Erreur de connexion';
    }
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
        $authToken = json_decode($_COOKIE['authToken'], true);

        // Vérifier si le jeton d'authentification correspond à celui enregistré dans votre système
        if (validateAuthToken($authToken['id'], $authToken['token'])) {
            // Autoriser la connexion
            return true;
        }
        else {
            // Refuser la connexion
            removeAuthToken($authToken['id']);
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
        // Supprimer le cookie d'authentification
        setcookie('authToken', '', time() - 3600, '/');
    }
    $_SESSION['authenticated'] = false;
    header('Location: ../auth/login.php');
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
    if (\db\validateAuthToken($userId, $authToken)) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Supprime le jeton d'authentification de votre système.
 *
 * @param int $userId  L'ID de l'utilisateur.
 * @return bool        Renvoie true si la suppression du jeton a réussi, sinon false.
 */
function removeAuthToken($userId)
{
    if (\db\saveAuthToken($userId, NULL)) {
        return true;
    }
    else {
        return false;
    }
}


?>