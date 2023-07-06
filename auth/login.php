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
require '../backend/auth.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Récupérer les données du formulaire de connexion
  $username = $_POST['username'];
  $password = $_POST['password'];
  auth\login($username, $password);

}
?>