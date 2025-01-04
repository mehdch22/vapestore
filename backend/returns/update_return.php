<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['return_id'], $data['statut'])) {
    echo json_encode(["message" => "Les champs return_id et statut sont obligatoires."]);
    exit;
}

$return_id = $data['return_id'];
$statut = $data['statut'];

// Vérifier si le statut est valide
$statuts_valides = ['en_attente', 'accepté', 'rejeté'];
if (!in_array($statut, $statuts_valides)) {
    echo json_encode(["message" => "Statut non valide. Les valeurs valides sont : en_attente, accepté, rejeté."]);
    exit;
}

// Mettre à jour le statut dans la table des retours
$query = $conn->prepare("UPDATE returns SET statut = ? WHERE id = ?");
$query->bind_param("si", $statut, $return_id);

if ($query->execute()) {
    if ($query->affected_rows > 0) {
        echo json_encode(["message" => "Statut mis à jour avec succès !"]);
    } else {
        echo json_encode(["message" => "Aucun retour trouvé avec cet ID."]);
    }
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour.", "error" => $conn->error]);
}

$conn->close();
?>
