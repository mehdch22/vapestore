<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$delivery_id = $data['id'];
$new_status = $data['statut'];
$date_livraison = null;

// Si le statut est "livrée", enregistrer la date de livraison
if ($new_status === 'livrée') {
    $date_livraison = date('Y-m-d H:i:s');
}

// Mettre à jour le statut et éventuellement la date de livraison
$query = $conn->prepare("UPDATE deliveries SET statut = ?, date_livraison = ? WHERE id = ?");
$query->bind_param("ssi", $new_status, $date_livraison, $delivery_id);

if ($query->execute()) {
    echo json_encode(["message" => "Statut de livraison mis à jour avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour du statut.", "error" => $conn->error]);
}

$conn->close();
?>
