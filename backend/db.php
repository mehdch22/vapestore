<?php
// Configuration de la base de données
$servername = "localhost"; // Hôte MySQL
$username = "root"; // Nom d'utilisateur MySQL
$password = "98765@Z.f"; // Mot de passe MySQL (vide par défaut pour XAMPP ou WAMP)
$dbname = "VapeStore"; // Nom de votre base de données

// Connexion à MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
} else {
    echo "Connexion réussie à la base de données !";
}
