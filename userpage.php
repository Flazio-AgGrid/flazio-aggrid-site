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
        <div class="table_all_user">
            <?php
            require_once './backend/db.php';
            // Appeler la fonction pour obtenir les informations de l'utilisateur
            $result = \db\get_userpage();

            if ($result->num_rows > 0) {
                // Affichage du tableau
                echo "<table>
            <tr>
                <th>Nom</th>
                <th>Date de dernière connexion</th>
                <th>En ligne/Hors ligne</th>
                <th>Profil actif/Non actif</th>
                <th>ID</th>
            </tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                <td>" . $row["username"] . "</td>
                <td>" . $row["last_connection"] . "</td>
                <td>" . $row["online"] . "</td>
                <td>" . $row["active_profile"] . "</td>
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

        <div class="profil_user">
            <p>dzezf</p>
        </div>

    </div>

</body>

</html>