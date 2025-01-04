<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$issue_id = $data['id'];
$new_status = $data['statut'];

// Valider le statut
$valid_statuses = ['ouvert', 'en_cours', 'résolu'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(["message" => "Statut invalide."]);
    exit;
}

// Mettre à jour le statut du problème
$query = $conn->prepare("UPDATE technical_issues SET statut = ? WHERE id = ?");
$query->bind_param("si", $new_status, $issue_id);

if ($query->execute()) {
    echo json_encode(["message" => "Statut mis à jour avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour du statut.", "error" => $conn->error]);
}

$conn->close();
?>
