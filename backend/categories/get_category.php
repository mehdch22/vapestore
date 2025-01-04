<?php
include '../db.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

// Vérifier si un ID est fourni
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    // Récupérer une catégorie spécifique
    $query = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        echo json_encode(["status" => "success", "data" => $category]);
    } else {
        echo json_encode(["status" => "error", "message" => "Aucune catégorie trouvée avec cet ID."]);
    }
} else {
    // Récupérer toutes les catégories
    $query = $conn->query("SELECT * FROM categories ORDER BY date_creation DESC");
    $categories = $query->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["status" => "success", "data" => $categories]);
}

$conn->close();
?>
