<!DOCTYPE html>
<html>

<head>
  <title>Authentification</title>
</head>

<body>
  <h2>Authentification</h2>
  <form method="POST" action="login.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>
    <input type="submit" value="Login">
    <a href="registration.php">Registration</a>
  </form>
</body>

</html>

<?php
require_once '../backend/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Récupérer les données du formulaire de connexion
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Vérifier si la connexion à la base de données est établie
  if (isset($mysqli) && $mysqli instanceof mysqli) {
    // Appeler la fonction pour obtenir les informations de l'utilisateur
    $result = db\get_username($username);

    // Vérifier si l'utilisateur existe dans la base de données
    if ($result && $result->num_rows == 1) {
      $row                  = $result->fetch_assoc();
      $hashedPasswordFromDB = $row['password'];

      // Vérifier si le mot de passe fourni correspond au hachage stocké
      if (password_verify($password, $hashedPasswordFromDB)) {
        // Authentification réussie
        $_SESSION['authenticated'] = true;
        header('Location: ../index.php');
        exit;
      }
      else {
        // Mot de passe incorrect
        echo 'Mot de passe incorrect.';
      }
    }
    else {
      // Nom d'utilisateur incorrect
      echo 'Nom d\'utilisateur incorrect.';
    }
  }
  else {
    // Erreur de connexion à la base de données
    echo 'Erreur de connexion à la base de données.';
  }
}
?>