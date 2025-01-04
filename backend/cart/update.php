<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['cart_id'], $data['quantity'])) {
    echo json_encode(["message" => "Les champs cart_id et quantity sont obligatoires."]);
    exit;
}

$cart_id = $data['cart_id'];
$new_quantity = $data['quantity'];

// Récupérer les informations actuelles du produit dans le panier
$query_cart = $conn->prepare("
    SELECT c.product_id, c.quantity AS current_quantity, p.stock
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
    $current_quantity = $cart_item['current_quantity'];
    $stock = $cart_item['stock'];

    // Calculer la différence entre la nouvelle et l'ancienne quantité
    $quantity_difference = $new_quantity - $current_quantity;

    // Vérifier si le stock est suffisant
    if ($quantity_difference > 0 && $quantity_difference > $stock) {
        echo json_encode(["message" => "Quantité demandée supérieure au stock disponible."]);
        exit;
    }

    // Mettre à jour la quantité dans le panier
    $update_cart = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update_cart->bind_param("ii", $new_quantity, $cart_id);
    if ($update_cart->execute()) {
        // Ajuster le stock du produit
        $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stock->bind_param("ii", $quantity_difference, $product_id);
        $update_stock->execute();

        echo json_encode(["message" => "Quantité mise à jour avec succès."]);
    } else {
        echo json_encode(["message" => "Erreur lors de la mise à jour de la quantité.", "error" => $conn->error]);
    }
} else {
    echo json_encode(["message" => "Article non trouvé dans le panier."]);
}

$conn->close();
?>
