<?php

// Inclure le fichier de configuration de la base de données
@include 'config.php';

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion à la base de données");
}

// Traitement du formulaire lors de la soumission
if (isset($_POST['submit'])) {

    // Récupérer et sécuriser les données du formulaire
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'user'; // Défaut à "user"

    // Initialiser un tableau d'erreurs
    $error = [];

    // Vérification des champs requis
    if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
        $error[] = 'Tous les champs sont requis!';
    }

    // Vérification de l'existence de l'utilisateur par e-mail
    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'L\'utilisateur existe déjà!';
    } else {
        // Vérification de la correspondance des mots de passe
        if ($password !== $cpassword) {
            $error[] = 'Les mots de passe ne correspondent pas!';
        } else {
            // Hachage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertion de l'utilisateur dans la base de données
            $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$hashed_password','$user_type')";
            if (mysqli_query($conn, $insert)) {
                header('Location: login_form.php'); // Redirection après succès
                exit(); // Arrêter l'exécution après redirection
            } else {
                $error[] = 'Erreur lors de l\'enregistrement. Veuillez réessayer.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>

    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-container">
    <form action="" method="post">
        <h3>Inscrivez-vous maintenant</h3>
        <?php
        // Afficher les erreurs, le cas échéant
        if (isset($error)) {
            foreach ($error as $error_msg) {
                echo '<span class="error-msg">' . $error_msg . '</span>';
            }
        }
        ?>
        <input type="text" name="name" required placeholder="Entrez votre nom">
        <input type="email" name="email" required placeholder="Entrez votre email">
        <input type="password" name="password" required placeholder="Entrez votre mot de passe">
        <input type="password" name="cpassword" required placeholder="Confirmez votre mot de passe">
        <select name="user_type">
            <option value="user">Utilisateur</option>
            <option value="admin">Administrateur</option>
        </select>
        <input type="submit" name="submit" value="Inscrivez-vous maintenant" class="form-btn">
        <p>Vous avez déjà un compte ? <a href="login_form.php">Connectez-vous maintenant</a></p>
    </form>
</div>


</body>
</html>
