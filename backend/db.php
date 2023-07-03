<?php
namespace db;

use mysqli;

// Établir une connexion à MySQL et exécuter une requête
$mysqli = new mysqli("localhost", "root", "", "reseller_experience");

// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
    $error = "Échec de la connexion MySQL : " . $mysqli->connect_error;
    exit();
}

/**
 * Récupère les revendeurs qui n'ont pas de catégorie assignée
 * @return mixed|null Résultat de la requête
 */
function get_reseller()
{
    global $mysqli;

    $query = "SELECT r.*, m.id_cat, m.id_cat_automatica FROM reseller_experience_customer r LEFT JOIN maps_info m ON r.id = m.fk_lead WHERE m.id_cat IS NULL LIMIT 10;";

    try {
        $result_data = $mysqli->query($query);
        return $result_data;
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
    }
}

/**
 * Récupère les catégories
 * @return mixed|null Résultat de la requête
 */
function get_category()
{
    global $mysqli;

    $query = "SELECT id,title FROM category;";

    try {
        $result_data = $mysqli->query($query);
        return $result_data;
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return null;
    }
}

/**
 * Met à jour la catégorie d'un revendeur
 * @param $modifiedData Données modifiées
 * @return false|null|string Résultat de la mise à jour au format JSON
 */
function update_reseller_category($modifiedData)
{
    $response = array();
    try {
        foreach ($modifiedData['modifiedData'] as $row) {
            $id     = $row["id"];
            $id_cat = $row['id_cat'];
            $count  = get_verify_fk_lead_exists($row, $id, $id_cat);
            if ($count > 0) {
                update_fk_lead($id, $id_cat);
                array_push($response, array("status" => "OK", "message" => "Mise à jour réussie pour l'enregistrement avec l'ID : " . $id));
            }
            else {
                $tableau = get_adress_reseller($id);
                set_fake_maps_info($tableau);
                array_push($response, array("status" => "OK", "message" => "Mise à jour réussie pour l'enregistrement avec l'ID : " . $id));
            }
        }
        return json_encode(array('messages' => $response));
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return null;
    }
}

/**
 * Vérifie si un enregistrement avec fk_lead existe dans la table maps_info
 * @param $row Ligne de données
 * @param $id Identifiant de l'enregistrement
 * @param $id_cat Identifiant de catégorie
 * @return int|null Nombre d'enregistrements trouvés
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
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return null;
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
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}

/**
 * Récupère l'adresse d'un revendeur
 * @param $id Identifiant de l'enregistrement
 * @return mixed|null Tableau contenant les informations d'adresse
 */
function get_adress_reseller($id)
{
    global $mysqli;

    $query = "SELECT id, Indirizzo, Comune, CAP, Provincia FROM reseller_experience_customer WHERE id = $id";

    try {
        $result          = $mysqli->query($query);
        $tableau_adresse = $result->fetch_all();
        return $tableau_adresse;
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return null;
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
            $fk_lead           = $row[0];
            $jsondata          = json_encode(
                array(
                    "formatted_address"      => $row[1] . ", " . $row[3] . ", " . $row[2] . ", Italy",
                    "formatted_phone_number" => "123 xxx 6789",
                    "name"                   => "Example",
                    "website"                => "http://www.example.it/"
                )
            );
            $website           = "example.it";
            $warning           = rand(0, 1);
            $id_cat            = null;
            $id_cat_automatica = $id_cat ? $id_cat : (rand(0, 10) ? null : null);

            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("isssii", $fk_lead, $jsondata, $website, $warning, $id_cat, $id_cat_automatica);
            $stmt->execute();
        }
        $stmt->close();
        return true;
    }
    catch (\Throwable $th) {
        echo "Une erreur s'est produite : " . $th->getMessage();
        return false;
    }
}