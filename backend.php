<?php

// session_start();

require "./backend/grid.php";

$page = $_GET['page'] ?? '';

// Définir les routes
switch ($page) {
    case 'index':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            echo grid\get_reseller_category();
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo grid\update_reseller_category();
        }
        break;

    case 'management':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            echo grid\get_manually_reseller_category();
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo grid\update_manually_reseller_category();
        }
        break;

    default:
        // Route par défaut pour les pages non définies
        include 'auth/erreur.php';
        break;
}
?>