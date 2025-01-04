<?php
include '../db.php'; // Connexion à la base de données

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs nécessaires sont présents
if (!isset($data['id'], $data['reponse'])) {
    echo json_encode(["status" => "error", "message" => "Les champs id et reponse sont obligatoires."]);
    exit;
}

$query_id = intval($data['id']);
$reponse = htmlspecialchars(strip_tags(trim($data['reponse'])));
$statut = 'répondue'; // Mettre le statut à "répondue"

// Vérifier si la requête existe
$query_check = $conn->prepare("SELECT id FROM queries WHERE id = ?");
$query_check->bind_param("i", $query_id);
$query_check->execute();
$result_check = $query_check->get_result();

if ($result_check->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Requête introuvable avec cet ID."]);
    exit;
}

// Mettre à jour la réponse et le statut
$query = $conn->prepare("UPDATE queries SET reponse = ?, statut = ? WHERE id = ?");
$query->bind_param("ssi", $reponse, $statut, $query_id);

if ($query->execute()) {
    echo json_encode(["status" => "success", "message" => "Réponse ajoutée avec succès !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la mise à jour de la requête.", "error" => $conn->error]);
}

$conn->close();
?>
