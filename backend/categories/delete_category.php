<?php
include '../db.php'; // Connexion à la base de données
include '../utils.php'; // Fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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

// Vérifier que l'ID de la catégorie est fourni
if (!isset($data['id'])) {
    json_response("error", null, "L'ID de la catégorie est requis.");
    exit;
}

$id = $data['id'];

// Supprimer la catégorie
$query = $conn->prepare("DELETE FROM categories WHERE id = ?");
$query->bind_param("i", $id);

if ($query->execute()) {
    if ($query->affected_rows > 0) {
        json_response("success", null, "Catégorie supprimée avec succès !");
    } else {
        json_response("error", null, "Aucune catégorie trouvée avec cet ID.");
    }
} else {
    json_response("error", null, "Erreur lors de la suppression de la catégorie.");
}

$conn->close();
?>
