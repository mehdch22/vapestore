<?php
include '../db.php'; // Connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les champs obligatoires sont présents
if (!isset($data['nom'], $data['prix'], $data['stock'])) {
    echo json_encode(["message" => "Les champs nom, prix et stock sont obligatoires."]);
    exit;
}

$nom = $data['nom'];
$description = $data['description'] ?? null; // Description facultative
$prix = $data['prix'];
$stock = $data['stock'];
$categorie_id = $data['categorie_id'] ?? null; // Catégorie facultative

// Insérer le produit dans la table
$query = $conn->prepare("INSERT INTO products (nom, description, prix, stock, categorie_id) VALUES (?, ?, ?, ?, ?)");
$query->bind_param("ssdii", $nom, $description, $prix, $stock, $categorie_id);

if ($query->execute()) {
    echo json_encode(["message" => "Produit ajouté avec succès !"]);
} else {
    echo json_encode(["message" => "Erreur lors de l'ajout du produit.", "error" => $conn->error]);
}

$conn->close();
?>
