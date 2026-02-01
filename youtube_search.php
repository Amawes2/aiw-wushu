<?php
// youtube_search.php - Script de recherche de vidéos YouTube
// Note: Nécessite une clé API YouTube Data v3

function searchYouTubeVideos($query, $maxResults = 5) {
    // Remplacer YOUR_API_KEY par votre clé YouTube Data API v3
    $apiKey = 'YOUR_API_KEY';
    $query = urlencode($query);

    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q={$query}&type=video&maxResults={$maxResults}&key={$apiKey}";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Accept: application/json',
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return ['error' => 'Impossible de contacter l\'API YouTube'];
    }

    $data = json_decode($response, true);

    if (isset($data['error'])) {
        return ['error' => $data['error']['message']];
    }

    $videos = [];
    foreach ($data['items'] as $item) {
        $videos[] = [
            'title' => $item['snippet']['title'],
            'description' => $item['snippet']['description'],
            'videoId' => $item['id']['videoId'],
            'thumbnail' => $item['snippet']['thumbnails']['medium']['url'],
            'channelTitle' => $item['snippet']['channelTitle'],
            'publishedAt' => $item['snippet']['publishedAt'],
            'url' => "https://www.youtube.com/watch?v=" . $item['id']['videoId'],
            'embedUrl' => "https://www.youtube.com/embed/" . $item['id']['videoId']
        ];
    }

    return $videos;
}

// Exemples de recherche pour chaque discipline
$searches = [
    'taolu' => [
        'IWUF Wushu Taolu Championship',
        'Chinese Wushu Taolu demonstration',
        'Taolu sword form professional'
    ],
    'sanda' => [
        'Sanda World Championship fight',
        'Sanda fighting techniques',
        'IWUF Sanda competition'
    ],
    'qigong_taichi' => [
        'Taichi 24 forms complete',
        'Qigong health exercises',
        'Chen style Taichi Quan'
    ],
    'formes_traditionnelles' => [
        'Shaolin Kung Fu forms',
        'Wudang martial arts',
        'Traditional Chinese weapons'
    ]
];

$message = '';
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $discipline = $_POST['discipline'] ?? '';
    $customQuery = $_POST['custom_query'] ?? '';

    if (!empty($customQuery)) {
        $results = searchYouTubeVideos($customQuery);
    } elseif (!empty($discipline) && isset($searches[$discipline])) {
        $query = $searches[$discipline][0]; // Utilise la première requête par défaut
        $results = searchYouTubeVideos($query);
    }

    if (isset($results['error'])) {
        $message = 'Erreur : ' . $results['error'];
        $results = [];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Vidéos YouTube - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .search-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
        }
        .search-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 2fr auto;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .form-group select,
        .form-group input {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .btn-search {
            background: #e30613;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            align-self: end;
        }
        .btn-search:hover {
            background: #c10510;
            transform: translateY(-2px);
        }
        .results-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .video-result {
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 20px;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
            margin-bottom: 20px;
            align-items: start;
        }
        .video-thumbnail {
            width: 200px;
            height: 112px;
            border-radius: 8px;
            overflow: hidden;
        }
        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .video-info h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.1rem;
        }
        .video-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .video-description {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .video-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .video-id {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.8rem;
            word-break: break-all;
        }
        .btn-copy {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-copy:hover {
            background: #218838;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .api-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-brand">
                <i class="fab fa-youtube"></i>
                Recherche YouTube
            </div>
            <ul class="nav-links">
                <li><a href="admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="media_upload.php"><i class="fas fa-upload"></i> Upload</a></li>
                <li><a href="media_gallery.php"><i class="fas fa-images"></i> Galerie</a></li>
                <li><a href="youtube_search.php" class="active"><i class="fab fa-youtube"></i> YouTube</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <div class="search-container">
                <h1><i class="fab fa-youtube"></i> Recherche de Vidéos YouTube</h1>

                <div class="api-note">
                    <h4><i class="fas fa-info-circle"></i> Configuration Requise</h4>
                    <p>Pour utiliser cette fonctionnalité, vous devez :</p>
                    <ol>
                        <li>Créer un projet Google Cloud Console</li>
                        <li>Activer l'API YouTube Data v3</li>
                        <li>Créer une clé API</li>
                        <li>Remplacer 'YOUR_API_KEY' dans le fichier youtube_search.php</li>
                    </ol>
                    <p><strong>Note :</strong> L'API YouTube a des quotas gratuits limités (10,000 unités/jour).</p>
                </div>

                <?php if ($message): ?>
                    <div class="message <?php echo strpos($message, 'Erreur') === 0 ? 'error' : 'success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="search-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="discipline">Discipline :</label>
                            <select name="discipline" id="discipline">
                                <option value="">Choisir une discipline</option>
                                <option value="taolu">Taolu</option>
                                <option value="sanda">Sanda</option>
                                <option value="qigong_taichi">Qigong & Taichi</option>
                                <option value="formes_traditionnelles">Formes Traditionnelles</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="custom_query">Recherche personnalisée :</label>
                            <input type="text" name="custom_query" id="custom_query"
                                   placeholder="IWUF Wushu Taolu Championship, Sanda fight, etc.">
                        </div>
                        <button type="submit" name="search" class="btn-search">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </form>

                <?php if (!empty($results)): ?>
                    <div class="results-container">
                        <h2>Résultats de Recherche (<?php echo count($results); ?> vidéos)</h2>

                        <?php foreach ($results as $video): ?>
                            <div class="video-result">
                                <div class="video-thumbnail">
                                    <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>"
                                         alt="<?php echo htmlspecialchars($video['title']); ?>">
                                </div>
                                <div class="video-info">
                                    <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                                    <div class="video-meta">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($video['channelTitle']); ?> |
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($video['publishedAt'])); ?>
                                    </div>
                                    <div class="video-description">
                                        <?php echo htmlspecialchars(substr($video['description'], 0, 200) . '...'); ?>
                                    </div>
                                    <div class="video-actions">
                                        <div class="video-id">
                                            <strong>ID:</strong> <?php echo htmlspecialchars($video['videoId']); ?>
                                        </div>
                                        <button class="btn-copy" onclick="copyToClipboard('<?php echo $video['embedUrl']; ?>')">
                                            <i class="fas fa-copy"></i> Copier l'URL d'intégration
                                        </button>
                                        <a href="<?php echo htmlspecialchars($video['url']); ?>" target="_blank" class="btn-copy" style="background: #dc3545;">
                                            <i class="fab fa-youtube"></i> Voir sur YouTube
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('URL d\'intégration copiée !');
            }, function(err) {
                // Fallback pour les navigateurs plus anciens
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('URL d\'intégration copiée !');
            });
        }

        // Mise à jour automatique de la recherche personnalisée
        document.getElementById('discipline').addEventListener('change', function() {
            const discipline = this.value;
            const customQuery = document.getElementById('custom_query');

            const suggestions = {
                'taolu': 'IWUF Wushu Taolu Championship',
                'sanda': 'Sanda World Championship fight',
                'qigong_taichi': 'Taichi 24 forms complete',
                'formes_traditionnelles': 'Shaolin Kung Fu forms'
            };

            if (suggestions[discipline]) {
                customQuery.value = suggestions[discipline];
            }
        });
    </script>
</body>
</html>