<?php
include '../db.php'; // Connexion à la base de données
include '../utils.php'; // Fonctions utilitaires

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response("error", null, "Méthode HTTP invalide.");
    exit;
}

// Récupérer les paramètres GET (commande_id ou utilisateur_id)
$commande_id = isset($_GET['commande_id']) ? intval($_GET['commande_id']) : null;
$utilisateur_id = isset($_GET['utilisateur_id']) ? intval($_GET['utilisateur_id']) : null;

// Construire la requête en fonction des paramètres fournis
if ($commande_id) {
    $query = $conn->prepare("SELECT * FROM payments WHERE commande_id = ?");
    $query->bind_param("i", $commande_id);
} elseif ($utilisateur_id) {
    $query = $conn->prepare("SELECT * FROM payments WHERE utilisateur_id = ?");
    $query->bind_param("i", $utilisateur_id);
} else {
    json_response("error", null, "Vous devez fournir commande_id ou utilisateur_id.");
    exit;
}

$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
    json_response("success", $payments, "Paiements récupérés avec succès.");
} else {
    json_response("error", null, "Aucun paiement trouvé.");
}

$conn->close();
?>
