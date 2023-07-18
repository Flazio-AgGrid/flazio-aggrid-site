<?php
namespace log;

require_once 'db.php';

/**
 * Récupère tous les logs depuis la base de données.
 *
 * @return mixed Les logs au format tableau associatif si des logs sont trouvés, sinon false.
 */
function get_log_all()
{
    $result_data = \db\get_log_all();

    $data = array();

    while ($row = $result_data->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $data[] = $row;
    }

    $finalData = array(
        "logs" => $data
    );

    // Convertir le tableau en JSON
    $jsonData = json_encode($finalData);

    // Retourner les données au format JSON
    return $jsonData;
}

/**
 * Récupère un log spécifique en fonction de son ID depuis la base de données.
 *
 * @param int $id L'ID du log à récupérer.
 * @return mixed Le log au format tableau associatif si trouvé, sinon false.
 */
function get_log_by_id($id)
{
    $result_data = \db\get_log_by_id($id);
    $data        = array();

    while ($row = $result_data->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $data[] = $row;
    }

    $finalData = array(
        "logs" => $data
    );

    // Convertir le tableau en JSON
    $jsonData = json_encode($finalData);

    // Retourner les données au format JSON
    return $jsonData;
}

/**
 * Enregistre un log dans la base de données.
 *
 * @param string $status Le statut du log.
 * @param int $initiator L'initiateur du log.
 * @param int $objectToLog L'objet à enregistrer dans le log.
 * @return bool True si le log est enregistré avec succès, sinon false.
 */
function set_log($status, $initiator, $objectToLog, $newData)
{
    if (\db\set_log($status, $initiator, $objectToLog, $newData)) {
        return true;
    }
    else {
        return false;
    }
}
?>