<?php
// Récupérer les données du formulaire de connexion
$username = $_POST['username'];
$password = $_POST['password'];

// Connexion à la base de données
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "reseller_experience";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Vérification des informations d'identification
$query = "SELECT * FROM utilisateurs WHERE username='$username' AND password='$password'";
$result = $conn->query($query);

if ($result->num_rows == 1) {
    // Authentification réussie
    session_start();
    $_SESSION['authenticated'] = true;
    http_response_code(200); // OK
  } else {
    // Authentification échouée
    http_response_code(401); // Unauthorized
  }
