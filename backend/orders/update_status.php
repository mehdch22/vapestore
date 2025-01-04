<?php
// Inclure la connexion à la base de données et les fonctions utilitaires
include '../db.php';
include '../utils.php';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Méthode HTTP invalide."]);
    exit;
}

// Récupérer les données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que les champs nécessaires sont présents
if (!isset($data['utilisateur_id'], $data['order_id'], $data['statut'])) {
    echo json_encode(["status" => "error", "message" => "Les champs utilisateur_id, order_id et statut sont obligatoires."]);
    exit;
}

$utilisateur_id = $data['utilisateur_id'];
$order_id = $data['order_id'];
$statut = $data['statut'];

// Récupérer le rôle de l'utilisateur
$query_role = $conn->prepare("SELECT role FROM users WHERE id = ?");
$query_role->bind_param("i", $utilisateur_id);
$query_role->execute();
$result_role = $query_role->get_result();

if ($result_role->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Utilisateur introuvable."]);
    exit;
}

$role = $result_role->fetch_assoc()['role'];

// Vérifier les droits d'accès
if ($role !== 'administrateur') {
    echo json_encode(["status" => "error", "message" => "Accès interdit : rôle insuffisant."]);
    exit;
}

// Vérifier si le statut est valide
$statuts_valides = ['en_attente', 'terminée', 'annulée'];
if (!in_array($statut, $statuts_valides)) {
    echo json_encode(["status" => "error", "message" => "Le statut fourni n'est pas valide."]);
    exit;
}

// Mettre à jour le statut de la commande
$query_update = $conn->prepare("UPDATE orders SET statut = ? WHERE id = ?");
$query_update->bind_param("si", $statut, $order_id);

if ($query_update->execute()) {
    if ($query_update->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Commande mise à jour avec succès."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Commande introuvable.", "order_id" => $order_id]);
    }
}

 else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la mise à jour.", "error" => $conn->error]);
}

$conn->close();
?>
