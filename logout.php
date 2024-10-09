<?php
session_start(); // Démarrer la session
session_unset(); // Libérer toutes les variables de session
session_destroy(); // Détruire la session

header('Location: login_form.php'); // Rediriger vers la page de connexion
exit();
?>
