<?php
include '../db.php'; // Connexion à la base de données
include '../utils.php'; // Fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si l'utilisateur est un administrateur
$utilisateur_id = $data['utilisateur_id'] ?? null;

if (!$utilisateur_id) {
    json_response("error", null, "L'identifiant utilisateur est requis.");
    exit;
}

// Récupérer le rôle de l'utilisateur
$role_query = $conn->prepare("SELECT role FROM users WHERE id = ?");
$role_query->bind_param("i", $utilisateur_id);
$role_query->execute();
$role_result = $role_query->get_result();

if ($role_result->num_rows === 0) {
    json_response("error", null, "Utilisateur introuvable.");
    exit;
}

$user_role = $role_result->fetch_assoc()['role'];
check_role('administrateur', $user_role);

// Vérifier les champs obligatoires
if (!isset($data['id'], $data['nom'], $data['prix'], $data['stock'])) {
    json_response("error", null, "Les champs id, nom, prix et stock sont obligatoires.");
    exit;
}

// Assigner les données à des variables
$id = $data['id'];
$nom = htmlspecialchars(strip_tags(trim($data['nom'])));
$description = htmlspecialchars(strip_tags(trim($data['description'] ?? '')));
$prix = $data['prix'];
$stock = $data['stock'];
$categorie_id = $data['categorie_id'] ?? null;

// Mettre à jour le produit dans la base de données
$query = $conn->prepare("UPDATE products SET nom = ?, description = ?, prix = ?, stock = ?, categorie_id = ? WHERE id = ?");
$query->bind_param("ssdiii", $nom, $description, $prix, $stock, $categorie_id, $id);

if ($query->execute()) {
    if ($query->affected_rows > 0) {
        json_response("success", null, "Produit mis à jour avec succès !");
    } else {
        json_response("error", null, "Aucun produit trouvé avec cet ID.");
    }
} else {
    json_response("error", null, "Erreur lors de la mise à jour du produit. Veuillez réessayer.");
}

$conn->close();
?>
