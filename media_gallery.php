<?php
// media_gallery.php - Galerie des médias uploadés
session_start();

// Vérification de l'authentification admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

function getMediaFiles($discipline, $type) {
    $dir = "media/{$discipline}/{$type}/";
    $files = [];

    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && !preg_match('/^metadata_/', $item)) {
                $metadata_file = $dir . 'metadata_' . pathinfo($item, PATHINFO_FILENAME) . '.json';
                $metadata = [];

                if (file_exists($metadata_file)) {
                    $metadata = json_decode(file_get_contents($metadata_file), true);
                }

                $files[] = [
                    'filename' => $item,
                    'path' => $dir . $item,
                    'metadata' => $metadata,
                    'size' => filesize($dir . $item),
                    'date' => filemtime($dir . $item)
                ];
            }
        }
    }

    // Trier par date (plus récent en premier)
    usort($files, function($a, $b) {
        return $b['date'] <=> $a['date'];
    });

    return $files;
}

$disciplines = ['taolu', 'sanda', 'qigong_taichi', 'formes_traditionnelles'];
$media_types = ['images', 'videos'];
$selected_discipline = $_GET['discipline'] ?? 'taolu';
$selected_type = $_GET['type'] ?? 'images';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie Médias - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .gallery-container {
            padding: 30px;
        }
        .gallery-filters {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .filter-group {
            flex: 1;
        }
        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .media-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .media-item:hover {
            transform: translateY(-5px);
        }
        .media-preview {
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .media-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .media-preview.video {
            background: #333;
            color: white;
        }
        .media-info {
            padding: 15px;
        }
        .media-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .media-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        .media-description {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 10px;
        }
        .media-actions {
            display: flex;
            gap: 10px;
        }
        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-view {
            background: #007bff;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-brand">
                <i class="fas fa-images"></i>
                Galerie Médias
            </div>
            <ul class="nav-links">
                <li><a href="admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="media_upload.php"><i class="fas fa-upload"></i> Upload</a></li>
                <li><a href="media_gallery.php" class="active"><i class="fas fa-images"></i> Galerie</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <div class="gallery-container">
                <h1><i class="fas fa-images"></i> Galerie des Médias</h1>

                <div class="gallery-filters">
                    <form method="GET" class="filter-row">
                        <div class="filter-group">
                            <label for="discipline">Discipline :</label>
                            <select name="discipline" id="discipline" onchange="this.form.submit()">
                                <?php foreach ($disciplines as $disc): ?>
                                    <option value="<?php echo $disc; ?>" <?php echo $selected_discipline === $disc ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(str_replace('_', ' & ', $disc)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="type">Type :</label>
                            <select name="type" id="type" onchange="this.form.submit()">
                                <?php foreach ($media_types as $type): ?>
                                    <option value="<?php echo $type; ?>" <?php echo $selected_type === $type ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($type); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="media-grid">
                    <?php
                    $media_files = getMediaFiles($selected_discipline, $selected_type);

                    if (empty($media_files)): ?>
                        <div class="empty-state">
                            <i class="fas fa-<?php echo $selected_type === 'images' ? 'image' : 'video'; ?>"></i>
                            <h3>Aucun média trouvé</h3>
                            <p>Il n'y a encore aucun <?php echo $selected_type === 'images' ? 'image' : 'vidéo'; ?> pour cette discipline.</p>
                            <a href="media_upload.php" class="btn">Uploader un média</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($media_files as $file): ?>
                            <div class="media-item">
                                <div class="media-preview <?php echo $selected_type === 'videos' ? 'video' : ''; ?>">
                                    <?php if ($selected_type === 'images'): ?>
                                        <img src="<?php echo htmlspecialchars($file['path']); ?>"
                                             alt="<?php echo htmlspecialchars($file['metadata']['description'] ?? $file['filename']); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-play-circle fa-3x"></i>
                                        <span><?php echo strtoupper(pathinfo($file['filename'], PATHINFO_EXTENSION)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="media-info">
                                    <div class="media-name">
                                        <?php echo htmlspecialchars($file['metadata']['original_name'] ?? $file['filename']); ?>
                                    </div>
                                    <div class="media-meta">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', $file['date']); ?> |
                                        <i class="fas fa-weight"></i> <?php echo round($file['size'] / 1024 / 1024, 2); ?> MB
                                    </div>
                                    <?php if (!empty($file['metadata']['description'])): ?>
                                        <div class="media-description">
                                            <?php echo htmlspecialchars($file['metadata']['description']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="media-actions">
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" target="_blank" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <button class="btn-action btn-delete" onclick="deleteMedia('<?php echo $file['filename']; ?>', '<?php echo $selected_discipline; ?>', '<?php echo $selected_type; ?>')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteMedia(filename, discipline, type) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
                // Créer un formulaire temporaire pour la suppression
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_media.php';

                const fields = {
                    filename: filename,
                    discipline: discipline,
                    type: type
                };

                for (const [key, value] of Object.entries(fields)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>