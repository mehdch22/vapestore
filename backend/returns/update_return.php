<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$return_id = $data['id'];
$new_status = $data['statut'];

// Valider le statut
$valid_statuses = ['en_attente', 'accepté', 'rejeté'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(["message" => "Statut invalide."]);
    exit;
}

// Mettre à jour le statut dans la base de données
$query = $conn->prepare("UPDATE returns SET statut = ? WHERE id = ?");
$query->bind_param("si", $new_status, $return_id);

if ($query->execute()) {
    echo json_encode(["message" => "Statut mis à jour avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour du statut."]);
}

$query->close();
$conn->close();
?>
