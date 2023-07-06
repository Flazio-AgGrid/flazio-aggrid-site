<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Flazio</title>
    <link rel="stylesheet" href="style.css">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

</head>

<body>
    <div id="page">
        <div class="box_table_all_user">
            <h1 class="title_box">User Management Menu</h1>
            <div class="table_all_user">
                <?php
                require_once './backend/db.php';
                // Appeler la fonction pour obtenir les informations de l'utilisateur
                $result = \db\get_userpage();

                if ($result->num_rows > 0) {
                    // Affichage du tableau
                    echo "<table>
                <tr>
                <th><h2>Name</h2></th>
                <th><h2>Status</h2></th>
                <th><h2>Last Connection</h2></th>
                <th><h2>ID</h2></th>
            </tr>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                <td>" . $row["username"] . "</td>
                <td>" . $row["online"] . "</td>
                <td>" . $row["last_connection"] . "</td>
                <td>" . $row["id"] . "</td>
                
            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Aucun utilisateur trouvé dans la base de données.";
                }

                // Fermeture de la connexion à la base de données
                $mysqli->close();
                ?>
            </div>
        </div>

        <div class="box_user_management">
            <h1 class="title_box">User Management Menu</h1>
            <div class="box_user_management_content">

                <input placeholder="Username" class="input" name="text" type="text">

                <input placeholder="Password" class="input" name="text" type="text">
            

            </div>

            <div class="box_user_management_content">

            <button>Save</button> 

            <button>Enabled/ <br> Disabled</button>

            </div>

        </div>

    </div>

</body>

</html>