<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$commande_id = $data['commande_id'];
$utilisateur_id = $data['utilisateur_id'];
$adresse_livraison = $data['adresse_livraison'];

// Insérer la livraison dans la base de données
$query = $conn->prepare("INSERT INTO deliveries (commande_id, utilisateur_id, adresse_livraison) VALUES (?, ?, ?)");
$query->bind_param("iis", $commande_id, $utilisateur_id, $adresse_livraison);

if ($query->execute()) {
    echo json_encode(["message" => "Livraison ajoutée avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de l'ajout de la livraison.", "error" => $conn->error]);
}

$conn->close();
?>
