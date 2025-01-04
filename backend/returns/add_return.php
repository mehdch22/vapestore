<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$commande_id = $data['commande_id'];
$produit_id = $data['produit_id'];
$utilisateur_id = $data['utilisateur_id'];
$raison = $data['raison'];

// Préparer et exécuter la requête pour insérer un retour
$query = $conn->prepare("INSERT INTO returns (commande_id, produit_id, utilisateur_id, raison) VALUES (?, ?, ?, ?)");
$query->bind_param("iiis", $commande_id, $produit_id, $utilisateur_id, $raison);

if ($query->execute()) {
    echo json_encode(["message" => "Retour ajouté avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de l'ajout du retour."]);
}

$query->close();
$conn->close();
?>
