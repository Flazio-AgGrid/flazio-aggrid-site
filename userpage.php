<!DOCTYPE html>
<?php
session_start();

// Import the authentication functions
require_once './backend/auth.php';

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) && auth\checkLogin()) {
    // Redirect to an error page or display an error message
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

    <!-- Prevent caching of the page -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>


    <div id="page">
        <!-- User Management Section -->
        <div class="box_table_all_user">
            <div class="header_box_user">
                <h1 id="title_box">User Management Menu</h1>
                <!-- Button to refresh the page -->
                <button class="input" id="button_box_user" onclick="window.location.reload()">
                    <span class="material-symbols-outlined">sync</span>
                </button>
            </div>

            <div class="table_all_user">
                <?php
                // Import the authentication functions
                require_once './backend/auth.php';

                // Call the function to get user information
                $result = \auth\get_alluserinfo();
                if ($result) {
                    // Display the user information in a table
                    ?>
                    <table>
                        <tr>
                            <th>
                                <h2>Name</h2>
                            </th>
                            <th>
                                <h2>Last Connection</h2>
                            </th>
                            <th>
                                <h2>Role</h2>
                            </th>
                            <th>
                                <h2>ID</h2>
                            </th>
                            <th>
                                <h2>Option</h2>
                            </th>
                        </tr>
                        <?php
                        foreach ($result as $row) {
                            // Determine the background color, dot color, and font color based on user status
                            $color_status = ""; // Background color
                            $color = ""; // Dot color
                            $font_color = ""; // Font color
                    
                            if ($row['status']['idStatus'] == 3) {
                                $color_status = "#00000035";
                                $font_color = "#00000045";
                            } else {
                                switch ($row['status']['idStatus']) {
                                    case 0:
                                        $color = "#27c500"; // Green
                                        break;
                                    case 1:
                                        $color = "#ff0000"; // Red
                                        break;
                                    case 2:
                                        $color = "#ffaa00"; // Orange
                                        break;
                                    case 3:
                                        $color = "#000000"; // Black
                                        break;
                                }
                            }

                            // Calculate the time passed since the last connection
                            $lastConnection = $row["lastConnection"];
                            if ($lastConnection) {
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
                            } else {
                                $timePassed = "Never connected";
                            }
                            ?>
                            <!-- // Display each user's information row in the table -->
                            <tr class='tr_status'
                                style='background-color:<?php echo $color_status ?>; color:<?php echo $font_color ?>'>
                                <td>
                                    <?php echo $row["username"] ?>
                                    <span class='dot' style='background-color: <?php echo $color; ?>'>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $timePassed ?>
                                </td>
                                <td>
                                    <?php echo $row['role']['titleRole'] ?>
                                </td>
                                <td>
                                    <?php echo $row["userId"] ?>
                                </td>
                                <td>
                                    <div class='dropdown'>
                                        <div class='ellipsis'>&#8942;</div>
                                        <div class='dropdown-content'>
                                            <!-- Options for each user -->
                                            <a class='dropdown_one status_button' href='#'
                                                data-id='<?php echo $row["userId"] ?>'
                                                data-idStatus='<?php echo $row['status']['idStatus'] ?>'>Enabled/Disabled</a>
                                            <a class='log_button' href='#' data-id='<?php echo $row["userId"] ?>'>Log</a>
                                            <a class='edit_button' href='#' data-id='<?php echo $row["userId"] ?>'>Edit</a>
                                            <a class='dropdown_three delete_button' href='#'
                                                data-id='<?php echo $row["userId"] ?>'
                                                data-idStatus=' <?php echo $row['status']['idStatus'] ?>'>
                                                <span class='text_delete'>Delete</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <?php
                } else {
                    echo "<span class='no_data_error'>Aucun utilisateur trouvé dans la base de données.</span>";
                }

                if (isset($_POST['button_submit'])) {
                    echo "<meta http-equiv='refresh' content='1'>";
                }
                ?>
            </div>
        </div>

        <!-- Add User Section -->
        <div class="box_user_management">
            <h1 class="title_box">Add User</h1>
            <div class="box_user_management_content">
                <form method="POST" action="userpage.php">
                    <input placeholder="Username" class="input" name="username" type="text" autocomplete="username"
                        required>
                    <!-- Future feature: Role selection -->
                    <select placeholder="Role" class="input" name="role" required>
                        <option value="1">Read-Only</option>
                        <option value="2">Read-Write</option>
                        <option value="3">Admin</option>
                    </select>
                    <input placeholder="Password" class="input" name="password" type="password"
                        autocomplete="current-password" required>
                    <input class="button_white" type="submit" name="button_submit" value="Register">
                </form>
                <input id="homeButton" class="button_white" type="button" name="homeButton" value="Return to home">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Get the data from the registration form
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $role = $_POST['role'];

                    // Register the user and display a success or failure message
                    echo auth\registerUser($username, $password, $role) ? "<br>Successful registration" : "<br>Registration failed";
                }
                ?>
                <div class="space"></div>
            </div>
        </div>
    </div>

    <!-- Modal Window for Editing User Data -->
    <?php foreach ($result as $row) { ?>
        <div id="<?php echo $row["userId"] ?>" class="modal">
            <div class="modal-content">
                <span class="close" id="close-<?php echo $row["userId"] ?>">&times;</span>
                <h1 class="title_box" id="title_box_modal">Edit User Data</h1>
                <br>

                <!-- Editing user data -->
                <form class="editForm">
                    <input type="hidden" name="userId" value="<?php echo $row["userId"] ?>">
                    <span class='modal_span'>
                        <i>Username: <?php echo $row["username"] ?></i>
                        <input placeholder='Username' class='input' name='username' type='text'>
                    </span>
                    <br>

                    <!-- Future feature: Role selection -->
                    <span class='modal_span'>
                        <i>Role: <?php echo $row['role']['titleRole'] ?></i>
                        <!-- 1 = read ; 2 = read/write ; 3 = admin -->
                        <select placeholder="Role" class="input role" name="role">
                            <option value="1" <?php echo $row['role']['idRole'] === "1" ? "selected" : "" ?>>Read-Only
                            </option>
                            <option value="2" <?php echo $row['role']['idRole'] === "2" ? "selected" : "" ?>>Read/Write
                            </option>
                            <option value="3" <?php echo $row['role']['idRole'] === "3" ? "selected" : "" ?>>Admin</option>
                        </select>

                    </span>
                    <br>

                    <span class='modal_span'>
                        <i>New Password: </i>
                        <input placeholder='Password' class='input' name='password' type='text'>
                    </span>
                    <br>

                    <div class='submit_udapte_user'>
                        <input class='button_white' type='submit' name='button_submit' value='Save'>
                    </div>
                </form>

            </div>
        </div>
    <?php } ?>

    <script src="history.js" type="module"></script>
    <script type="module">
        import HistoryResellers from "./history";
        const statusButtons = document.querySelectorAll('.status_button');
        const logButtons = document.querySelectorAll('.log_button');
        const editButtons = document.querySelectorAll('.edit_button');
        const deleteButtons = document.querySelectorAll('.delete_button');
        const close = document.querySelectorAll('.close');

        // DELETE USER 
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                const userId = button.getAttribute('data-id'); // Récupérer l'ID de l'utilisateur
                fetch('backend.php?page=deleteUser&userId=' + userId)
                    .then((response) => response.json())
                    .then((res) => {
                        console.log(res)
                        createNotification(`Delete user ${res ? "success" : "failed"}`)
                    });;
                setTimeout(() => {
                    location.reload();
                }, 250)
            });
        });

        // UPDATE USERS STATUS 
        statusButtons.forEach(button => {

            button.addEventListener('click', function (event) {
                const userId = button.getAttribute('data-id'); // Récupérer l'ID de l'utilisateur
                const idStatus = button.getAttribute('data-idStatus');

                function setStatus(status) {
                    button.setAttribute('data-idStatus', status);

                    const formData = new FormData();
                    formData.append('userId', userId);
                    formData.append('idStatus', status);

                    fetch('backend.php?page=userpage&option=status', {
                        method: 'POST',
                        body: formData
                    })
                        .then((response) => response.json())
                        .then((res) => {
                            console.log(res)
                            createNotification(`Update status ${res ? "success" : "failed"}`)
                        });
                    setTimeout(() => {
                        location.reload();
                    }, 250)
                }

                switch (parseInt(idStatus)) {
                    case 3:
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

        // UPDATE USERS DATA 
        editButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openModal(event.target.getAttribute('data-id'));
            });
        });

        // Fonction pour ouvrir la fenêtre modale
        function openModal(userId) {
            var modal = document.getElementById(userId);
            modal.style.display = 'block';
        }

        // Fonction pour fermer la fenêtre modale
        function closeModal(userId) {
            var modal = document.getElementById(userId);
            modal.style.display = 'none';
        }

        close.forEach((button) => {
            button.addEventListener('click', (event) => {
                closeModal(event.target.parentNode.parentNode.id)
            })
        })

        function handleSubmit(event) {

            const form = event.target; // Get the form that was submitted
            const formData = new FormData(form); // Collect form data

            // Make a POST request to the backend endpoint
            fetch('backend.php?page=userpage&option=editAccount', {
                method: 'POST',
                body: formData
            }).then((response) => response.json())
                .then((res) => {
                    res.map((el) => {
                        createNotification(`${el.message} ${el.status ? "successfully" : "failed"} modified`)
                    })
                }).catch((err) => {
                    console.error(err)
                })
            setTimeout(() => {
                location.reload();
            }, 750)
        }
        const forms = document.querySelectorAll('.editForm');
        forms.forEach((form) => {
            form.addEventListener('submit', handleSubmit);
        })
        // CREATE NOTIFICATION

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
    </script>

    <script>
        const homeButton = document.getElementById("homeButton");
        homeButton.addEventListener("click", function () {
            document.location.pathname = "/";
        });
    </script>

</body>

</html>