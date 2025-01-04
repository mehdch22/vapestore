<?php
include '../db.php'; // Connexion à la base de données

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

//
// Vérifier si les champs nécessaires sont présents
if (!isset($data['id'], $data['statut'])) {
    echo json_encode(["status" => "error", "message" => "Les champs id et statut sont obligatoires."]);
    exit;
}

$delivery_id = intval($data['id']);
$new_status = htmlspecialchars(strip_tags(trim($data['statut'])));
$date_livraison = null;

// Valider le statut
$valid_statuses = ['en préparation', 'expédiée', 'livrée'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(["status" => "error", "message" => "Statut invalide. Statuts valides : en préparation, expédiée, livrée."]);
    exit;
}

// Si le statut est "livrée", enregistrer la date de livraison
if ($new_status === 'livrée') {
    $date_livraison = date('Y-m-d H:i:s');
}

// Vérifier si la livraison existe
$query_check = $conn->prepare("SELECT * FROM deliveries WHERE id = ?");
$query_check->bind_param("i", $delivery_id);
$query_check->execute();
$result_check = $query_check->get_result();

if ($result_check->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Livraison introuvable avec cet ID."]);
    exit;
}

// Mettre à jour le statut et éventuellement la date de livraison
$query_update = $conn->prepare("
    UPDATE deliveries 
    SET statut = ?, date_livraison = ? 
    WHERE id = ?
");
$query_update->bind_param("ssi", $new_status, $date_livraison, $delivery_id);

if ($query_update->execute()) {
    echo json_encode(["status" => "success", "message" => "Statut de livraison mis à jour avec succès !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la mise à jour du statut.", "error" => $conn->error]);
}

$conn->close();
?>
