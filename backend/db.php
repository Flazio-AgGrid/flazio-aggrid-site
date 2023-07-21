<?php

namespace db;

use mysqli;
use mysqli_sql_exception;

// Établir une connexion à MySQL et exécuter une requête
$mysqli = new mysqli("localhost", "root", "", "reseller_experience");

// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
    $error = "Échec de la connexion MySQL : " . $mysqli->connect_error;
    exit();
}

/**
 * Récupère les revendeurs qui n'ont pas de catégorie assignée
 * @return mixed|false Résultat de la requête
 */
function get_reseller()
{
    global $mysqli;

    $query = "SELECT r.*, m.id_cat, m.id_cat_automatica FROM reseller_experience_customer r LEFT JOIN maps_info m ON r.id = m.fk_lead WHERE m.id_cat IS NULL LIMIT 10;";

    try {
        $result_data = $mysqli->query($query);
        return $result_data;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Récupère les catégories
 * @return mixed|false Résultat de la requête
 */
function get_category()
{
    global $mysqli;

    $query = "SELECT id,title FROM category;";

    try {
        $result_data = $mysqli->query($query);
        return $result_data;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Vérifie si un enregistrement avec fk_lead existe dans la table maps_info
 * @param $row Ligne de données
 * @param $id Identifiant de l'enregistrement
 * @param $id_cat Identifiant de catégorie
 * @return int|false Nombre d'enregistrements trouvés
 */
function get_verify_fk_lead_exists($row, $id, $id_cat)
{
    global $mysqli;

    $checkSql = "SELECT COUNT(*) FROM maps_info WHERE fk_lead = ?";

    try {
        $count = 0;
        // Vérifie si l'enregistrement existe
        $stmt = $mysqli->prepare($checkSql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Met à jour la catégorie de fk_lead dans la table maps_info
 * @param $id Identifiant de l'enregistrement
 * @param $id_cat Identifiant de catégorie
 * @return bool Succès ou échec de la mise à jour
 */
function update_fk_lead($id, $id_cat)
{
    global $mysqli;

    $updateSql = "UPDATE maps_info SET id_cat = ? WHERE fk_lead = ?";

    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("ii", $id_cat, $id);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Récupère l'adresse d'un revendeur
 * @param $id Identifiant de l'enregistrement
 * @return mixed|false Tableau contenant les informations d'adresse
 */
function get_adress_reseller($id)
{
    global $mysqli;

    $query = "SELECT id, Indirizzo, Comune, CAP, Provincia FROM reseller_experience_customer WHERE id = $id";

    try {
        $result = $mysqli->query($query);
        $tableau_adresse = $result->fetch_all();
        return $tableau_adresse;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Insère de fausses informations dans la table maps_info
 * @param $tableau Tableau d'adresses
 * @return bool Succès ou échec de l'insertion
 */
function set_fake_maps_info($tableau)
{
    global $mysqli;

    $query = "INSERT INTO maps_info (fk_lead, jsondata, website, warning, id_cat, id_cat_automatica) VALUES (?, ?, ?, ?, ?, ?)";

    try {
        foreach ($tableau as $row) {
            $fk_lead = $row[0];
            $jsondata = json_encode(
                array(
                    "formatted_address" => $row[1] . ", " . $row[3] . ", " . $row[2] . ", Italy",
                    "formatted_phone_number" => "123 xxx 6789",
                    "name" => "Example",
                    "website" => "http://www.example.it/"
                )
            );
            $website = "example.it";
            $warning = rand(0, 1);
            $id_cat = null;
            $id_cat_automatica = $id_cat ? $id_cat : (rand(0, 10) ? null : null);

            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("isssii", $fk_lead, $jsondata, $website, $warning, $id_cat, $id_cat_automatica);
            $stmt->execute();
        }
        $stmt->close();
        return true;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Récupère un utilisateur en fonction de son nom d'utilisateur et de son mot de passe.
 * @param string $username - Le nom d'utilisateur.
 * @return mysqli_result | false - Le résultat de la requête si elle réussit, sinon false en cas d'erreur.
 */
function get_username($username)
{
    global $mysqli;

    $query = "SELECT username, password, id, role , status FROM users WHERE username = ?";

    try {
        // Utiliser une requête préparée pour éviter l'injection SQL
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}


/**
 * Insère un nouvel enregistrement dans la table "users" avec un nom d'utilisateur et un mot de passe donnés.
 *
 * @param string $username Le nom d'utilisateur à insérer.
 * @param string $hashedPassword Le mot de passe haché à insérer.
 * @return bool Retourne true si l'insertion a réussi, sinon false.
 */
function set_register($username, $hashedPassword, $role)
{
    global $mysqli;

    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

    try {
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param(
            "sss",
            $username,
            $hashedPassword,
            $role
        );
        $stmt->execute();
        $stmt->close();
        return true;
    } catch (\Throwable $th) {

        if ($th->getCode() === 1062) {
            echo "Nom d'utilisateur déjà utilisé.";
            return false;
        } else {
            echo "Une erreur s'est produite : " . $th->getMessage();
            return false;
        }
    }
}

/**
 * Récupère les informations des revendeurs qui n'ont pas de catégorie automatique assignée.
 * @return mysqli_result|false - Résultat de la requête contenant les informations des revendeurs, ou false en cas d'erreur.
 */
function get_reseller_set_manually()
{
    global $mysqli;

    $query = "SELECT r.id, r.AccountID, r.TaxRegID, r.Nome, r.Cognome, r.RagioneSociale, r.Email, r.Indirizzo, r.Comune, r.CAP, r.Provincia, r.Tel, r.NumMobile, r.NumPagineDaAttivare, r.NumPagineAttivate, r.status, r.create_dt, r.update_dt, r.readed, r.reseller_experience_manager_id, r.CAOName, IF(r.lead_status_cat IS NULL, 1, r.lead_status_cat) AS lead_status, m.id_cat, m.id_cat_automatica FROM reseller_experience_customer r INNER JOIN maps_info m ON r.id = m.fk_lead LEFT JOIN lead_status l ON r.lead_status_cat = l.id WHERE m.id_cat_automatica IS NULL AND m.id_cat IS NOT NULL;";

    try {
        $result_data = $mysqli->query($query);
        return $result_data;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Récupère les informations des statuts de leads.
 * @return mysqli_result|false - Résultat de la requête contenant les informations des statuts, ou false en cas d'erreur.
 */
function get_status()
{
    global $mysqli;

    $query = "SELECT id, title FROM lead_status;";

    try {
        $result_data = $mysqli->query($query);
        return $result_data;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function update_status_lead($id, $lead_status)
{
    global $mysqli;

    $updateSql = "UPDATE reseller_experience_customer SET lead_status_cat = ? WHERE reseller_experience_customer.id = ?";

    try {
        // Vérifie si l'enregistrement existe
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("ii", $lead_status, $id);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();

        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function get_alluserinfo()
{
    global $mysqli;

    // Récupération des utilisateurs depuis la base de données
    $query = "SELECT id, username, lastconnection, status, role FROM users ORDER BY username";

    try {
        $result = $mysqli->query($query);
        return $result->num_rows > 0 ? $result : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Vérifie si un jeton d'authentification est valide pour un utilisateur donné.
 *
 * @param int $userid ID de l'utilisateur.
 * @param string $token Jeton d'authentification à vérifier.
 * @return bool Renvoie true si le jeton est valide, sinon false.
 */
function validateAuthToken($userid, $token)
{
    global $mysqli;

    // Requête pour récupérer les utilisateurs depuis la base de données
    $query = "SELECT token FROM users WHERE id = ?";

    try {
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $dbToken = $row['token'];
            return $dbToken === $token;
        } else {
            return false;
        }
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function saveAuthToken($userId, $token)
{
    global $mysqli;
    $updateSql = "UPDATE users SET token = ? WHERE id = ?";

    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("si", $token, $userId);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();
        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function keepAlive($userId)
{
    global $mysqli;

    $updateSql = "UPDATE users SET lastconnection = ? WHERE id = ?";
    $lastconnection = date('Y-m-d H:i:s');
    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("si", $lastconnection, $userId);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();
        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function checkOnline()
{
    global $mysqli;

    $updateSql = "UPDATE users SET STATUS = CASE WHEN TIMESTAMPDIFF(MINUTE, lastconnection, NOW()) > 10 OR token IS NULL THEN 1 ELSE 0 END,
    token = CASE WHEN STATUS = 1 AND TIMESTAMPDIFF(MINUTE, lastconnection, NOW()) > 30 THEN NULL ELSE token END;";
    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function modifiedStatus($userId, $idStatus)
{
    global $mysqli;

    $updateSql = "UPDATE users SET status = ? WHERE id = ?";

    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("ss", $idStatus, $userId);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();
        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}
function modifiedUsername($userId, $username)
{
    global $mysqli;

    $updateSql = "UPDATE users SET username  = ? WHERE id = ?";

    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("si", $username, $userId);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();
        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}
function modifiedPassword($userId, $password)
{
    global $mysqli;

    $updateSql = "UPDATE users SET password  = ? WHERE id = ?";

    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("si", $password, $userId);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();
        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function modifiedRole($userId, $role)
{
    global $mysqli;

    $updateSql = "UPDATE users SET role  = ? WHERE id = ?";

    try {
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("si", $role, $userId);
        $stmt->execute();
        $rowCount = $stmt->affected_rows;
        $stmt->close();
        return $rowCount === 1 ? true : false;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Récupère un log spécifique par son ID.
 * @param int $id L'ID du log à récupérer.
 * @return mixed|array|false Retourne un tableau contenant le log si la requête est réussie, sinon retourne false.
 */
function get_log_by_reseller_id($id)
{
    global $mysqli;

    $getLogSql = "SELECT l.id, username, objectToLog, l.status, dateTime, oldData, newData FROM log l INNER JOIN users u ON l.initiator = u.id INNER JOIN reseller_experience_customer r ON l.objectToLog = r.id WHERE r.id = ?";

    try {
        $stmt = $mysqli->prepare($getLogSql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function get_log_by_user_id($id, $status, $column)
{
    global $mysqli;

    $getLogSql = "SELECT id, initiator as  username, objectToLog, status, dateTime, oldData, newData FROM log WHERE initiator = ?";
    if ($column) {
        $getLogSql .= ' AND ' . $column . ' = ?';
    }
    try {
        $stmt = $mysqli->prepare($getLogSql);

        if ($column) {
            $stmt->bind_param("is", $id, $status);
        } else {
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}



function set_log($status, $initiator, $objectToLog, $oldData, $newData)
{
    global $mysqli;

    $updateSql = "INSERT INTO log (status, objectToLog, initiator, dateTime, oldData,newData) VALUES (?, ?, ?, NOW(),?, ?)";

    try {
        // Vérifie si l'enregistrement existe
        $stmt = $mysqli->prepare($updateSql);
        $stmt->bind_param("siiss", $status, $objectToLog, $initiator, $oldData, $newData);
        $stmt->execute();
        $stmt->close();

        return true;
    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

function deleteUser($userId)
{
    global $mysqli;

    // Requête pour supprimer l'utilisateur avec l'ID donné
    $deleteSql = "DELETE FROM users WHERE id = ?";

    try {
        // Préparer la requête
        $stmt = $mysqli->prepare($deleteSql);

        // Lier le paramètre de l'ID de l'utilisateur
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        return true; // La suppression a réussi

    } catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false; // La suppression a échoué en raison d'une erreur
    }
}