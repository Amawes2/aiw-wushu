<?php
// contact.php - Traitement du formulaire de contact pour AIW Wushu

// Configuration de l'email (à personnaliser)
$to = "contact@aiw-wushu.ci"; // Remplacez par l'email réel de l'AIW
$subject_prefix = "Message depuis le site AIW Wushu";

// Fonction pour nettoyer les données
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et nettoyer les données
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $subject = clean_input($_POST['subject']);
    $message = clean_input($_POST['message']);

    // Validation basique
    $errors = [];
    if (empty($name)) $errors[] = "Le nom est requis.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email valide requis.";
    if (empty($message)) $errors[] = "Le message est requis.";

    if (empty($errors)) {
        // Préparer l'email
        $full_subject = $subject_prefix . ": " . $subject;
        $body = "Nom: $name\n";
        $body .= "Email: $email\n";
        $body .= "Sujet: $subject\n\n";
        $body .= "Message:\n$message\n\n";
        $body .= "--\nEnvoyé depuis le site web AIW Wushu";

        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Envoyer l'email
        if (mail($to, $full_subject, $body, $headers)) {
            $success = "Merci $name ! Votre message a été envoyé avec succès. Nous vous contacterons bientôt.";
        } else {
            $error = "Erreur lors de l'envoi du message. Veuillez réessayer ou nous contacter directement.";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - AIW Wushu</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            text-align: center;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .btn-back { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <?php if (isset($success)): ?>
                <h2 class="success">Message Envoyé !</h2>
                <p><?php echo $success; ?></p>
            <?php elseif (isset($error)): ?>
                <h2 class="error">Erreur</h2>
                <p><?php echo $error; ?></p>
            <?php else: ?>
                <h2>Formulaire de Contact</h2>
                <p>Utilisez le formulaire sur la page principale pour nous contacter.</p>
            <?php endif; ?>

            <a href="index.html" class="btn btn-back">Retour au Site</a>
        </div>
    </div>
</body>
</html>