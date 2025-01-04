<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les paramètres utilisateur_id ou commande_id depuis l'URL
$utilisateur_id = $_GET['utilisateur_id'] ?? null;
$commande_id = $_GET['commande_id'] ?? null;

// Construire la requête SQL en fonction des paramètres fournis
$sql = "SELECT deliveries.id, 
               orders.id AS commande_id, 
               users.nom AS utilisateur, 
               deliveries.adresse_livraison, 
               deliveries.statut, 
               deliveries.date_creation, 
               deliveries.date_livraison 
        FROM deliveries
        JOIN orders ON deliveries.commande_id = orders.id
        JOIN users ON deliveries.utilisateur_id = users.id";

if ($utilisateur_id) {
    $sql .= " WHERE deliveries.utilisateur_id = ?";
} elseif ($commande_id) {
    $sql .= " WHERE deliveries.commande_id = ?";
}

$query = $conn->prepare($sql);

if ($utilisateur_id) {
    $query->bind_param("i", $utilisateur_id);
} elseif ($commande_id) {
    $query->bind_param("i", $commande_id);
}

$query->execute();
$result = $query->get_result();

$deliveries = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deliveries[] = $row;
    }
    echo json_encode(["status" => "success", "deliveries" => $deliveries]);
} else {
    echo json_encode(["status" => "error", "message" => "Aucune livraison trouvée."]);
}

$conn->close();
?>
