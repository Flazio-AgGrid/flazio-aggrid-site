<?php
session_start();

require_once './backend/auth.php';
// Vérifier si l'utilisateur est authentifié
if (isset($_SESSION['authenticated']) === false && auth\checkLogin()) {
    // Rediriger vers une page d'erreur ou afficher un message d'erreur
    header('Location: ./auth/erreur.php');
    exit;
}
?>
<?php
require_once "./backend/grid.php";

$page = $_GET['page'] ?? '';

// Définir les routes
switch ($page) {
    case 'index':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            auth\checkLogin();
            echo grid\get_reseller_category();
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            auth\checkLogin();
            echo grid\update_reseller_category();
        }
        break;

    case 'management':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            auth\checkLogin();
            echo grid\get_manually_reseller_category();
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            auth\checkLogin();
            echo grid\update_manually_reseller_category();
        }
        break;

    case 'userpage':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            echo auth\checkLogin();
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            auth\checkLogin();
            // Récupérer les données modifiées depuis la requête POST
            $jsonData     = file_get_contents('php://input');
            $modifiedData = json_decode($jsonData, true);
            $row          = $modifiedData['modifiedData'];
            $userId           = $row["id"];
            $statusId  = $row['statusId'];
            echo auth\modifiedStatus($userId, $statusId);
        }
        break;
    default:
        // Route par défaut pour les pages non définies
        include 'auth/erreur.php';
        break;
}
?>