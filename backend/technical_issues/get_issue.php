<?php
include '../db.php'; // Connexion à la base de données

// Récupérer tous les problèmes techniques
$sql = "SELECT technical_issues.id, users.nom AS utilisateur, technical_issues.description, technical_issues.statut, technical_issues.date_creation 
        FROM technical_issues 
        JOIN users ON technical_issues.utilisateur_id = users.id";
$result = $conn->query($sql);

$issues = [];
while ($row = $result->fetch_assoc()) {
    $issues[] = $row;
}

echo json_encode($issues);

$conn->close();
?>
