<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['user_id'], $data['product_id'], $data['quantity'])) {
    echo json_encode(["message" => "Les champs user_id, product_id et quantity sont obligatoires."]);
    exit;
}

$user_id = $data['user_id'];
$product_id = $data['product_id'];
$quantity = $data['quantity'];

// Vérifier le stock du produit
$query_stock = $conn->prepare("SELECT stock FROM products WHERE id = ?");
$query_stock->bind_param("i", $product_id);
$query_stock->execute();
$result_stock = $query_stock->get_result();

if ($result_stock->num_rows > 0) {
    $product = $result_stock->fetch_assoc();
    $stock_disponible = $product['stock'];

    if ($quantity > $stock_disponible) {
        echo json_encode(["message" => "Quantité demandée supérieure au stock disponible."]);
        exit;
    }
} else {
    echo json_encode(["message" => "Produit non trouvé."]);
    exit;
}

// Vérifier si le produit existe déjà dans le panier pour cet utilisateur
$query = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$query->bind_param("ii", $user_id, $product_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    // Mettre à jour la quantité si le produit est déjà dans le panier
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
    $update->bind_param("iii", $quantity, $user_id, $product_id);
    if ($update->execute()) {
        // Réduire le stock du produit
        $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stock->bind_param("ii", $quantity, $product_id);
        $update_stock->execute();

        echo json_encode(["message" => "Quantité mise à jour dans le panier."]);
    } else {
        echo json_encode(["message" => "Erreur lors de la mise à jour de la quantité.", "error" => $conn->error]);
    }
} else {
    // Ajouter un nouveau produit dans le panier
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    if ($insert->execute()) {
        // Réduire le stock du produit
        $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stock->bind_param("ii", $quantity, $product_id);
        $update_stock->execute();

        echo json_encode(["message" => "Produit ajouté au panier."]);
    } else {
        echo json_encode(["message" => "Erreur lors de l'ajout du produit au panier.", "error" => $conn->error]);
    }
}

$conn->close();
?>
