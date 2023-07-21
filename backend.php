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

// fetch('backend.php?page=??&userId=?')
$page = $_GET['page'] ?? '';

// Définir les routes
switch ($page) {
    case 'index':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin())
                echo grid\get_reseller_category();
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (auth\checkLogin(1))
                echo grid\update_reseller_category();
            else
                echo false;

        }
        break;

    case 'management':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin())
                echo grid\get_manually_reseller_category();
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (auth\checkLogin(1))
                echo grid\update_manually_reseller_category();
            else
                echo false;
        }
        break;

    case 'userpage':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            echo auth\checkLogin();
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //fetch(backend.php?page=userpage&option=password)
            if (auth\checkLogin(array(1, 2))) {
                $option = $_GET['option'] ?? '';
                switch ($option) {
                    case 'editAccount':
                        $userId = $_POST["userId"] ?? "";
                        $username = $_POST["username"] ?? "";
                        $password = $_POST['password'] ?? "";
                        $role = $_POST['role'] ?? "";
                        echo auth\editAccount($userId, $username, $password, $role);
                        break;
                    case 'status':
                        $userId = $_POST["userId"];
                        $statusId = $_POST['idStatus'];
                        echo auth\modifiedStatus($userId, $statusId);
                        break;
                    default:
                        break;
                }
            } else
                echo false;
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
            if (auth\checkLogin(array(1, 2))) {
                echo log\get_log_by_id($_GET['id'], $_GET['modeUser']);
            } else
                echo false;
        }
        break;
    case 'deleteUser':
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (auth\checkLogin(array(1, 2))) {
                echo auth\deleteUser($_GET['userId']);
            } else
                echo false;
        }
        break;
    default:
        break;
}
?>