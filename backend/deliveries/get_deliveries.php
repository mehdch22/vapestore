<?php
include '../db.php'; // Connexion à la base de données

// Récupérer toutes les livraisons
$sql = "SELECT deliveries.id, orders.id AS commande_id, users.nom AS utilisateur, deliveries.adresse_livraison, deliveries.statut, deliveries.date_creation, deliveries.date_livraison 
        FROM deliveries
        JOIN orders ON deliveries.commande_id = orders.id
        JOIN users ON deliveries.utilisateur_id = users.id";
$result = $conn->query($sql);

$deliveries = [];
while ($row = $result->fetch_assoc()) {
    $deliveries[] = $row;
}

echo json_encode($deliveries);

$conn->close();
?>
