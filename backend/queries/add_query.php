<?php
include '../db.php'; // Connexion à la base de données

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs nécessaires sont présents
if (!isset($data['utilisateur_id'], $data['message'])) {
    echo json_encode(["status" => "error", "message" => "Les champs utilisateur_id et message sont obligatoires."]);
    exit;
}

$utilisateur_id = intval($data['utilisateur_id']);
$message = htmlspecialchars(strip_tags(trim($data['message'])));

// Vérifier si l'utilisateur existe
$query_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
$query_user->bind_param("i", $utilisateur_id);
$query_user->execute();
$result_user = $query_user->get_result();

if ($result_user->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Utilisateur introuvable."]);
    exit;
}

// Insérer la requête dans la base de données
$query = $conn->prepare("INSERT INTO queries (utilisateur_id, message) VALUES (?, ?)");
$query->bind_param("is", $utilisateur_id, $message);

if ($query->execute()) {
    echo json_encode(["status" => "success", "message" => "Requête ajoutée avec succès !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout de la requête.", "error" => $conn->error]);
}

$conn->close();
?>
