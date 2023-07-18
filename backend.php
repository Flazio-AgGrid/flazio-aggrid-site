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
<?php
require_once "./backend/grid.php";
require_once "./backend/logs.php";

$page = $_GET['page'] ?? '';

// Définir les routes
switch ($page) {
    case 'index':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin())
                echo grid\get_reseller_category();
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (auth\checkLogin())
                echo grid\update_reseller_category();
        }
        break;

    case 'management':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin())
                echo grid\get_manually_reseller_category();
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (auth\checkLogin())
                echo grid\update_manually_reseller_category();
        }
        break;

    case 'userpage':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            echo auth\checkLogin();
        }
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (auth\checkLogin()) {
                // Récupérer les données modifiées depuis la requête POST
                $jsonData     = file_get_contents('php://input');
                $modifiedData = json_decode($jsonData, true);
                $row          = $modifiedData['modifiedData'];
                $userId       = $row["id"];
                $statusId     = $row['statusId'];
                echo auth\modifiedStatus($userId, $statusId);
            }
        }
        break;

    case 'logout':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin()) {
                echo auth\logout();
            }
        }
        break;
    case 'logs':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin()) {
                echo log\get_log_by_id($_GET['id'], $_GET['modeUser']);
            }
        }
        break;
    default:
        break;
}
?>