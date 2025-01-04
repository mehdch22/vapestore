<?php
// Inclure la connexion à la base de données
include '../db.php';

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['email'], $data['mot_de_passe'])) {
    echo json_encode(["message" => "Tous les champs (email, mot_de_passe) sont obligatoires."]);
    exit;
}

// Assigner les données à des variables
$email = $data['email'];
$mot_de_passe = $data['mot_de_passe'];

// Vérifier si l'utilisateur existe
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $utilisateur = $result->fetch_assoc();

    // Comparer le mot de passe directement (pas de hash)
    if ($mot_de_passe === $utilisateur['mot_de_passe']) {
        echo json_encode([
            "message" => "Connexion réussie !",
            "utilisateur" => [
                "id" => $utilisateur['id'],
                "nom" => $utilisateur['nom'],
                "email" => $utilisateur['email'],
                "role" => $utilisateur['role']
            ]
        ]);
    } else {
        echo json_encode(["message" => "Mot de passe incorrect."]);
    }
} else {
    echo json_encode(["message" => "Email non trouvé."]);
}

// Fermer la connexion
$conn->close();
?>
