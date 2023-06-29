<?php
session_start();

// Configuration de la connexion à la base de données
$servername  = "localhost";
$db_username = "root";
$db_password = "";
$db_name     = "reseller_experience";

// Fonction d'authentification pour la connexion
function login($username, $password)
{
    global $servername, $db_username, $db_password, $db_name;

    // Connexion à la base de données
    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    // Vérification des informations d'identification
    $query  = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row                  = $result->fetch_assoc();
        $hashedPasswordFromDB = $row['password'];

        // Vérifier si le mot de passe fourni correspond au hachage stocké
        if (password_verify($password, $hashedPasswordFromDB)) {
            // Authentification réussie
            $_SESSION['authenticated'] = true;
            http_response_code(200); // OK
        }
        else {
            // Authentification échouée
            http_response_code(401); // Unauthorized
        }
    }
    else {
        // Authentification échouée
        http_response_code(401); // Unauthorized
    }

    $conn->close();
}

// Fonction d'inscription
function register($username, $password)
{
    global $servername, $db_username, $db_password, $db_name;
    
    // Chiffrer le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Connexion à la base de données
    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    // Requête d'insertion pour enregistrer l'utilisateur dans la base de données
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";

    if ($conn->query($query) === TRUE) {
        // Inscription réussie
        http_response_code(200); // OK
    }
    else {
        // Erreur lors de l'inscription
        http_response_code(500); // Internal Server Error
    }

    $conn->close();
}

// Vérifier le type de requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier le type d'opération (login ou register)
    $operation = $_POST['operation'];

    // Effectuer l'opération appropriée
    if ($operation === 'login') {
        login($username, $password);
    }
    elseif ($operation === 'register') {
        register($username, $password);
    }
}
?>