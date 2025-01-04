<?php
// Inclure la connexion à la base de données
include '../db.php';

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si les données nécessaires sont présentes
if (!isset($data['nom'], $data['email'], $data['mot_de_passe'])) {
    echo json_encode(["message" => "Tous les champs (nom, email, mot_de_passe) sont obligatoires."]);
    exit;
}

// Assigner les données à des variables
$nom = $data['nom'];
$email = $data['email'];
$mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT); // Hachage sécurisé du mot de passe
$role = isset($data['role']) ? $data['role'] : 'utilisateur'; // Rôle par défaut : utilisateur

// Vérifier si le rôle est valide
$roles_valides = ['utilisateur', 'administrateur', 'support'];
if (!in_array($role, $roles_valides)) {
    echo json_encode(["message" => "Le rôle fourni n'est pas valide. Les rôles valides sont : utilisateur, administrateur, support."]);
    exit;
}

// Vérifier si l'utilisateur existe déjà dans la base de données
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["message" => "Cet email est déjà utilisé."]);
} else {
    // Insérer un nouvel utilisateur
    $insert = $conn->prepare("INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $nom, $email, $mot_de_passe, $role);
    if ($insert->execute()) {
        echo json_encode([
            "message" => "Inscription réussie !",
            "utilisateur" => [
                "nom" => $nom,
                "email" => $email,
                "role" => $role
            ]
        ]);
    } else {
        echo json_encode(["message" => "Erreur lors de l'inscription. Veuillez réessayer."]);
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>
