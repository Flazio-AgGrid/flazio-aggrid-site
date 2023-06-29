<!DOCTYPE html>
<html>

<head>
  <title>Registration</title>
</head>

<body>
  <h2>Registration</h2>
  <form method="POST" action="./registration.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Register">
    <a href="./auth.html">Login</a>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire d'inscription
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Chiffrer le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Connexion à la base de données
    $servername  = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name     = "reseller_experience";

    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    // Vérifier la connexion à la base de données
    if ($conn->connect_error) {
      die("Erreur de connexion à la base de données : " . $conn->connect_error);
    }

    // Requête d'insertion pour enregistrer l'utilisateur dans la base de données
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";

    if ($conn->query($query) === TRUE) {
      echo "Inscription réussie !";
      header('Location: ./auth.html');
    }
    else {
      echo "Erreur lors de l'inscription : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
  }
  ?>

</body>

</html>