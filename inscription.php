<!DOCTYPE html>
<html>
<head>
  <title>Inscription</title>
</head>
<body>
  <h2>Inscription</h2>
  <form method="POST" action="inscription.php">
    <label for="username">Nom d'utilisateur:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Mot de passe:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="S'inscrire">
  </form>

  <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Récupérer les données du formulaire d'inscription
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Chiffrer le mot de passe
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  // Connexion à la base de données
  $servername = "localhost";
  $db_username = "root";
  $db_password = "";
  $db_name = "reseller_experience";

  $conn = new mysqli($servername, $db_username, $db_password, $db_name);

  // Vérifier la connexion à la base de données
  if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
  }

  // Requête d'insertion pour enregistrer l'utilisateur dans la base de données
  $query = "INSERT INTO utilisateurs (username, password) VALUES ('$username', '$hashedPassword')";

  if ($conn->query($query) === TRUE) {
    echo "Inscription réussie !";
  } else {
    echo "Erreur lors de l'inscription : " . $conn->error;
  }

  // Fermer la connexion à la base de données
  $conn->close();
}
?>

</body>
</html>
