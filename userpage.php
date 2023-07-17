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

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">




</head>

<body>
    <?php
    // date_default_timezone_set("Europe/Paris");
    echo  date('Y-m-d H:i:s'); ?>
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
                    // Affichage du tableau
                    echo "<table>
                    <tr>
                    <th><h2>Name</h2></th>
                    <th><h2>Last Connection</h2></th>
                    <th><h2>ID</h2></th>
                    <th><h2>Option</h2></th>
                    </tr>";

                    foreach ($result as $row) {
                        $color_status = ""; // Variable pour stocker la couleur de fond
                        $color        = ""; // Variable pour stocker la couleur de l'élément dot
                        $font_color   = ""; // Variable pour stocker la couleur de la police
                
                        if ($row['status']['idStatus'] == 3) {
                            $color_status = "#00000035";
                            $font_color   = "#00000045";
                        }
                        else {
                            switch ($row['status']['idStatus']) {
                                case 0:
                                    $color = "#27c500"; // Couleur pour le cas "connected"
                                    break;
                                case 1:
                                    $color = "#ff0000"; // Couleur pour le cas "offline"
                                    break;
                            }
                        }

                        $lastConnection = $row["lastConnection"];

                        // Convertir la date en timestamp
                        $timestamp = strtotime($lastConnection);

                        $now = time();


                        // Calculer la différence en secondes
                        $diff = $now - $timestamp;

                        // Calculer le nombre de minutes, hours  ou days passés
                        $minutes = floor($diff / 60);
                        $hours   = floor($diff / 3600);
                        $days    = floor($diff / 86400);

                        // Construire le résultat dans le format souhaité
                        $timePassed = "";
                        if ($days > 0) {
                            $timePassed .= $days . " day(s) ";
                        }
                        if ($hours > 0) {
                            $timePassed .= ($hours % 24) . " hour(s) ";
                        }
                        if ($minutes > 0) {
                            $timePassed .= ($minutes % 60) . " minute(s) ";
                        }
                        else {
                            $timePassed .= "<1 min";
                        }

                        echo "<tr class=\"tr_status\" style=\"background-color: $color_status; color: $font_color;\">
                    <td>" . $row["username"] . " <span class=\"dot\" style=\"background-color: $color;\"></span></td>
                    <td>" . $timePassed . "</td>
                    <td>" . $row["userId"] . "</td>
                    <td>
                        <div class='ellipsis' onclick='openModal(this)'>&#8942;</div>
                        <div class='modal'>
                            <div class='modal-content'>
                                <span class='close' onclick='closeModal(this)'>&times;</span>
                                <p>Select an option:</p>
                                <select class='optionSelect'>
                                    <option value=''></option>
                                    <option value='enabled/disabled'>Enabled/Disabled</option>
                                    <option value='edit'>Edit</option>
                                    <option value='delete'>Delete</option>
                                </select>
                                <button class='white_button' onclick='handleOption(this)'>Submit</button>
                            </div>
                        </div>
                    </td>
                </tr>";


                        ?>
                        <div id="optionsMenu" style="display: none;">
                            <select id="optionsSelect">
                                <option value="activate">Activer</option>
                                <option value="deactivate">Désactiver</option>
                                <option value="edit">Modifier</option>
                                <option value="delete">Supprimer</option>
                            </select>
                        </div>

                        <?php

                    }
                    echo "</table>";
                }
                else {
                    echo "<p class='no_data_error'>Aucun utilisateur trouvé dans la base de données.</p>";
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
                    <input placeholder="Password" class="input" id="password" name="password" type="password" required>
                    <input id="button_submit" class="button_white" type="submit" name="button_submit" value="Register">
                </form>
                <input id="homeButton" class="button_white" type="button" name="homeButton" value="Return to home">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Récupérer les données du formulaire d'inscription
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    echo auth\registerUser($username, $password) ? "Successfull registration" : "Registration failed";
                }
                ?>

            </div>
        </div>
    </div>

    <script>
        function openModal(element) {
            var modal = element.nextElementSibling;
            modal.style.display = "block";
        }

        function closeModal(element) {
            var modal = element.parentNode.parentNode;
            modal.style.display = "none";
        }

        function handleOption(element) {
            var modal = element.parentNode;
            var selectedOption = modal.querySelector(".optionSelect").value;

            // Gérer l'action en fonction de l'option sélectionnée
            if (selectedOption === "enabled/disabled") {
                // Traitement pour l'option Enabled/Disabled
                console.log("Option Enabled/Disabled selected");
            } else if (selectedOption === "edit") {
                // Traitement pour l'option Edit
                console.log("Option Edit selected");
            } else if (selectedOption === "delete") {
                // Traitement pour l'option Delete
                console.log("Option Delete selected");
            }

            // Fermer la fenêtre modale après traitement
            closeModal(element);
        }

        function setStatus(userId, statusId) {
            const data_tmp = { id: userId, statusId: statusId }
            fetch("backend.php?page=userpage", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ modifiedData: data_tmp }),
            })
        }
        const optionsSelect = document.getElementById('optionsSelect');
        const options = optionsSelect.options;
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            console.log(option.value);
        }
    </script>
    <script>
        const homeButton = document.getElementById("homeButton");
        homeButton.addEventListener("click", function () {
            document.location.pathname = "/";
        });
    </script>


</body>

</html>