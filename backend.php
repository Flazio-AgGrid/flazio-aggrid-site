<?php
// Établir une connexion à MySQL et exécuter une requête
$mysqli = new mysqli("localhost", "root", "", "reseller_experience");

// Vérifier si la connexion a échoué
if ($mysqli->connect_errno) {
    echo "Échec de la connexion MySQL : " . $mysqli->connect_error;
    exit();
}

$query = "SELECT r.*
    FROM reseller_experience_customer r
    LEFT JOIN maps_info m ON r.id = m.fk_lead
    WHERE m.id_cat IS NULL
    LIMIT 10";

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
echo $jsonData;
//echo "Les données ont été transférées vers un fichier JSON avec succès.";

// Fermer la connexion à la base de données
$mysqli->close();
?>