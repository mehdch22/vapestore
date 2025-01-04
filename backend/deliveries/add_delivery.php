<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs nécessaires sont présents
if (!isset($data['commande_id'], $data['utilisateur_id'], $data['adresse_livraison'])) {
    echo json_encode(["status" => "error", "message" => "Les champs commande_id, utilisateur_id et adresse_livraison sont obligatoires."]);
    exit;
}

$commande_id = intval($data['commande_id']);
$utilisateur_id = intval($data['utilisateur_id']);
$adresse_livraison = htmlspecialchars(strip_tags(trim($data['adresse_livraison'])));

// Vérifier si la commande existe
$query_order = $conn->prepare("SELECT id FROM orders WHERE id = ?");
$query_order->bind_param("i", $commande_id);
$query_order->execute();
$result_order = $query_order->get_result();

if ($result_order->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Commande introuvable."]);
    exit;
}

// Vérifier si l'utilisateur existe
$query_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
$query_user->bind_param("i", $utilisateur_id);
$query_user->execute();
$result_user = $query_user->get_result();

if ($result_user->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Utilisateur introuvable."]);
    exit;
}

// Insérer la livraison dans la base de données
$query_delivery = $conn->prepare("
    INSERT INTO deliveries (commande_id, utilisateur_id, adresse_livraison)
    VALUES (?, ?, ?)
");
$query_delivery->bind_param("iis", $commande_id, $utilisateur_id, $adresse_livraison);

if ($query_delivery->execute()) {
    echo json_encode(["status" => "success", "message" => "Livraison ajoutée avec succès !"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout de la livraison.", "error" => $conn->error]);
}

$conn->close();
?>
