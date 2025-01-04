<?php
// Inclure la connexion à la base de données
include '../db.php';

// Vérifier que la méthode HTTP est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Accès interdit : méthode HTTP invalide."]);
    exit;
}

// Récupérer les données JSON envoyées via Postman
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que les données nécessaires sont présentes
if (!isset($data['nom'], $data['email'], $data['mot_de_passe'])) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Tous les champs (nom, email, mot_de_passe) sont obligatoires."]);
    exit;
}

// Nettoyer et valider les données
$nom = htmlspecialchars(strip_tags(trim($data['nom'])));
$email = htmlspecialchars(strip_tags(trim($data['email'])));
$mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT); // Hachage sécurisé du mot de passe
$role = isset($data['role']) ? htmlspecialchars(strip_tags(trim($data['role']))) : 'utilisateur'; // Rôle par défaut : utilisateur

// Vérifier si le rôle est valide
$roles_valides = ['utilisateur', 'administrateur', 'support'];
if (!in_array($role, $roles_valides)) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Le rôle fourni n'est pas valide. Les rôles valides sont : utilisateur, administrateur, support."]);
    exit;
}

// Vérifier si l'utilisateur existe déjà dans la base de données
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Cet email est déjà utilisé."]);
    exit;
}

// Insérer un nouvel utilisateur
$insert = $conn->prepare("INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
$insert->bind_param("ssss", $nom, $email, $mot_de_passe, $role);

if ($insert->execute()) {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "message" => "Inscription réussie !",
        "utilisateur" => [
            "nom" => $nom,
            "email" => $email,
            "role" => $role
        ]
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'inscription. Veuillez réessayer."]);
}

// Fermer la connexion à la base de données
$conn->close();
?>
