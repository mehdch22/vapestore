<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer tous les produits
$query = "SELECT p.id, p.nom, p.description, p.prix, p.stock, c.nom AS categorie, p.date_creation
          FROM products p
          LEFT JOIN categories c ON p.categorie_id = c.id";
$result = $conn->query($query);

$produits = [];

// Parcourir les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
    }
}

// Retourner les produits sous forme de JSON
echo json_encode($produits);

$conn->close();
?>
