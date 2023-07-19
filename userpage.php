<!DOCTYPE html>
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

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Flazio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@30.0.2/dist/ag-grid-enterprise.min.js"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <?php
    // date_default_timezone_set("Europe/Paris");
    echo date('Y-m-d H:i:s');
    ?>

    <div id="page">
        <div class="box_table_all_user">
            <div class="header_box_user">
                <h1 id="title_box">User Management Menu</h1>
                <button class="input" id="button_box_user" onclick="window.location.reload()">
                    <span class="material-symbols-outlined">sync</span>
                </button>
            </div>

            <div class="table_all_user">
                <?php
                require_once './backend/auth.php';

                // Appeler la fonction pour obtenir les informations de l'utilisateur
                $result = \auth\get_alluserinfo();

                if ($result) {
                    echo "<table>
                        <tr>
                            <th><h2>Name</h2></th>
                            <th><h2>Last Connection</h2></th>
                            <th><h2>ID</h2></th>
                            <th><h2>Option</h2></th>
                        </tr>";

                    foreach ($result as $row) {
                        $color_status = ""; // Variable pour stocker la couleur de fond
                        $color = ""; // Variable pour stocker la couleur de l'élément dot
                        $font_color = ""; // Variable pour stocker la couleur de la police
                
                        if ($row['status']['idStatus'] == 3) {
                            $color_status = "#00000035";
                            $font_color = "#00000045";
                        } else {
                            switch ($row['status']['idStatus']) {
                                case 0:
                                    $color = "#27c500";
                                    break;
                                case 1:
                                    $color = "#ff0000";
                                    break;
                                case 2:
                                    $color = "#ffaa00";
                                    break;
                                case 3:
                                    $color = "#000000";
                                    break;
                            }
                        }

                        $lastConnection = $row["lastConnection"];
                        $timestamp = strtotime($lastConnection);
                        $now = time();

                        $diff = $now - $timestamp;
                        $minutes = floor($diff / 60);
                        $hours = floor($diff / 3600);
                        $days = floor($diff / 86400);

                        $timePassed = "";
                        if ($days > 0) {
                            $timePassed .= $days . " day(s) ";
                        }
                        if ($hours > 0) {
                            $timePassed .= ($hours % 24) . " hour(s) ";
                        }
                        if ($minutes > 0) {
                            $timePassed .= ($minutes % 60) . " minute(s) ";
                        } else {
                            $timePassed .= "<1 min";
                        }

                        echo "<tr class='tr_status' style='background-color: $color_status; color: $font_color;'>
                    <td>" . $row["username"] .
                            "<span class='dot' style='background-color: $color;'></span>
                    </td><td>" . $timePassed . "</td><td>" . $row["userId"] . "</td><td>
                    <div class='dropdown'>
                        <div class='ellipsis'>&#8942;</div>
                            <div class='dropdown-content'>
                            <a class='dropdown_one status_button' href='#' data-id='" . $row["userId"] . "' data-idStatus='" . $row['status']['idStatus'] . "'>Enabled/Disabled</a>
                            <a class='log_button' href='#' data-id='" . $row["userId"] . "'>Log</a>
                                <a class='edit_button' href='#'>Edit</a>
                                <a class='dropdown_three delete_button' href='#' data-id='" . $row["userId"] . " data-idStatus='" . $row['status']['idStatus'] . "'><span class='text_delete'>Delete</span></a> 
                            </div>
                        </div>
                    </div>
                    </td>
                </tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<span class='no_data_error'>Aucun utilisateur trouvé dans la base de données.</p>";
                }

                if (isset($_POST['button_submit'])) {
                    echo "<meta http-equiv='refresh' content='1'>";
                }
                ?>
            </div>
        </div>

        <div class="box_user_management">
            <h1 class="title_box">Add User</h1>
            <div class="box_user_management_content">
                <form method="POST" action="userpage.php">
                    <input placeholder="Username" class="input" id="username" name="username" type="text" required>
                    <!-- Future feature -->
                    <!-- <select placeholder="Role" class="input" id="role" name="role" required>
                        <option value="0">Read-Only</option>  
                        <option value="1">Read-Write</option>  
                        <option value="2">Admin</option> 
                    </select> -->
                    <input placeholder="Password" class="input" id="password" name="password" type="password" required>
                    <input id="button_submit" class="button_white" type="submit" name="button_submit" value="Register">
                </form>
                <input id="homeButton" class="button_white" type="button" name="homeButton" value="Return to home">
                <div class="space"></div>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Récupérer les données du formulaire d'inscription
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    auth\registerUser($username, $password) ? " <br>Successful registration" : "<br>Registration failed";
                }
                ?>
            </div>
        </div>
    </div>


    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h1 class="title_box" id="title_box_modal">Edit User Data</h1> <br>

            <?php
            echo "<form>";
            echo "<span class='modal_span'><i>Username: " . $row["username"] . '</i>' . "<input placeholder='Username' class='input' id='username' name='username' type='text' required=''></span> <br> ";

            //Future feature
            // echo "<span class='modal_span'><i>Role: " . $row["username"] . '</i>' . "<input placeholder='Role' class='input' id='role' name='role' type='text' required=''></span> <br> ";
            
            echo "<span class='modal_span'><i>New Password: </i>" . "<input placeholder='Password' class='input' id='password' name='password' type='text' required=''></span> <br>";

            echo "<div class='submit_udapte_user'>";
            echo "<input id='button_submit' class='button_white' type='submit' name='button_submit' value='Register'>";
            echo "</div></form>";
            ?>

        </div>
    </div>

    <script src="history.js" type="module"></script>
    <script type="module">
        import HistoryResellers from "./history";
        const statusButtons = document.querySelectorAll('.status_button');
        const logButtons = document.querySelectorAll('.log_button');
        const editButtons = document.querySelectorAll('.edit_button');
        const deleteButtons = document.querySelectorAll('.delete_button');

        // DELETE USER 
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Pour éviter de suivre le lien

                const userId = button.getAttribute('data-id'); // Récupérer l'ID de l'utilisateur
                fetch('backend.php?page=deleteUser&userId=' + userId);
                location.reload();
            });
        });

        // UPDATE USERS STATUS 
        statusButtons.forEach(button => {

            button.addEventListener('click', function (event) {
                //event.preventDefault();
                const userId = button.getAttribute('data-id'); // Récupérer l'ID de l'utilisateur
                const idStatus = button.getAttribute('data-idStatus');

                function setStatus(status) {
                    button.setAttribute('data-idStatus', status);
                    fetch('backend.php?page=updateStatus&userId=' + userId + '&idStatus=' + status);
                    location.reload();
                }

                switch (idStatus) {
                    case '3':
                        setStatus('2');
                        break;
                    default:
                        setStatus('3');
                        break;
                }
            });
        });

        logButtons.forEach(button => {
            const userId = button.getAttribute('data-id');
            console.log(userId)
            button.addEventListener('click', function (event) {
                fetch(`backend.php?page=logs&id=${userId}&modeUser=true`)
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.logs.length > 0) {
                            const history = new HistoryResellers(data.logs); // Assuming the HistoryResellers class is defined elsewhere
                            history.createGrid();
                        } else {
                            createNotification("No history");
                        }
                    });
            });
        });

        /**
         * Crée une notification et l'ajoute à l'interface utilisateur.
         * @param {string} text - Texte de la notification.
         */
        function createNotification(text) {
            var notificationsContainer = document.getElementById("notifications-container");
            if (!notificationsContainer) {
                notificationsContainer = document.createElement("div");
                notificationsContainer.id = "notifications-container";
                notificationsContainer.style.display = "flex";
                notificationsContainer.style.flexDirection = "column";
                notificationsContainer.style.position = "fixed";
                notificationsContainer.style.bottom = "60px";
                notificationsContainer.style.right = "20px";
                document.body.appendChild(notificationsContainer);
            }
            const notification = document.createElement("div");
            notification.textContent = text;

            notification.style.padding = "10px";
            notification.style.backgroundColor = "#007bff";
            notification.style.paddingInline = "2vh";
            notification.style.color = "#fff";
            notification.style.borderRadius = "5px";
            notification.style.boxShadow = "0 2px 5px rgba(0, 0, 0, 0.3)";
            notification.style.cursor = "pointer";
            notification.style.opacity = "0";
            notification.style.transition = "opacity 0.4s ease";
            notification.style.marginBottom = "5px";

            notificationsContainer.insertBefore(notification, notificationsContainer.firstChild);

            setTimeout(function () {
                notification.style.opacity = "1";
            }, 100);

            setTimeout(function () {
                notification.style.opacity = "0";
                setTimeout(function () {
                    notification.parentNode.removeChild(notification);
                }, 300);
            }, 3000);
        }


        // UPDATE USERS DATA 
        editButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                openModal();


                // const userId = button.getAttribute('data-id'); // Récupérer l'ID de l'utilisateur
                // fetch('backend.php?page=modifiedStatus&userId=' + userId);
                // location.reload()
            });
        });



        // Fonction pour ouvrir la fenêtre modale
        function openModal() {
            var modal = document.getElementById('myModal');
            modal.style.display = 'block';
        }

        // Fonction pour fermer la fenêtre modale
        function closeModal() {
            var modal = document.getElementById('myModal');
            modal.style.display = 'none';
        }

        // Fermer la fenêtre modale lorsque l'utilisateur clique en dehors du contenu
        window.onclick = function (event) {
            var modal = document.getElementById('myModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };


    </script>

    <script>
        const homeButton = document.getElementById("homeButton");
        homeButton.addEventListener("click", function () {
            document.location.pathname = "/";
        });
    </script>

</body>

</html>