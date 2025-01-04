<?php
include '../db.php'; // Connexion à la base de données
include '../utils.php'; // Fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response("error", null, "Méthode HTTP invalide.");
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Validation des champs obligatoires
if (!isset($data['commande_id'], $data['utilisateur_id'], $data['montant'], $data['methode'])) {
    json_response("error", null, "Les champs commande_id, utilisateur_id, montant et methode sont obligatoires.");
    exit;
}

$commande_id = $data['commande_id'];
$utilisateur_id = $data['utilisateur_id'];
$montant = $data['montant'];
$methode = $data['methode'];
$statut = 'en_attente'; // Par défaut

// Valider la méthode de paiement
$methodes_valides = ['carte_credit', 'paypal', 'virement', 'espèces'];
if (!in_array($methode, $methodes_valides)) {
    json_response("error", null, "Méthode de paiement invalide. Méthodes valides : carte_credit, paypal, virement, espèces.");
    exit;
}

// Vérifier si la commande existe
$query_order = $conn->prepare("SELECT montant_total FROM orders WHERE id = ?");
$query_order->bind_param("i", $commande_id);
$query_order->execute();
$result_order = $query_order->get_result();

if ($result_order->num_rows === 0) {
    json_response("error", null, "Commande introuvable.");
    exit;
}

$order_data = $result_order->fetch_assoc();

// Vérifier que le montant payé correspond au montant total
if ($montant != $order_data['montant_total']) {
    json_response("error", null, "Le montant payé doit correspondre au montant total de la commande.");
    exit;
}

// Simuler la méthode de paiement
if ($methode === 'carte_credit' || $methode === 'paypal') {
    $statut = 'terminé'; // Paiement réussi (simulé)
} elseif ($methode === 'espèces' || $methode === 'virement') {
    $statut = 'en_attente'; // En attente de validation
}

// Ajouter le paiement
$query_payment = $conn->prepare("
    INSERT INTO payments (commande_id, utilisateur_id, montant, methode, statut)
    VALUES (?, ?, ?, ?, ?)
");
$query_payment->bind_param("iisss", $commande_id, $utilisateur_id, $montant, $methode, $statut);

if ($query_payment->execute()) {
    json_response("success", ["payment_id" => $query_payment->insert_id, "statut" => $statut], "Paiement ajouté avec succès.");
} else {
    json_response("error", null, "Erreur lors de l'ajout du paiement.");
}

$conn->close();
?>
