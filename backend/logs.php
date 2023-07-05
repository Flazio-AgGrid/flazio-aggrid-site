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
    $log = \db\get_log_all();
    if ($log) {
        return $log;
    }
    else {
        return false;
    }
}

/**
 * Récupère un log spécifique en fonction de son ID depuis la base de données.
 *
 * @param int $id L'ID du log à récupérer.
 * @return mixed Le log au format tableau associatif si trouvé, sinon false.
 */
function get_log_by_id($id)
{
    $log = \db\get_log_by_id($id);
    if ($log) {
        return $log;
    }
    else {
        return false;
    }
}

/**
 * Enregistre un log dans la base de données.
 *
 * @param string $status Le statut du log.
 * @param int $initiator L'initiateur du log.
 * @param int $objectToLog L'objet à enregistrer dans le log.
 * @return bool True si le log est enregistré avec succès, sinon false.
 */
function set_log($status, $initiator, $objectToLog)
{
    if (\db\set_log($status, $initiator, $objectToLog)) {
        return true;
    }
    else {
        return false;
    }
}
?>