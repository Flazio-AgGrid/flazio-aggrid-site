<?php
namespace grid;

require 'db.php';

/**
 * Récupère les données des revendeurs et des catégories depuis la base de données.
 *
 * @return string Les données des revendeurs et des catégories au format JSON.
 */
function get_reseller_category()
{
    $error = ""; // Variable inutilisée pour l'instant

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
        "category" => $cat,
        "error"    => $error // Variable inutilisée pour l'instant
    );

    // Convertir le tableau en JSON
    $jsonData = json_encode($finalData);

    // Retourner les données au format JSON
    return $jsonData;
}

/**
 * Met à jour la catégorie d'un revendeur.
 *
 * @return void
 */
function update_reseller_category()
{
    // Tableau de réponse
    $response = array();

    // Récupérer les données modifiées depuis la requête POST
    $jsonData     = file_get_contents('php://input');
    $modifiedData = json_decode($jsonData, true);

    // Appeler la fonction de mise à jour des catégories des revendeurs et récupérer la réponse
    $response = \db\update_reseller_category($modifiedData);

    // Retourner la réponse au format JSON
    return $response;
}
?>