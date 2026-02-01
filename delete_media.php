<?php
// delete_media.php - Suppression de médias
session_start();

// Vérification de l'authentification admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = $_POST['filename'] ?? '';
    $discipline = $_POST['discipline'] ?? '';
    $type = $_POST['type'] ?? '';

    // Validation
    $allowed_disciplines = ['taolu', 'sanda', 'qigong_taichi', 'formes_traditionnelles'];
    $allowed_types = ['images', 'videos'];

    if (empty($filename) || !in_array($discipline, $allowed_disciplines) || !in_array($type, $allowed_types)) {
        $_SESSION['error'] = 'Paramètres invalides.';
    } else {
        $file_path = "media/{$discipline}/{$type}/{$filename}";
        $metadata_path = "media/{$discipline}/{$type}/metadata_" . pathinfo($filename, PATHINFO_FILENAME) . '.json';

        $deleted = false;

        // Supprimer le fichier principal
        if (file_exists($file_path)) {
            unlink($file_path);
            $deleted = true;
        }

        // Supprimer le fichier de métadonnées
        if (file_exists($metadata_path)) {
            unlink($metadata_path);
        }

        if ($deleted) {
            $_SESSION['message'] = 'Média supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Fichier non trouvé.';
        }
    }
}

header('Location: media_gallery.php?discipline=' . urlencode($discipline) . '&type=' . urlencode($type));
exit();
?>