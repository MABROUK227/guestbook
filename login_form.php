<?php
// Inclure le fichier de configuration de la base de données
@include 'config.php';

// Démarrer la session
session_start();

// Traitement du formulaire lors de la soumission
if (isset($_POST['submit'])) {
    // Récupérer les données du formulaire et les sécuriser
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Mot de passe fourni par l'utilisateur

    // Requête pour sélectionner l'utilisateur en fonction de l'email
    $select = "SELECT * FROM user_form WHERE email = '$email'";

    // Exécuter la requête
    $result = mysqli_query($conn, $select);

    // Vérification si l'utilisateur existe
    if ($result && mysqli_num_rows($result) > 0) {
        // Récupérer les informations de l'utilisateur
        $row = mysqli_fetch_array($result);

        // Vérifier si le mot de passe correspond avec password_verify()
        if (password_verify($password, $row['password'])) {
            // Enregistrer l'ID de l'utilisateur pour soumettre les messages
            $_SESSION['user_id'] = $row['id'];

            // Vérifier le type d'utilisateur et rediriger en conséquence
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                header('Location: admin_page.php');
                exit(); // Arrêter l'exécution après redirection
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                header('Location: index.php'); // Rediriger vers index.php
                exit(); // Arrêter l'exécution après redirection
            }
        } else {
            // Mot de passe incorrect
            $error[] = 'Email ou mot de passe incorrect!';
        }
    } else {
        // L'utilisateur avec cet email n'existe pas
        $error[] = 'Email ou mot de passe incorrect!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de connexion</title>
    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <form action="" method="post">
        <h3>Connectez-vous maintenant</h3>
        <?php
        // Afficher les erreurs, le cas échéant
        if (isset($error)) {
            foreach ($error as $error_msg) {
                echo '<span class="error-msg">' . $error_msg . '</span>';
            }
        }
        ?>
        <input type="email" name="email" required placeholder="Entrez votre email">
        <input type="password" name="password" required placeholder="Entrez votre mot de passe">
        <input type="submit" name="submit" value="Connexion" class="form-btn">
        <p>Vous n'avez pas de compte? <a href="register_form.php">Inscrivez-vous maintenant</a></p>
    </form>
</div>
</body>
</html>
