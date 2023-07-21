<?php
namespace grid;

require_once 'db.php';
require_once 'logs.php';

/**
 * Récupère les données des revendeurs et des catégories depuis la base de données.
 *
 * @return string Les données des revendeurs et des catégories au format JSON.
 */
function get_reseller_category()
{
    // Récupérer les données des revendeurs depuis la base de données
    $result_data = \db\get_reseller();

    // Récupérer les données des catégories depuis la base de données
    $result_cat = \db\get_category();

    // Créer un tableau pour stocker les données des revendeurs
    $data = array();

    // Traiter les résultats de la requête des revendeurs
    while ($row = $result_data->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $data[] = $row;
    }

    // Créer un tableau pour stocker les données des catégories
    $cat = array();

    // Traiter les résultats de la requête des catégories
    while ($row = $result_cat->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $cat[] = $row;
    }

    // Créer un tableau final contenant les données des revendeurs et des catégories
    $finalData = array(
        "data"     => $data,
        "category" => $cat
    );

    // Convertir le tableau en JSON
    $jsonData = json_encode($finalData);

    // Retourner les données au format JSON
    return $jsonData;
}

/**
 * Met à jour les catégories des revendeurs en fonction des données modifiées reçues via une requête POST.
 *
 * @return string La réponse au format JSON contenant les messages de mise à jour.
 */
function update_reseller_category()
{
    // Tableau de réponse
    $response = array();

    // Récupérer les données modifiées depuis la requête POST
    $jsonData        = file_get_contents('php://input');
    $modifiedData    = json_decode($jsonData, true);
    $authTokenCookie = json_decode($_COOKIE['authToken'], true);

    foreach ($modifiedData['modifiedData'] as $row) {
        $id     = $row["id"];
        $id_cat = $row['id_cat'];
        $count  = \db\get_verify_fk_lead_exists($row, $id, $id_cat);

        if ($count > 0) {
            \log\set_log('updated', $authTokenCookie['id'], $id, array('id_cat' => $id_cat));

            \db\update_fk_lead($id, $id_cat);
            array_push($response, array("status" => "OK", "message" => "Mise à jour réussie pour l'enregistrement avec l'ID : " . $id));
        }
        else {
            $tableau = \db\get_adress_reseller($id);
            \log\set_log('updated', $authTokenCookie['id'], $id, array('id_cat' => $id_cat));

            \db\set_fake_maps_info($tableau);
            \db\update_fk_lead($id, $id_cat);
            array_push($response, array("status" => "OK", "message" => "Mise à jour réussie pour l'enregistrement avec l'ID : " . $id));
        }
    }

    // Appeler la fonction de mise à jour des catégories des revendeurs et récupérer la réponse
    $response = json_encode(array('messages' => $response));

    // Retourner la réponse au format JSON
    return $response;
}


function get_manually_reseller_category()
{
    // Récupérer les données des revendeurs depuis la base de données
    $result_data = \db\get_reseller_set_manually();

    // Récupérer les données des catégories depuis la base de données
    $result_cat = \db\get_category();

    $result_status = \db\get_status();

    // Créer un tableau pour stocker les données des revendeurs
    $data = array();

    // Traiter les résultats de la requête des revendeurs
    while ($row = $result_data->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $data[] = $row;
    }

    // Créer un tableau pour stocker les données des catégories
    $cat = array();

    // Traiter les résultats de la requête des catégories
    while ($row = $result_cat->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $cat[] = $row;
    }

    $status = array();

    while ($row = $result_status->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $status[] = $row;
    }

    // Créer un tableau final contenant les données des revendeurs et des catégories
    $finalData = array(
        "data"     => $data,
        "category" => $cat,
        "status"   => $status
    );

    // Convertir le tableau en JSON
    $jsonData = json_encode($finalData);

    // Retourner les données au format JSON
    return $jsonData;
}

function update_manually_reseller_category()
{
    // Tableau de réponse
    $response = array();

    // Récupérer les données modifiées depuis la requête POST
    $jsonData     = file_get_contents('php://input');
    $modifiedData = json_decode($jsonData, true);
    $row          = $modifiedData['modifiedData'];
    $id           = $row["id"];
    $lead_status  = $row['lead_status'];

    $authTokenCookie = json_decode($_COOKIE['authToken'], true);

    \log\set_log('updated', $authTokenCookie['id'], $id, array('lead_status' => $lead_status));

    if (\db\update_status_lead($id, $lead_status)) {
        array_push($response, array("status" => "OK", "message" => "Mise à jour réussie pour l'enregistrement avec l'ID : " . $id, "data" => $id . "&" . $lead_status));
    }
    else {
        array_push($response, array("status" => "FAILED", "message" => "Mise à jour échoué pour l'enregistrement avec l'ID : " . $id));
    }



    // Appeler la fonction de mise à jour des catégories des revendeurs et récupérer la réponse
    $response = json_encode(array('messages' => $response));

    // Retourner la réponse au format JSON
    return $response;
}

?>