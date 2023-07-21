<?php

namespace auth;

require_once 'db.php';
require_once 'logs.php';

/**
 * Récupère les informations de tous les utilisateurs depuis la base de données.
 *
 * @return array | bool Les informations de tous les utilisateurs au format JSON.
 */
function get_alluserinfo()
{
    $alluserinfo = \db\get_alluserinfo();
    $result = array();

    if ($alluserinfo) {

        while ($row = $alluserinfo->fetch_assoc()) {
            // Convertir le statut en chaîne de caractères lisible
            $lastconnection = $row['lastconnection'];
            $userId = $row['id'];
            $status = "";
            $role = "";
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

            switch ($row['role']) {
                case 1:
                    $role = 'Read-Only';
                    break;
                case 2:
                    $role = 'Read/Write';
                    break;
                case 3:
                    $role = 'Admin';
                    break;
            }

            // Les variables $status et $lastconnection ne sont pas utilisées ici, donc elles peuvent être supprimées.
            array_push($result, array("userId" => $userId, "username" => $row['username'], "status" => array("titleStatus" => $status, "idStatus" => $row['status']), "role" => array("titleRole" => $role, "idRole" => $row['role']), "lastConnection" => $lastconnection));
        }


        // Retourner les données au format JSON
        return $result;
    } else {
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
        $userId = $row['id'];
        $role = $row['role'];
        $status = $row['status'];
        // Vérifier le mot de passe saisi avec le mot de passe haché de la base de données
        if ($status !== 3)
            if (password_verify($password, $hashedPasswordFromDB)) {
                $token = generateAuthToken();
                // Générer un jeton d'authentification unique
                $authToken = array("id" => $userId, "token" => $token, "role" => $role);
                // Enregistrer le jeton d'authentification
                if (saveAuthToken($userId, $token)) {
                    \log\set_log_user('login', $userId, null, null);
                    // Enregistrer le jeton d'authentification dans un cookie
                    setcookie('authToken', json_encode($authToken), time() + 3600, '/');

                    modifiedStatus($userId, 0);
                    \db\keepAlive($userId);

                    // Authentification réussie
                    $_SESSION['authenticated'] = true;
                    header('Location: ../index.php');
                    exit;
                }
            }
    }

    // Le nom d'utilisateur ou le mot de passe est incorrect ou une erreur s'est produite
    header('Location: ./erreur.php');
    exit;
}

/**
 * Vérifie si l'utilisateur est connecté et autorisé en fonction du rôle, en vérifiant le cookie d'authentification.
 *
 * @param int|array $exclude Un seul rôle ou un tableau de rôles à exclure de la connexion. Par défaut, aucun rôle n'est exclu (valeur 0).
 * @return bool Renvoie true si l'utilisateur est connecté et autorisé, sinon false.
 */
function checkLogin($exclude = 0)
{
    // Vérifier si le cookie d'authentification existe
    if (isset($_COOKIE['authToken'])) {
        // Vérifier si l'utilisateur est en ligne
        \db\checkOnline();

        // Décoder le cookie d'authentification pour obtenir les données du jeton
        $authTokenCookie = json_decode($_COOKIE['authToken'], true);

        // Récupérer le rôle de l'utilisateur à partir des données du jeton
        $roleUser = array($authTokenCookie['role']);

        // Vérifier si les données du jeton sont valides et non vides
        if (validateAuthToken($authTokenCookie['id'], $authTokenCookie['token'])) {
            // Si $exclude n'est pas un tableau, le convertir en tableau avec un seul élément
            if (!is_array($exclude)) {
                $exclude = array($exclude);
            }

            // Vérifier si le rôle de l'utilisateur est autorisé en fonction de $exclude
            if (count(array_intersect($exclude, $roleUser)) === 0) {
                // Actualiser le jeton d'authentification dans la base de données
                \db\keepAlive($authTokenCookie['id']);
                return true; // L'utilisateur est connecté et autorisé
            }
        } else {
            // Les données du jeton sont invalides ou vides, déconnecter l'utilisateur
            logout(); // Si vous souhaitez déconnecter l'utilisateur en cas de jeton invalide
            return false; // L'utilisateur n'est pas connecté ou autorisé
        }
    } else {
        // Le cookie d'authentification n'existe pas, déconnecter l'utilisateur
        logout(); // Si vous souhaitez déconnecter l'utilisateur en cas d'absence de cookie
        return false; // L'utilisateur n'est pas connecté ou autorisé
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
        \log\set_log_user('logout', $authToken['id'], null, null);
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
    } else {
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
    \log\set_log_user('modifiedStatus', $userId, 'status', array("idStatus" => $idStatus));
    return \db\modifiedStatus($userId, $idStatus);
}

function modifiedUsername($userId, $username)
{
    \log\set_log_user('modifiedUsername', $userId, 'initiator', array("idStatus" => $username));
    return \db\modifiedUsername($userId, $username);
}

function modifiedPassword($userId, $password)
{
    \log\set_log_user('modifiedPassword', $userId, NULL, NULL);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    return \db\modifiedPassword($userId, $hashedPassword);
}

function modifiedRole($userId, $role)
{
    \log\set_log_user('modifiedUsername', $userId, 'role', array("role" => $role));
    return \db\modifiedRole($userId, $role);
}

function registerUser($username, $password, $role)
{
    // Chiffrer le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Appeler la fonction pour enregistrer l'utilisateur dans la base de données
    return \db\set_register($username, $hashedPassword, $role);
}

function deleteUser($userId)
{
    return \db\deleteUser($userId);
}

function editAccount($userId, $username, $password, $role)
{
    if ($userId) {
        $result = array();
        if ($username) {
            array_push($result, array("message" => "Username", "status" => modifiedUsername($userId, $username)));
        }
        if ($password) {
            array_push($result, array("message" => "Password", "status" => modifiedPassword($userId, $password)));
            ;
        }
        if ($role) {
            array_push($result, array("message" => "Role", "status" => modifiedRole($userId, $role)));
            ;
        }
        return json_encode($result);
    } else {
        return json_encode(array("status" => false, "message" => "Need UserID"));
    }
}