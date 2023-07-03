<?php

session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Rediriger vers une page d'erreur ou afficher un message d'erreur
    header('Location: ./auth/error.php');
    exit;
}

require "./backend/grid.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo grid\get_reseller_category();
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo grid\update_reseller_category();
}
?>