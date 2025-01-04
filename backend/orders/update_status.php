<?php
include '../db.php'; // Inclure la connexion à la base de données
include '../utils.php'; // Inclure les fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id'], $data['statut'], $data['utilisateur_id'])) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Les champs order_id, statut et utilisateur_id sont obligatoires."]);
    exit;
}

$order_id = $data['order_id'];
$statut = $data['statut'];
$utilisateur_id = $data['utilisateur_id'];

// Récupérer le rôle de l'utilisateur depuis la base de données
$role_query = $conn->prepare("SELECT role FROM users WHERE id = ?");
$role_query->bind_param("i", $utilisateur_id);
$role_query->execute();
$role_result = $role_query->get_result();

if ($role_result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Utilisateur introuvable."]);
    exit;
}

$user_role = $role_result->fetch_assoc()['role'];

// Vérifier les droits d'accès
check_role('administrateur', $user_role);

// Vérifier si le statut est valide
$statuts_valides = ['en_attente', 'terminée', 'annulée'];
if (!in_array($statut, $statuts_valides)) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Le statut fourni n'est pas valide."]);
    exit;
}

// Si le statut est "terminée", mettre à jour les stocks
if ($statut === 'terminée') {
    // Récupérer les produits de la commande
    $order_items_query = $conn->prepare("SELECT produit_id, quantite FROM order_items WHERE commande_id = ?");
    $order_items_query->bind_param("i", $order_id);
    $order_items_query->execute();
    $order_items_result = $order_items_query->get_result();

    while ($item = $order_items_result->fetch_assoc()) {
        $produit_id = $item['produit_id'];
        $quantite = $item['quantite'];

        // Vérifier le stock disponible
        $stock_query = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stock_query->bind_param("i", $produit_id);
        $stock_query->execute();
        $stock_result = $stock_query->get_result();

        if ($stock_result->num_rows > 0) {
            $stock_disponible = $stock_result->fetch_assoc()['stock'];

            if ($stock_disponible < $quantite) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Stock insuffisant pour le produit ID : $produit_id."
                ]);
                exit;
            }

            // Mettre à jour le stock
            $update_stock_query = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $update_stock_query->bind_param("ii", $quantite, $produit_id);
            $update_stock_query->execute();
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Produit ID : $produit_id introuvable."
            ]);
            exit;
        }
    }
}

// Mettre à jour le statut de la commande
$update_order_query = $conn->prepare("UPDATE orders SET statut = ? WHERE id = ?");
$update_order_query->bind_param("si", $statut, $order_id);

if ($update_order_query->execute()) {
    echo json_encode(["status" => "success", "message" => "Commande mise à jour avec succès."]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la mise à jour de la commande.", "error" => $conn->error]);
}

$conn->close();
?>
