<?php
// media_upload.php - Script d'upload de médias pour l'administration
session_start();

// Vérification de l'authentification admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $discipline = $_POST['discipline'] ?? '';
    $media_type = $_POST['media_type'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validation
    $allowed_disciplines = ['taolu', 'sanda', 'qigong_taichi', 'formes_traditionnelles'];
    $allowed_types = ['images', 'videos'];

    if (!in_array($discipline, $allowed_disciplines)) {
        $error = 'Discipline non valide.';
    } elseif (!in_array($media_type, $allowed_types)) {
        $error = 'Type de média non valide.';
    } else {
        // Création du dossier si nécessaire
        $upload_dir = "media/{$discipline}/{$media_type}/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file = $_FILES['media_file'];
        $file_name = basename($file['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validation des extensions
        $allowed_images = ['jpg', 'jpeg', 'png', 'webp'];
        $allowed_videos = ['mp4', 'webm', 'avi', 'mov'];

        $allowed_ext = ($media_type === 'images') ? $allowed_images : $allowed_videos;

        if (!in_array($file_ext, $allowed_ext)) {
            $error = 'Extension de fichier non autorisée.';
        } elseif ($file['size'] > 50 * 1024 * 1024) { // 50MB max
            $error = 'Fichier trop volumineux (max 50MB).';
        } else {
            // Génération d'un nom de fichier unique
            $new_filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\-_.]/', '', $file_name);
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Création d'un fichier de métadonnées
                $metadata = [
                    'filename' => $new_filename,
                    'original_name' => $file_name,
                    'description' => $description,
                    'upload_date' => date('Y-m-d H:i:s'),
                    'size' => $file['size'],
                    'type' => $file['type']
                ];

                file_put_contents($upload_dir . 'metadata_' . pathinfo($new_filename, PATHINFO_FILENAME) . '.json',
                                json_encode($metadata, JSON_PRETTY_PRINT));

                $message = 'Média uploadé avec succès !';
            } else {
                $error = 'Erreur lors de l\'upload du fichier.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Médias - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .upload-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .upload-form {
            display: grid;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group select,
        .form-group input[type="file"],
        .form-group textarea {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn-upload {
            background: #e30613;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-upload:hover {
            background: #c10510;
            transform: translateY(-2px);
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-brand">
                <i class="fas fa-upload"></i>
                Upload Médias
            </div>
            <ul class="nav-links">
                <li><a href="admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="media_upload.php" class="active"><i class="fas fa-upload"></i> Upload Médias</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <div class="upload-container">
                <h1><i class="fas fa-cloud-upload-alt"></i> Upload de Médias</h1>
                <p>Ajoutez des images et vidéos pour enrichir les pages de disciplines.</p>

                <?php if ($message): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="upload-form">
                    <div class="form-group">
                        <label for="discipline">Discipline :</label>
                        <select name="discipline" id="discipline" required>
                            <option value="">Choisir une discipline</option>
                            <option value="taolu">Taolu</option>
                            <option value="sanda">Sanda</option>
                            <option value="qigong_taichi">Qigong & Taichi</option>
                            <option value="formes_traditionnelles">Formes Traditionnelles</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="media_type">Type de média :</label>
                        <select name="media_type" id="media_type" required>
                            <option value="">Choisir le type</option>
                            <option value="images">Image</option>
                            <option value="videos">Vidéo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="media_file">Fichier :</label>
                        <input type="file" name="media_file" id="media_file" required
                               accept="image/*,video/*">
                        <div class="file-info">
                            <small>
                                <strong>Formats acceptés :</strong><br>
                                Images : JPG, PNG, WebP (max 50MB)<br>
                                Vidéos : MP4, WebM, AVI, MOV (max 50MB)
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description (optionnel) :</label>
                        <textarea name="description" id="description"
                                  placeholder="Décrivez le contenu du média..."></textarea>
                    </div>

                    <button type="submit" class="btn-upload">
                        <i class="fas fa-upload"></i> Uploader le média
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Prévisualisation du fichier sélectionné
        document.getElementById('media_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileInfo = document.querySelector('.file-info');
                const size = (file.size / 1024 / 1024).toFixed(2);
                fileInfo.innerHTML += `<br><strong>Fichier sélectionné :</strong> ${file.name} (${size} MB)`;
            }
        });
    </script>
</body>
</html>