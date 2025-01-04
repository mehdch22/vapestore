<?php
include '../db.php'; // Connexion à la base de données

// Récupérer toutes les requêtes
$sql = "SELECT queries.id, users.nom AS utilisateur, queries.message, queries.reponse, queries.statut, queries.date_creation 
        FROM queries 
        JOIN users ON queries.utilisateur_id = users.id";
$result = $conn->query($sql);

$queries = [];
while ($row = $result->fetch_assoc()) {
    $queries[] = $row;
}

echo json_encode($queries);

$conn->close();
?>
