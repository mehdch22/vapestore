<?php
include '../db.php'; // Connexion à la base de données
include '../utils.php'; // Fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    json_response("error", null, "Méthode HTTP invalide.");
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier les champs obligatoires
if (!isset($data['payment_id'], $data['statut'])) {
    json_response("error", null, "Les champs payment_id et statut sont obligatoires.");
    exit;
}

$payment_id = $data['payment_id'];
$statut = $data['statut'];

// Vérifier si le statut est valide
$statuts_valides = ['en_attente', 'terminé', 'échoué'];
if (!in_array($statut, $statuts_valides)) {
    json_response("error", null, "Le statut fourni n'est pas valide. Statuts valides : en_attente, terminé, échoué.");
    exit;
}

// Vérifier si le paiement existe
$payment_query = $conn->prepare("SELECT * FROM payments WHERE id = ?");
$payment_query->bind_param("i", $payment_id);
$payment_query->execute();
$payment_result = $payment_query->get_result();

if ($payment_result->num_rows === 0) {
    json_response("error", null, "Paiement introuvable.");
    exit;
}

// Mettre à jour le statut du paiement
$update_query = $conn->prepare("UPDATE payments SET statut = ? WHERE id = ?");
$update_query->bind_param("si", $statut, $payment_id);

if ($update_query->execute()) {
    json_response("success", null, "Statut du paiement mis à jour avec succès.");
} else {
    json_response("error", null, "Erreur lors de la mise à jour du paiement.");
}

$conn->close();
?>
