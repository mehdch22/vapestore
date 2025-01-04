<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les paramètres de filtrage depuis l'URL
$utilisateur_id = $_GET['utilisateur_id'] ?? null;
$statut = $_GET['statut'] ?? null;

// Construire la requête SQL avec des filtres dynamiques
$sql = "SELECT queries.id, 
               users.nom AS utilisateur, 
               queries.message, 
               queries.reponse, 
               queries.statut, 
               queries.date_creation 
        FROM queries 
        JOIN users ON queries.utilisateur_id = users.id";

$conditions = [];
$params = [];
$types = "";

// Ajouter des filtres si présents
if ($utilisateur_id) {
    $conditions[] = "queries.utilisateur_id = ?";
    $params[] = $utilisateur_id;
    $types .= "i";
}

if ($statut) {
    $conditions[] = "queries.statut = ?";
    $params[] = $statut;
    $types .= "s";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$query = $conn->prepare($sql);

// Lier les paramètres dynamiquement si des filtres sont présents
if (!empty($params)) {
    $query->bind_param($types, ...$params);
}

$query->execute();
$result = $query->get_result();

$queries = [];

// Parcourir les résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $queries[] = $row;
    }
    echo json_encode(["status" => "success", "queries" => $queries]);
} else {
    echo json_encode(["status" => "error", "message" => "Aucune requête trouvée."]);
}

$conn->close();
?>
