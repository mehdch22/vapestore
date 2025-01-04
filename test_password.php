<?php
$mot_de_passe = 'mypassword123';
$hash = '$2y$10$Pf7n1Uxfs9YpnlmyFeNfxu0AqvFcV/sA4o3.JqCPOF59bwXODksOi';

if (password_verify($mot_de_passe, $hash)) {
    echo "Mot de passe correct";
} else {
    echo "Mot de passe incorrect";
}
?>
