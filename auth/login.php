<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM Flazio</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

</head>

<body>
  <div id="page_login">
    <div class="box_user_management">
      <h1 class="title_box">Connection</h1>
      <div class="box_user_management_content">

        <form method="POST" action="login.php">

          <input placeholder="Username" class="input" id="username" name="username" type="text" required="">
          <input placeholder="Password" class="input" id="password" name="password" type="password" required="">
          <input id="button_submit" class="button_white" type="submit" name="button_submit" value="Login">
        </form>

      </div>
    </div>

    
  </div>


  <footer>
    <p id="footer_contact">
      In case of any issues, contact the administrator <a id="a_footer_contact"
        href="mailto:example@flazio.com">here</a> .
    </p>
  </footer>
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