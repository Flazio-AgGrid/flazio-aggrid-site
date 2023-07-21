<?php
namespace log;

require_once 'db.php';

/**
 * Récupère un log spécifique en fonction de son ID depuis la base de données.
 *
 * @param int $id L'ID du log à récupérer.
 * @return mixed Le log au format tableau associatif si trouvé, sinon false.
 */
function get_log_by_id($id, $modeUser)
{
    $result_data = $modeUser ? \db\get_log_by_user_id($id, NULL, NULL) : \db\get_log_by_reseller_id($id);
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
 * Enregistre un nouvel enregistrement de journal (log) avec les données fournies,
 * en utilisant le champ 'oldData' de la dernière ligne renseignée à partir de la
 * table de logs pour l'ID d'objet spécifié.
 *
 * @param string $status Le statut du journal.
 * @param int $initiator L'initiateur de l'action.
 * @param int $objectId  L'ID de l'objet concerné par le journal.
 * @param array $arrayNewData Les nouvelles données à enregistrer.
 * @return bool Renvoie true si l'enregistrement a été effectué avec succès, sinon false.
 */
function set_log($status, $initiator, $objectId, $arrayNewData)
{
    try {
        $result = \db\get_log_by_reseller_id($objectId);

        if ($result->num_rows > 0) {
            // Déplacer le pointeur du résultat à la dernière ligne
            $result->data_seek($result->num_rows - 1);

            // Récupérer la dernière ligne
            $row = $result->fetch_assoc();
            $jsonOldData = $row['newData'];
        } else {
            $jsonOldData = NULL;
        }

        \db\set_log($status, $initiator, $objectId, $jsonOldData, json_encode($arrayNewData));
        return true;

    } catch (\Throwable $th) {
        return false;
    }
}

/**
 * Ajoute une entrée dans le journal d'activité de l'utilisateur.
 *
 * @param string $status Le statut de l'action (par exemple : "login", "logout", etc.).
 * @param int $initiator L'ID de l'utilisateur initiateur de l'action.
 * @param string $column Le nom de la colonne associée à l'action (si applicable, sinon NULL).
 * @param array $arrayNewData Les nouvelles données à enregistrer dans le journal.
 * @return bool Renvoie true si l'entrée a été ajoutée avec succès, sinon false.
 */
function set_log_user($status, $initiator, $column, $arrayNewData)
{
    try {
        // Récupérer le journal d'activité de l'utilisateur
        $result = \db\get_log_by_user_id($initiator, $status, $column);

        // Vérifier s'il y a au moins une entrée dans le journal pour le statut spécifié
        if ($result->num_rows >= 1 && ($status !== 'login' || $status !== 'logout')) {
            // Déplacer le pointeur du résultat à la dernière ligne
            $result->data_seek($result->num_rows - 1);

            // Récupérer la dernière ligne
            $row = $result->fetch_assoc();
            $jsonOldData = $row['newData'];
        } else {
            // Pas d'entrée existante ou le statut est "login" ou "logout", donc pas de données précédentes
            $jsonOldData = NULL;
        }

        // Enregistrer l'entrée dans le journal avec les nouvelles données et les données précédentes
        \db\set_log($status, $initiator, null, $jsonOldData, json_encode($arrayNewData));
        return true;
    } catch (\Throwable $th) {
        return false;
    }
}

?>