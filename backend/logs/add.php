<?php
include '../db.php'; // Inclure la connexion à la base de données

$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs obligatoires sont présents
if (!isset($data['utilisateur_id'], $data['action'])) {
    echo json_encode(["status" => "error", "message" => "Les champs utilisateur_id et action sont obligatoires."]);
    exit;
}

$utilisateur_id = $data['utilisateur_id'];
$action = htmlspecialchars(strip_tags($data['action']));

// Ajouter l'entrée dans la table activity_logs
$query = $conn->prepare("INSERT INTO activity_logs (utilisateur_id, action) VALUES (?, ?)");
$query->bind_param("is", $utilisateur_id, $action);

if ($query->execute()) {
    echo json_encode(["status" => "success", "message" => "Journal d'activité ajouté avec succès."]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout du journal.", "error" => $conn->error]);
}

$conn->close();
?>
