<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['id'], $data['nom'], $data['prix'], $data['stock'])) {
    echo json_encode(["message" => "Les champs id, nom, prix et stock sont obligatoires."]);
    exit;
}

$id = $data['id'];
$nom = $data['nom'];
$description = $data['description'] ?? null; // Description facultative
$prix = $data['prix'];
$stock = $data['stock'];
$categorie_id = $data['categorie_id'] ?? null; // Catégorie facultative

// Mettre à jour le produit
$query = $conn->prepare("UPDATE products SET nom = ?, description = ?, prix = ?, stock = ?, categorie_id = ? WHERE id = ?");
$query->bind_param("ssdiii", $nom, $description, $prix, $stock, $categorie_id, $id);

if ($query->execute()) {
    echo json_encode(["message" => "Produit mis à jour avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour du produit.", "error" => $conn->error]);
}

$conn->close();
?>
