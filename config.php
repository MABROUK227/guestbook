<?php
// Connexion à la base de données
$conn = mysqli_connect('localhost', 'root', '', 'mbk');

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
} else {
    echo "Connexion réussie à la base de données!";
}
?>
