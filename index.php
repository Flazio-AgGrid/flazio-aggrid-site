<?php
session_start();

require_once './backend/auth.php';
// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['authenticated']) && auth\checkLogin()) {
    // Rediriger vers une page d'erreur ou afficher un message d'erreur
    header('Location: ./auth/erreur.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>CRM Flazio</title>
    <meta charSet="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style media="only screen">
        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            box-sizing: border-box;
            -webkit-overflow-scrolling: touch;
        }

        html {
            position: absolute;
            top: 0;
            left: 0;
            padding: 0;
            overflow: auto;
        }

        body {
            padding: 1rem;
            overflow: auto;
        }
    </style>
</head>

<body>
    <div id="myGrid" class="ag-theme-alpine" style="height: 100%">
    </div>
    <div id="buttonArea" style="position: fixed; bottom: 10px; right: 20px;"></div>
    <script>
        var __basePath = './';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@30.0.2/dist/ag-grid-enterprise.min.js">
    </script>
    <script src="main.js" type="module">
    </script>
</body>

</html>