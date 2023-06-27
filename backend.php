<?php
// Établir une connexion à MySQL et exécuter une requête
$mysqli = new mysqli("localhost", "root", "", "reseller_experience");

// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
    $error += "Échec de la connexion MySQL : " . $mysqli->connect_error;
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $error = "";

    $query = "SELECT r.*, m.id_cat, m.id_cat_automatica FROM reseller_experience_customer r LEFT JOIN maps_info m ON r.id = m.fk_lead WHERE m.id_cat IS NULL LIMIT 10;";

    $result_data = $mysqli->query($query);

    // Vérifier si la requête a échoué
    if (!$result_data) {
        $error += "Erreur lors de l'exécution de la requête : " . $mysqli->error;
        exit();
    }

    $query = "SELECT id,title FROM category;";

    $result_cat = $mysqli->query($query);

    // Vérifier si la requête a échoué
    if (!$result_cat) {
        echo "Erreur lors de l'exécution de la requête : " . $mysqli->error;
        exit();
    }

    // Créer un tableau pour stocker les données
    $data = array();

    // Traiter les résultats de la requête
    while ($row = $result_data->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $data[] = $row;
    }

    // Créer un tableau pour stocker les données
    $cat = array();

    // Traiter les résultats de la requête
    while ($row = $result_cat->fetch_assoc()) {
        // Ajouter chaque ligne de résultat au tableau
        $cat[] = $row;
    }

    // Créer un tableau final contenant les données et les catégories
    $finalData = array(
        "data"     => $data,
        "category" => $cat,
        "error"    => $error
    );

    // Convertir le tableau en JSON
    $jsonData = json_encode($finalData);

    // Enregistrer le JSON dans un fichier
    echo $jsonData;
    //echo "Les données ont été transférées vers un fichier JSON avec succès.";
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonData     = file_get_contents('php://input');
    $modifiedData = json_decode($jsonData, true);

    $response = array();

    $id     = $modifiedData["modifiedData"]["id"];
    $id_cat = $modifiedData["modifiedData"]['id_cat'];

    // Check if the record exists
    $checkSql = "SELECT COUNT(*) FROM maps_info WHERE fk_lead = ?";
    $stmt     = $mysqli->prepare($checkSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Update the record if it exists
        $updateSql = "UPDATE maps_info SET id_cat = ? WHERE fk_lead = ?";
        $stmt      = $mysqli->prepare($updateSql);
        $stmt->bind_param("ii", $id_cat, $id);
        $stmt->execute();
        $stmt->close();

        $response = array("id" => $id, "message" => "Mise à jour réussie pour l'enregistrement avec l'ID : " . $id);
    }
    else {
        // Insert the record with default data if it doesn't exist
        $default_jsondata = json_encode(['formatted_address' => 'DEFAULT_STREET , 1234567, DEFAULT_DISTRICT, Italy', 'formatted_phone_number' => '123 xxx 6789', 'name' => 'Example', 'website' => 'http://www.example.it/']);
        $insertSql        = "INSERT INTO maps_info (fk_lead, id_cat, jsondata, website, warning, id_cat_automatica) VALUES (?, ?, ?, 'default_website', 'default_warning', 'default_id_cat_automatica')";
        $stmt             = $mysqli->prepare($insertSql);
        $stmt->bind_param("iis", $id, $id_cat, $default_jsondata);
        $stmt->execute();
        $stmt->close();

        $response = array("id" => $id, "message" => "Enregistrement créé avec succès pour l'ID : " . $id);
    }

    echo json_encode($response);

}

// Fermer la connexion à la base de données
$mysqli->close();
?>