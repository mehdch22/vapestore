<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

$utilisateur_id = $data['utilisateur_id'];
$message = $data['message'];

// Insérer la requête dans la base de données
$query = $conn->prepare("INSERT INTO queries (utilisateur_id, message) VALUES (?, ?)");
$query->bind_param("is", $utilisateur_id, $message);

if ($query->execute()) {
    echo json_encode(["message" => "Requête ajoutée avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de l'ajout de la requête.", "error" => $conn->error]);
}

$conn->close();
?>
