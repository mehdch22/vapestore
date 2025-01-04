<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['order_id'], $data['statut'])) {
    echo json_encode(["message" => "Les champs order_id et statut sont obligatoires."]);
    exit;
}

$order_id = $data['order_id'];
$statut = $data['statut'];

// Vérifier si le statut est valide
$statuts_valides = ['en_attente', 'terminée', 'annulée'];
if (!in_array($statut, $statuts_valides)) {
    echo json_encode(["message" => "Le statut fourni n'est pas valide. Statuts valides : en_attente, terminée, annulée."]);
    exit;
}

// Mettre à jour le statut de la commande
$query = $conn->prepare("UPDATE orders SET statut = ? WHERE id = ?");
$query->bind_param("si", $statut, $order_id);

if ($query->execute()) {
    if ($query->affected_rows > 0) {
        echo json_encode(["message" => "Statut de la commande mis à jour avec succès."]);
    } else {
        echo json_encode(["message" => "Aucune commande trouvée avec cet ID."]);
    }
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour du statut.", "error" => $conn->error]);
}

$conn->close();
?>
