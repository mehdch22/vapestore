<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si l'ID de l'utilisateur est présent
if (!isset($data['utilisateur_id'])) {
    echo json_encode(["message" => "L'ID de l'utilisateur est obligatoire."]);
    exit;
}

$utilisateur_id = $data['utilisateur_id'];

// Récupérer les articles du panier pour cet utilisateur
$query_cart = $conn->prepare("
    SELECT c.product_id, c.quantity, p.prix, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$query_cart->bind_param("i", $utilisateur_id);
$query_cart->execute();
$result_cart = $query_cart->get_result();

if ($result_cart->num_rows > 0) {
    $total_amount = 0;
    $order_items = [];

    // Préparer les articles de la commande et vérifier les stocks
    while ($row = $result_cart->fetch_assoc()) {
        if ($row['quantity'] > $row['stock']) {
            echo json_encode([
                "message" => "Quantité insuffisante pour le produit avec ID {$row['product_id']}. Stock disponible : {$row['stock']}."
            ]);
            exit;
        }
        $total_amount += $row['prix'] * $row['quantity'];
        $order_items[] = $row;
    }

    // Créer la commande
    $query_order = $conn->prepare("INSERT INTO orders (utilisateur_id, montant_total) VALUES (?, ?)");
    $query_order->bind_param("id", $utilisateur_id, $total_amount);

    if ($query_order->execute()) {
        $order_id = $query_order->insert_id;

        // Insérer les articles dans la table order_items et mettre à jour le stock des produits
        foreach ($order_items as $item) {
            $query_items = $conn->prepare("
                INSERT INTO order_items (commande_id, produit_id, quantite, prix)
                VALUES (?, ?, ?, ?)
            ");
            $query_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['prix']);
            $query_items->execute();

            // Réduire le stock du produit
            $query_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $query_stock->bind_param("ii", $item['quantity'], $item['product_id']);
            $query_stock->execute();
        }

        // Vider le panier de l'utilisateur
        $query_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $query_clear_cart->bind_param("i", $utilisateur_id);
        $query_clear_cart->execute();

        echo json_encode(["message" => "Commande créée avec succès.", "order_id" => $order_id]);
    } else {
        echo json_encode(["message" => "Erreur lors de la création de la commande.", "error" => $conn->error]);
    }
} else {
    echo json_encode(["message" => "Le panier est vide. Impossible de créer une commande."]);
}

$conn->close();
?>
