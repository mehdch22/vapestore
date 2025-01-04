<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer l'ID du produit à partir de la requête
$produit_id = $_GET['produit_id'];

$query = $conn->prepare("SELECT r.id, r.note, r.commentaire, r.date_creation, u.nom AS utilisateur_nom 
                         FROM reviews r
                         JOIN users u ON r.utilisateur_id = u.id
                         WHERE r.produit_id = ?");
$query->bind_param("i", $produit_id);
$query->execute();
$result = $query->get_result();

$avis = [];
while ($row = $result->fetch_assoc()) {
    $avis[] = $row;
}

echo json_encode($avis);

$conn->close();
?>
