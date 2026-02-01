<?php
// config_email.php - Configuration des emails pour Wushu Club CI

// Configuration SMTP (à modifier selon votre fournisseur)
define('SMTP_HOST', 'smtp.gmail.com'); // ou votre serveur SMTP
define('SMTP_PORT', 587); // 587 pour TLS, 465 pour SSL
define('SMTP_USERNAME', 'votre-email@gmail.com'); // Votre email
define('SMTP_PASSWORD', 'votre-mot-de-passe'); // Votre mot de passe
define('SMTP_ENCRYPTION', 'tls'); // 'tls' ou 'ssl'

// Configuration générale
define('FROM_EMAIL', 'contact@wushuclubci.ci');
define('FROM_NAME', 'Wushu Club CI - Fédération Ivoirienne des Arts Martiaux Chinois');
define('REPLY_TO', 'contact@fiamc.ci');

// Fonction pour envoyer un email
function sendEmail($to, $subject, $message, $isHtml = true) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . REPLY_TO . "\r\n";

    if ($isHtml) {
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    } else {
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    }

    // Pour l'instant, on utilise mail() de PHP (à remplacer par une vraie bibliothèque SMTP en production)
    return mail($to, $subject, $message, $headers);
}

// Templates d'emails
function getClubRegistrationTemplate($clubData) {
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: linear-gradient(135deg, #e30613, #d4af37); color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
            .btn { background: #e30613; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🏆 Bienvenue à la FIAMC !</h1>
            <p>Fédération Ivoirienne des Arts Martiaux Chinois</p>
        </div>

        <div class='content'>
            <h2>Confirmation d'inscription de club</h2>
            <p>Bonjour <strong>{$clubData['nom_club']}</strong>,</p>

            <p>Nous avons bien reçu votre demande d'inscription à la FIAMC. Voici un récapitulatif de vos informations :</p>

            <ul>
                <li><strong>Nom du club :</strong> {$clubData['nom_club']}</li>
                <li><strong>Président :</strong> {$clubData['president']}</li>
                <li><strong>Contact :</strong> {$clubData['email']} | {$clubData['telephone']}</li>
                <li><strong>Adresse :</strong> {$clubData['adresse']}</li>
            </ul>

            <p>Votre demande est actuellement en cours de validation par notre équipe. Vous recevrez un email de confirmation dans les plus brefs délais.</p>

            <p>En attendant, n'hésitez pas à nous contacter pour toute question.</p>

            <a href='http://localhost:8080' class='btn'>Visiter notre site</a>
        </div>

        <div class='footer'>
            <p>FIAMC - Fédération Ivoirienne des Arts Martiaux Chinois<br>
            Siège : Yamoussoukro, Côte d'Ivoire<br>
            Email : contact@fiamc.ci | Tél : +225 07 09 67 50 05</p>
        </div>
    </body>
    </html>";
}

function getCompetitorRegistrationTemplate($competitorData) {
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: linear-gradient(135deg, #e30613, #d4af37); color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
            .btn { background: #e30613; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🥋 Bienvenue chez FIAMC !</h1>
            <p>Fédération Ivoirienne des Arts Martiaux Chinois</p>
        </div>

        <div class='content'>
            <h2>Confirmation d'inscription de compétiteur</h2>
            <p>Bonjour <strong>{$competitorData['prenom']} {$competitorData['nom']}</strong>,</p>

            <p>Félicitations ! Votre inscription à la FIAMC a été enregistrée avec succès. Voici vos informations :</p>

            <ul>
                <li><strong>Nom complet :</strong> {$competitorData['prenom']} {$competitorData['nom']}</li>
                <li><strong>Date de naissance :</strong> {$competitorData['date_naissance']}</li>
                <li><strong>Catégorie :</strong> {$competitorData['categorie']}</li>
                <li><strong>Club :</strong> {$competitorData['nom_club']}</li>
                <li><strong>Contact :</strong> {$competitorData['email']} | {$competitorData['telephone']}</li>
            </ul>

            <p>Votre inscription est en attente de validation. Vous pourrez bientôt participer aux compétitions et entraînements organisés par la FIAMC.</p>

            <p>Restez connecté pour découvrir les dernières actualités et événements !</p>

            <a href='http://localhost:8080' class='btn'>Découvrir nos activités</a>
        </div>

        <div class='footer'>
            <p>FIAMC - Fédération Ivoirienne des Arts Martiaux Chinois<br>
            Siège : Yamoussoukro, Côte d'Ivoire<br>
            Email : contact@fiamc.ci | Tél : +225 07 09 67 50 05</p>
        </div>
    </body>
    </html>";
}

function getValidationNotificationTemplate($type, $data) {
    $title = $type === 'club' ? 'Club Validé' : 'Compétiteur Validé';
    $icon = $type === 'club' ? '🏆' : '✅';

    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
            .btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>{$icon} {$title}</h1>
            <p>Fédération Ivoirienne des Arts Martiaux Chinois</p>
        </div>

        <div class='content'>
            <h2>Félicitations !</h2>
            <p>Votre inscription a été validée par notre équipe.</p>

            <p>Vous pouvez maintenant profiter pleinement de tous les services et avantages de la FIAMC.</p>

            <a href='http://localhost:8080' class='btn'>Accéder au site</a>
        </div>

        <div class='footer'>
            <p>FIAMC - Fédération Ivoirienne des Arts Martiaux Chinois<br>
            Email : contact@fiamc.ci | Tél : +225 07 09 67 50 05</p>
        </div>
    </body>
    </html>";
}

// Fonction pour envoyer une notification d'inscription
function sendRegistrationNotification($type, $data) {
    if ($type === 'club') {
        $subject = "Confirmation d'inscription - FIAMC";
        $template = getClubRegistrationTemplate($data);
        $to = $data['email'];
    } elseif ($type === 'competitor') {
        $subject = "Bienvenue à la FIAMC !";
        $template = getCompetitorRegistrationTemplate($data);
        $to = $data['email'];
    }

    return sendEmail($to, $subject, $template);
}

// Fonction pour envoyer une notification de validation
function sendValidationNotification($type, $email, $name) {
    $subject = "Validation de votre inscription - FIAMC";
    $template = getValidationNotificationTemplate($type, ['name' => $name]);

    return sendEmail($email, $subject, $template);
}
?>