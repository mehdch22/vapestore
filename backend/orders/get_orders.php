<?php
include '../db.php'; // Connexion à la base de données
include '../utils.php'; // Fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response("error", null, "Méthode HTTP invalide.");
    exit;
}

// Récupérer l'utilisateur ou l'ID de commande
$utilisateur_id = isset($_GET['utilisateur_id']) ? intval($_GET['utilisateur_id']) : null;
$commande_id = isset($_GET['commande_id']) ? intval($_GET['commande_id']) : null;

if ($commande_id) {
    // Récupérer les détails d'une commande spécifique
    $query_order = $conn->prepare("
        SELECT o.id AS commande_id, o.montant_total, o.statut, o.date_creation,
               oi.produit_id, oi.quantite, oi.prix, p.nom AS produit_nom
        FROM orders o
        JOIN order_items oi ON o.id = oi.commande_id
        JOIN products p ON oi.produit_id = p.id
        WHERE o.id = ?
    ");
    $query_order->bind_param("i", $commande_id);
    $query_order->execute();
    $result_order = $query_order->get_result();

    if ($result_order->num_rows > 0) {
        $commande = [];
        while ($row = $result_order->fetch_assoc()) {
            $commande['commande_id'] = $row['commande_id'];
            $commande['montant_total'] = $row['montant_total'];
            $commande['statut'] = $row['statut'];
            $commande['date_creation'] = $row['date_creation'];
            $commande['articles'][] = [
                "produit_id" => $row['produit_id'],
                "produit_nom" => $row['produit_nom'],
                "quantite" => $row['quantite'],
                "prix" => $row['prix']
            ];
        }
        json_response("success", $commande, "Détails de la commande récupérés avec succès.");
    } else {
        json_response("error", null, "Aucune commande trouvée avec cet ID.");
    }
} elseif ($utilisateur_id) {
    // Récupérer toutes les commandes d'un utilisateur spécifique
    $query_orders = $conn->prepare("
        SELECT id, montant_total, statut, date_creation
        FROM orders
        WHERE utilisateur_id = ?
        ORDER BY date_creation DESC
    ");
    $query_orders->bind_param("i", $utilisateur_id);
    $query_orders->execute();
    $result_orders = $query_orders->get_result();

    if ($result_orders->num_rows > 0) {
        $commandes = $result_orders->fetch_all(MYSQLI_ASSOC);
        json_response("success", $commandes, "Commandes récupérées avec succès.");
    } else {
        json_response("error", null, "Aucune commande trouvée pour cet utilisateur.");
    }
} else {
    json_response("error", null, "Veuillez fournir un utilisateur_id ou un commande_id.");
}

$conn->close();
?>
