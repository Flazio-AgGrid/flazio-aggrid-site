<!DOCTYPE html>
<html>

<head>
  <title>Registration</title>
</head>

<body>
  <h2>Registration</h2>
  <form method="POST" action="registration.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Register">
    <a href="login.php">Login</a>
  </form>

  <?php
  require_once '../backend/db.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire d'inscription
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Chiffrer le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Appeler la fonction pour enregistrer l'utilisateur dans la base de données
    if (db\set_register($username, $hashedPassword)) {
      echo "Inscription réussie !";
      header('Location: login.php');
      exit; // Terminer l'exécution du script après la redirection
    }
    else {
      echo "Erreur lors de l'inscription !";
    }
  }
  ?>

</body>

</html>