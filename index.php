<?php
// Inclure le fichier de configuration de la base de données
@include 'config.php';

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion à la base de données");
}

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Obtenir les informations de l'utilisateur à partir de la base de données
    $select_user = "SELECT * FROM user_form WHERE id = '$user_id'";
    $result_user = mysqli_query($conn, $select_user);
    
    // Vérification si la requête a réussi
    if ($result_user && mysqli_num_rows($result_user) > 0) {
        $user_data = mysqli_fetch_assoc($result_user);
        $user_email = $user_data['email'];
        $user_name = $user_data['name'];
    } else {
        // Gérer l'erreur si l'utilisateur n'est pas trouvé
        echo "Utilisateur introuvable.";
        exit();
    }
} else {
    // Rediriger vers le formulaire de connexion si l'utilisateur n'est pas connecté
    header('Location: login_form.php');
    exit();
}

// Traitement des messages
if (isset($_POST['submit'])) {
    $msg = mysqli_real_escape_string($conn, $_POST['msg']);
    
    if (!empty($msg)) {
        $insert_msg = "INSERT INTO comments (user_id, user_email, message) VALUES ('$user_id', '$user_email', '$msg')";
        if (!mysqli_query($conn, $insert_msg)) {
            echo "Erreur lors de l'ajout du message: " . mysqli_error($conn);
        }
    } else {
        echo "Le message ne peut pas être vide.";
    }
}

// Modifier un message
if (isset($_POST['edit'])) {
    $msg_id = $_POST['msg_id'];
    $new_msg = mysqli_real_escape_string($conn, $_POST['new_msg']);
    
    if (!empty($new_msg)) {
        $update_msg = "UPDATE comments SET message = '$new_msg' WHERE id = '$msg_id'";
        if (!mysqli_query($conn, $update_msg)) {
            echo "Erreur lors de la modification du message: " . mysqli_error($conn);
        }
    } else {
        echo "Le message ne peut pas être vide.";
    }
}

// Supprimer un message
if (isset($_POST['delete'])) {
    $msg_id = $_POST['msg_id'];
    $delete_msg = "DELETE FROM comments WHERE id = '$msg_id'";
    if (!mysqli_query($conn, $delete_msg)) {
        echo "Erreur lors de la suppression du message: " . mysqli_error($conn);
    }
}

// Récupérer les messages
$select_comments = "SELECT * FROM comments ORDER BY created_at DESC";
$comments_result = mysqli_query($conn, $select_comments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Book</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <h3>Bienvenue, <?php echo htmlspecialchars($user_name); ?></h3>
    
    <form action="" method="post">
        <label for="msg">Message:</label>
        <input type="text" name="msg" required placeholder="Écrivez votre message ici">
        <input type="submit" name="submit" value="Poster le message">
    </form>

    <!-- Formulaire de déconnexion -->
    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Déconnexion" class="logout-btn">
    </form>
</div>

<div class="comments-section">
    <h3>Messages des utilisateurs</h3>
    <?php
    if ($comments_result) {
        while ($comment = mysqli_fetch_assoc($comments_result)) {
            echo "<div class='comment'>";
            echo "<p><strong>" . htmlspecialchars($comment['user_email']) . ":</strong> " . htmlspecialchars($comment['message']) . "</p>";
            
            // Vérifiez si l'utilisateur est l'auteur du message avant d'afficher les boutons Modifier et Supprimer
            if ($comment['user_id'] == $user_id) { // Vérifie que l'ID de l'utilisateur connecté est le même que l'ID de l'auteur du message
                echo "<form action='' method='post' style='display: inline;'>
                        <input type='hidden' name='msg_id' value='" . $comment['id'] . "'>
                        <input type='text' name='new_msg' placeholder='Modifier votre message' required>
                        <input type='submit' name='edit' value='Modifier' class='edit-btn'>
                      </form>";
                
                echo "<form action='' method='post' style='display: inline;'>
                        <input type='hidden' name='msg_id' value='" . $comment['id'] . "'>
                        <input type='submit' name='delete' value='Supprimer' class='delete-btn' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce message?');\">
                      </form>";
            }
            echo "</div>";
        }
    } else {
        echo "Aucun message trouvé.";
    }
    ?>
</div>

</div>
</body>
</html>
