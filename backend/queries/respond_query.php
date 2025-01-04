<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$query_id = $data['id'];
$reponse = $data['reponse'];
$statut = 'répondue'; // Mettre le statut à "répondue"

// Mettre à jour la réponse et le statut dans la base de données
$query = $conn->prepare("UPDATE queries SET reponse = ?, statut = ? WHERE id = ?");
$query->bind_param("ssi", $reponse, $statut, $query_id);

if ($query->execute()) {
    echo json_encode(["message" => "Réponse ajoutée avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour de la requête.", "error" => $conn->error]);
}

$conn->close();
?>
