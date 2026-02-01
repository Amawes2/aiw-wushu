<?php
session_start();
require_once 'functions.php';
requireAdmin();

// Configuration de la base de données
$db_file = 'fiamc.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Récupérer les statistiques détaillées
$stats = getGeneralStats($pdo);

// Statistiques par mois (derniers 6 mois)
$monthlyStats = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $monthName = date('M Y', strtotime("-$i months"));

    // Clubs inscrits ce mois
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clubs WHERE strftime('%Y-%m', date_inscription) = ?");
    $stmt->execute([$date]);
    $clubs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Compétiteurs inscrits ce mois
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM competiteurs WHERE strftime('%Y-%m', date_inscription) = ?");
    $stmt->execute([$date]);
    $competiteurs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $monthlyStats[] = [
        'month' => $monthName,
        'clubs' => $clubs,
        'competiteurs' => $competiteurs
    ];
}

// Répartition par catégories d'âge
$categoryStats = $pdo->query("
    SELECT categorie, COUNT(*) as total
    FROM competiteurs
    WHERE statut = 'valide'
    GROUP BY categorie
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Top 5 clubs par nombre de membres
$topClubs = $pdo->query("
    SELECT c.nom_club, COUNT(comp.id) as membres
    FROM clubs c
    LEFT JOIN competiteurs comp ON c.id = comp.club_id AND comp.statut = 'valide'
    WHERE c.statut = 'valide'
    GROUP BY c.id, c.nom_club
    ORDER BY membres DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Logs récents
$recentLogs = getRecentLogs($pdo, 10);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FIAMC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/js-styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #e30613;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #e30613;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .detail-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .detail-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .data-list {
            list-style: none;
            padding: 0;
        }

        .data-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .data-item:last-child {
            border-bottom: none;
        }

        .data-label {
            font-weight: 500;
        }

        .data-value {
            font-weight: 600;
            color: #e30613;
        }

        .logs-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .log-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .log-action {
            font-weight: 500;
        }

        .log-time {
            color: #666;
            font-size: 0.8rem;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #e30613;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .refresh-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .refresh-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body class="ivory-theme">
    <div class="dashboard-container">
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au panel admin
        </a>

        <div class="dashboard-header">
            <h1><i class="fas fa-chart-line"></i> Tableau de Bord Détaillé</h1>
            <button class="refresh-btn" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>

        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_clubs']; ?></div>
                <div class="stat-label">Clubs Total</div>
                <div style="font-size: 0.8rem; color: #28a745; margin-top: 5px;">
                    <?php echo $stats['clubs_valides']; ?> validés
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_competiteurs']; ?></div>
                <div class="stat-label">Compétiteurs Total</div>
                <div style="font-size: 0.8rem; color: #28a745; margin-top: 5px;">
                    <?php echo $stats['competiteurs_valides']; ?> validés
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_competitions']; ?></div>
                <div class="stat-label">Compétitions</div>
                <div style="font-size: 0.8rem; color: #007bff; margin-top: 5px;">
                    <?php echo $stats['competitions_futures']; ?> à venir
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_resultats']; ?></div>
                <div class="stat-label">Résultats</div>
                <div style="font-size: 0.8rem; color: #ffd700; margin-top: 5px;">
                    <i class="fas fa-trophy"></i> Enregistrés
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-grid">
            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Évolution des Inscriptions (6 derniers mois)</div>
                <canvas id="monthlyChart" width="400" height="200"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Répartition par Catégorie</div>
                <canvas id="categoryChart" width="200" height="200"></canvas>
            </div>
        </div>

        <!-- Détails -->
        <div class="details-grid">
            <div class="detail-section">
                <div class="detail-title">
                    <i class="fas fa-crown"></i> Top 5 Clubs
                </div>
                <ul class="data-list">
                    <?php foreach ($topClubs as $club): ?>
                        <li class="data-item">
                            <span class="data-label"><?php echo htmlspecialchars($club['nom_club']); ?></span>
                            <span class="data-value"><?php echo $club['membres']; ?> membres</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="detail-section">
                <div class="detail-title">
                    <i class="fas fa-history"></i> Activité Récente
                </div>
                <div class="logs-list">
                    <?php if (empty($recentLogs)): ?>
                        <p style="color: #666; text-align: center; padding: 20px;">Aucune activité récente</p>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="log-item">
                                <span class="log-action"><?php echo htmlspecialchars($log['action']); ?></span>
                                <span class="log-time"><?php echo date('d/m H:i', strtotime($log['date_action'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Graphique des inscriptions mensuelles
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlyStats, 'month')); ?>,
                datasets: [{
                    label: 'Clubs',
                    data: <?php echo json_encode(array_column($monthlyStats, 'clubs')); ?>,
                    borderColor: '#e30613',
                    backgroundColor: 'rgba(227, 6, 19, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Compétiteurs',
                    data: <?php echo json_encode(array_column($monthlyStats, 'competiteurs')); ?>,
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique de répartition par catégorie
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($categoryStats, 'categorie')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($categoryStats, 'total')); ?>,
                    backgroundColor: [
                        '#e30613',
                        '#d4af37',
                        '#28a745',
                        '#007bff',
                        '#6f42c1',
                        '#fd7e14'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>

    <script src="js/main.js"></script>
</body>
</html>