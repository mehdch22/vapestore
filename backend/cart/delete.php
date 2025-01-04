<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['cart_id'])) {
    echo json_encode(["message" => "L'ID de l'article du panier est obligatoire."]);
    exit;
}

$cart_id = $data['cart_id'];

// Récupérer les informations du produit à supprimer
$query_cart = $conn->prepare("
    SELECT c.product_id, c.quantity, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.id = ?
");
$query_cart->bind_param("i", $cart_id);
$query_cart->execute();
$result_cart = $query_cart->get_result();

if ($result_cart->num_rows > 0) {
    $cart_item = $result_cart->fetch_assoc();
    $product_id = $cart_item['product_id'];
    $quantity = $cart_item['quantity'];

    // Supprimer l'article du panier
    $delete_cart = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $delete_cart->bind_param("i", $cart_id);

    if ($delete_cart->execute()) {
        // Réajuster le stock du produit
        $update_stock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $update_stock->bind_param("ii", $quantity, $product_id);
        $update_stock->execute();

        echo json_encode(["message" => "Article supprimé du panier avec succès."]);
    } else {
        echo json_encode(["message" => "Erreur lors de la suppression de l'article du panier.", "error" => $conn->error]);
    }
} else {
    echo json_encode(["message" => "Article non trouvé dans le panier."]);
}

$conn->close();
?>
