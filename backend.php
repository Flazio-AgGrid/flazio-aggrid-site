<?php
// Établir une connexion à MySQL et exécuter une requête
$mysqli = new mysqli("localhost", "root", "", "reseller_experience");

// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
    echo "Échec de la connexion MySQL : " . $mysqli->connect_error;
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $query = "SELECT r.*, m.id_cat, m.id_cat_automatica FROM reseller_experience_customer r LEFT JOIN maps_info m ON r.id = m.fk_lead WHERE m.id_cat IS NULL LIMIT 10;";

    $result = $mysqli->query($query);

    // Vérifier si la requête a échoué
    if (!$result) {
        echo "Erreur lors de l'exécution de la requête : " . $mysqli->error;
        exit();
    }

    // Créer un tableau pour stocker les données
    $data = array();

    // Traiter les résultats de la requête
    while ($row = $result->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $data[] = $row;
    }

    // Convertir le tableau en JSON
    $jsonData = json_encode($data);

    // Enregistrer le JSON dans un fichier
// $file = 'data.json';
// file_put_contents($file, $jsonData);
    echo $jsonData;
    //echo "Les données ont été transférées vers un fichier JSON avec succès.";
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonData     = file_get_contents('php://input');
    $modifiedData = json_decode($jsonData, true);

    foreach ($modifiedData["modifiedData"] as $row) {
        $id     = $row['id'];
        $id_cat = $row['id_cat'];

        $sql = "UPDATE reseller_experience_customer r LEFT JOIN maps_info m ON r.id = m.fk_lead SET m.id_cat = $id_cat WHERE r.id = $id";

        if ($mysqli->query($sql) === TRUE) {
            $response = array("message" => "Mise a jour reussie pour l'enregistrement avec l'ID : " . $id, "sql" => $sql);
            echo json_encode($response);
        }
        else {
            $response = array("message" => "Erreur lors de la mise a jour pour l'enregistrement avec l'ID : " . $id, "erreur" => $mysqli->error);
            echo json_encode($response);
        }
    }
}

// Fermer la connexion à la base de données
$mysqli->close();
?>