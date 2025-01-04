<?php
include '../db.php'; // Inclure la connexion à la base de données

$utilisateur_id = $_GET['utilisateur_id'] ?? null;

// Construire la requête
if ($utilisateur_id) {
    $query = $conn->prepare("SELECT * FROM activity_logs WHERE utilisateur_id = ?");
    $query->bind_param("i", $utilisateur_id);
} else {
    $query = $conn->prepare("SELECT * FROM activity_logs");
}

$query->execute();
$result = $query->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

if (!empty($logs)) {
    echo json_encode($logs);
} else {
    echo json_encode(["status" => "error", "message" => "Aucun journal trouvé."]);
}

$conn->close();
?>
