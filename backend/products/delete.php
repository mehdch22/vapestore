<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si l'ID est présent
if (!isset($data['id'])) {
    echo json_encode(["message" => "L'ID du produit est obligatoire."]);
    exit;
}

$id = $data['id'];

// Supprimer le produit
$query = $conn->prepare("DELETE FROM products WHERE id = ?");
$query->bind_param("i", $id);

if ($query->execute()) {
    if ($query->affected_rows > 0) {
        echo json_encode(["message" => "Produit supprimé avec succès !"]);
    } else {
        echo json_encode(["message" => "Aucun produit trouvé avec cet ID."]);
    }
} else {
    echo json_encode(["message" => "Erreur lors de la suppression du produit.", "error" => $conn->error]);
}

$conn->close();
?>
