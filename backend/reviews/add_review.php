<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$utilisateur_id = $data['utilisateur_id'];
$produit_id = $data['produit_id'];
$note = $data['note'];
$commentaire = $data['commentaire'];

// Validation des champs
if ($note < 1 || $note > 5) {
    echo json_encode(["message" => "La note doit être entre 1 et 5."]);
    exit;
}

// Insérer l'avis dans la base de données
$query = $conn->prepare("INSERT INTO reviews (utilisateur_id, produit_id, note, commentaire) VALUES (?, ?, ?, ?)");
$query->bind_param("iiis", $utilisateur_id, $produit_id,
$note, $commentaire);
if ($query->execute()) {
    echo json_encode(["message" => "Avis ajouté avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de l'ajout de l'avis."]);
}

$conn->close();
?>