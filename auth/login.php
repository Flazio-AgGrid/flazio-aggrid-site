<?php
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] === true && $_SESSION['authenticated'] === false) {
  // Rediriger vers une page d'erreur ou afficher un message d'erreur
  header('Location: ../index.php');
  exit;
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Récupérer les données du formulaire de connexion
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Connexion à la base de données
  $servername  = "localhost";
  $db_username = "root";
  $db_password = "";
  $db_name     = "reseller_experience";

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
      session_start();
      $_SESSION['authenticated'] = true;
      http_response_code(200); // OK
      header('Location: ../index.php');
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
?>