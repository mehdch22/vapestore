<?php
// Fonction pour envoyer une réponse JSON
function json_response($status, $data = null, $message = null) {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => $status,
        "data" => $data,
        "message" => $message
    ]);
    exit;
}

// Fonction pour vérifier le rôle de l'utilisateur
function check_role($required_role, $user_role) {
    if ($user_role !== $required_role) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Accès interdit : rôle insuffisant."
        ]);
        exit;
    }
}


// Fonction pour nettoyer les entrées utilisateur
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Fonction pour valider qu'un champ est obligatoire
function validate_required($field, $value) {
    if (empty($value)) {
        json_response("error", null, "Le champ '$field' est obligatoire.");
    }
}
?>
