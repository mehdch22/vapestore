<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer tous les retours
$query = $conn->prepare("SELECT * FROM returns");
$query->execute();
$result = $query->get_result();

$retours = [];
while ($row = $result->fetch_assoc()) {
    $retours[] = $row;
}

echo json_encode($retours);

$query->close();
$conn->close();
?>
