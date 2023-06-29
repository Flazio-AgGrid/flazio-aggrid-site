<?php

session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Rediriger vers une page d'erreur ou afficher un message d'erreur
    header('Location: erreur.php');
    exit;
}

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
    $response = array();

    $jsonData     = file_get_contents('php://input');
    $modifiedData = json_decode($jsonData, true);
    // Traiter les résultats de la requête
    foreach ($modifiedData['modifiedData'] as $row) {
        $id     = $row["id"];
        $id_cat = $row['id_cat'];

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

            array_push($response, array("status" => "OK", "message" => "Mise a jour reussie pour l'enregistrement avec l'ID : " . $id));
        }
        else {
            $query           = "SELECT id, Indirizzo, Comune, CAP, Provincia FROM reseller_experience_customer WHERE id = $id";
            $result          = $mysqli->query($query);
            $tableau_adresse = $result->fetch_all();

            // Générer et insérer les données dans la table
            foreach ($tableau_adresse as $row) {
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

                $query = "INSERT INTO maps_info (fk_lead, jsondata, website, warning, id_cat, id_cat_automatica) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt  = $mysqli->prepare($query);
                $stmt->bind_param("isssii", $fk_lead, $jsondata, $website, $warning, $id_cat, $id_cat_automatica);
                $stmt->execute();
            }

            $stmt->close();
            array_push($response, array("status" => "OK", "message" => "Mise a jour reussie pour l'enregistrement avec l'ID : " . $id));
        }
    }
    //var_dump(array('messages' => $response));
    echo json_encode(array('messages' => $response));

}

// Fermer la connexion à la base de données
$mysqli->close();
?>