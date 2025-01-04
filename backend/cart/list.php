<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si l'ID de l'utilisateur est fourni
if (!isset($data['user_id'])) {
    echo json_encode(["message" => "L'ID de l'utilisateur est obligatoire."]);
    exit;
}

$user_id = $data['user_id'];

// Requête pour récupérer les articles du panier avec les détails des produits
$query = $conn->prepare("
    SELECT c.id AS cart_id, c.quantity, p.nom, p.description, p.prix, (c.quantity * p.prix) AS total_price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$cart_items = [];

// Parcourir les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
    echo json_encode($cart_items);
} else {
    echo json_encode(["message" => "Le panier est vide."]);
}

$conn->close();
?>
