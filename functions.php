<?php
// functions.php - Fonctions utilitaires pour Wushu Club CI

/**
 * Fonction de logging des actions importantes
 */
function logAction($pdo, $action, $details = '', $user_id = null) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt = $pdo->prepare("INSERT INTO logs (action, details, user_id, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$action, $details, $user_id, $ip]);
    } catch (Exception $e) {
        // Log l'erreur dans le fichier error_log de PHP
        error_log("Erreur de logging: " . $e->getMessage());
    }
}

/**
 * Fonction pour obtenir les logs récents
 */
function getRecentLogs($pdo, $limit = 50) {
    try {
        $stmt = $pdo->query("SELECT * FROM logs ORDER BY date_action DESC LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Fonction pour nettoyer et valider les données d'entrée
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Fonction pour valider un email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Fonction pour valider un numéro de téléphone ivoirien
 */
function isValidPhone($phone) {
    // Format Côte d'Ivoire: +225 XX XX XX XX XX ou 07 XX XX XX XX
    $phone = preg_replace('/\s+/', '', $phone);
    return preg_match('/^(\+225|00225)?[0-9]{10}$/', $phone);
}

/**
 * Fonction pour calculer l'âge à partir d'une date de naissance
 */
function calculateAge($birthDate) {
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $today->diff($birth)->y;
}

/**
 * Fonction pour déterminer la catégorie selon l'âge
 */
function getCategoryByAge($age) {
    if ($age >= 8 && $age <= 11) return 'benjamin';
    if ($age >= 12 && $age <= 14) return 'minime';
    if ($age >= 15 && $age <= 17) return 'cadet';
    if ($age >= 18 && $age <= 20) return 'junior';
    if ($age >= 21) return 'senior';
    return 'trop_jeune';
}

/**
 * Fonction pour formater une date en français
 */
function formatDateFr($date) {
    $dateTime = new DateTime($date);
    $mois = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'
    ];

    return $dateTime->format('j') . ' ' . $mois[(int)$dateTime->format('n')] . ' ' . $dateTime->format('Y');
}

/**
 * Fonction pour obtenir les statistiques générales
 */
function getGeneralStats($pdo) {
    $stats = [];

    // Clubs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubs");
    $stats['total_clubs'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubs WHERE statut = 'valide'");
    $stats['clubs_valides'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Compétiteurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM competiteurs");
    $stats['total_competiteurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM competiteurs WHERE statut = 'valide'");
    $stats['competiteurs_valides'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Compétitions
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM competitions");
    $stats['total_competitions'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM competitions WHERE date_competition > date('now')");
    $stats['competitions_futures'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Résultats
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM resultats");
    $stats['total_resultats'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    return $stats;
}

/**
 * Fonction pour générer un mot de passe aléatoire
 */
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

/**
 * Fonction pour vérifier si une session admin est valide
 */
function isAdminSessionValid() {
    return isset($_SESSION['admin_logged_in']) &&
           $_SESSION['admin_logged_in'] === true &&
           isset($_SESSION['login_time']) &&
           (time() - $_SESSION['login_time'] < 1800); // 30 minutes
}

/**
 * Fonction pour rediriger si non admin
 */
function requireAdmin() {
    if (!isAdminSessionValid()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Fonction pour obtenir l'adresse IP réelle
 */
function getRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
?>