<?php
include '../db.php'; // Inclure la connexion à la base de données

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['commande_id'], $data['utilisateur_id'], $data['montant'], $data['methode'])) {
    echo json_encode(["message" => "Les champs commande_id, utilisateur_id, montant et methode sont obligatoires."]);
    exit;
}

$commande_id = $data['commande_id'];
$utilisateur_id = $data['utilisateur_id'];
$montant = $data['montant'];
$methode = $data['methode'];
$statut = 'en_attente'; // Par défaut, le paiement est en attente

// Vérifier si la commande existe
$query_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$query_order->bind_param("i", $commande_id);
$query_order->execute();
$result_order = $query_order->get_result();

if ($result_order->num_rows > 0) {
    // Simuler la méthode de paiement
    if ($methode === 'carte_credit') {
        $statut = 'terminé'; // Paiement réussi (simulé)
    } elseif ($methode === 'paypal') {
        $statut = 'terminé'; // Paiement réussi (simulé)
    } elseif ($methode === 'espèces') {
        $statut = 'en_attente'; // Paiement en attente de confirmation
    } elseif ($methode === 'virement') {
        $statut = 'en_attente'; // Paiement en attente de validation
    } else {
        echo json_encode(["message" => "Méthode de paiement invalide."]);
        exit;
    }

    // Ajouter le paiement
    $query_payment = $conn->prepare("
        INSERT INTO payments (commande_id, utilisateur_id, montant, methode, statut)
        VALUES (?, ?, ?, ?, ?)
    ");
    $query_payment->bind_param("iisss", $commande_id, $utilisateur_id, $montant, $methode, $statut);

    if ($query_payment->execute()) {
        echo json_encode(["message" => "Paiement ajouté avec succès.", "statut" => $statut]);
    } else {
        echo json_encode(["message" => "Erreur lors de l'ajout du paiement.", "error" => $conn->error]);
    }
} else {
    echo json_encode(["message" => "Commande introuvable."]);
}

$conn->close();
?>
