<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$utilisateur_id = $data['utilisateur_id'];
$description = $data['description'];
$statut = 'ouvert'; // Par défaut, le statut est "ouvert"

// Insérer le problème technique dans la base de données
$query = $conn->prepare("INSERT INTO technical_issues (utilisateur_id, description, statut) VALUES (?, ?, ?)");
$query->bind_param("iss", $utilisateur_id, $description, $statut);

if ($query->execute()) {
    echo json_encode(["message" => "Problème signalé avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de la soumission du problème.", "error" => $conn->error]);
}

$conn->close();
?>
