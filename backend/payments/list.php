<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si commande_id ou utilisateur_id est fourni
if (!isset($data['commande_id']) && !isset($data['utilisateur_id'])) {
    echo json_encode(["message" => "Vous devez fournir commande_id ou utilisateur_id."]);
    exit;
}

$commande_id = $data['commande_id'] ?? null;
$utilisateur_id = $data['utilisateur_id'] ?? null;

// Construire la requête en fonction des paramètres fournis
if ($commande_id) {
    $query = $conn->prepare("SELECT * FROM payments WHERE commande_id = ?");
    $query->bind_param("i", $commande_id);
} elseif ($utilisateur_id) {
    $query = $conn->prepare("SELECT * FROM payments WHERE utilisateur_id = ?");
    $query->bind_param("i", $utilisateur_id);
}

$query->execute();
$result = $query->get_result();

$payments = [];

// Parcourir les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    echo json_encode($payments);
} else {
    echo json_encode(["message" => "Aucun paiement trouvé."]);
}

$conn->close();
?>
