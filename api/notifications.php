<?php
// api/notifications.php - API pour les notifications temps réel

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Inclure les fonctions utilitaires
require_once '../functions.php';

// Configuration de la base de données
$db_file = '../wushuclubci.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check':
        // Vérifier les nouvelles notifications
        checkNewNotifications($pdo);
        break;

    case 'send_test':
        // Envoyer une notification de test (pour développement)
        sendTestNotification($pdo);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action non valide']);
        break;
}

function checkNewNotifications($pdo) {
    try {
        // Récupérer les dernières notifications (simulées pour l'instant)
        $notifications = [];

        // Simulation de notifications basées sur les événements récents
        $recentEvents = getRecentEvents($pdo);

        foreach ($recentEvents as $event) {
            if (shouldNotify($event)) {
                $notifications[] = [
                    'id' => $event['id'],
                    'type' => getNotificationType($event),
                    'message' => getNotificationMessage($event),
                    'timestamp' => $event['timestamp'],
                    'persistent' => false
                ];
            }
        }

        // Ajouter des notifications système occasionnelles
        if (rand(1, 10) === 1) { // 10% de chance
            $notifications[] = [
                'id' => 'system_' . time(),
                'type' => 'info',
                'message' => 'Système Wushu Club CI opérationnel - ' . date('H:i'),
                'timestamp' => date('Y-m-d H:i:s'),
                'persistent' => false
            ];
        }

        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function getRecentEvents($pdo) {
    // Récupérer les événements récents qui pourraient déclencher des notifications
    $events = [];

    // Nouveaux clubs inscrits (dernière heure)
    $stmt = $pdo->prepare("
        SELECT 'club_registered' as type, id, nom_club as name, date_inscription as timestamp
        FROM clubs
        WHERE date_inscription > datetime('now', '-1 hour')
        ORDER BY date_inscription DESC
    ");
    $stmt->execute();
    $events = array_merge($events, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Nouveaux compétiteurs inscrits (dernière heure)
    $stmt = $pdo->prepare("
        SELECT 'competitor_registered' as type, id, (nom || ' ' || prenom) as name, date_inscription as timestamp
        FROM competiteurs
        WHERE date_inscription > datetime('now', '-1 hour')
        ORDER BY date_inscription DESC
    ");
    $stmt->execute();
    $events = array_merge($events, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Compétitions validées récemment
    $stmt = $pdo->prepare("
        SELECT 'competition_validated' as type, id, nom_competition as name, date_creation as timestamp
        FROM competitions
        WHERE date_creation > datetime('now', '-1 hour')
        ORDER BY date_creation DESC
    ");
    $stmt->execute();
    $events = array_merge($events, $stmt->fetchAll(PDO::FETCH_ASSOC));

    return $events;
}

function shouldNotify($event) {
    // Logique pour déterminer si un événement doit déclencher une notification
    // Pour l'instant, notifier tous les événements récents
    return true;
}

function getNotificationType($event) {
    switch ($event['type']) {
        case 'club_registered':
            return 'success';
        case 'competitor_registered':
            return 'info';
        case 'competition_validated':
            return 'warning';
        default:
            return 'info';
    }
}

function getNotificationMessage($event) {
    switch ($event['type']) {
        case 'club_registered':
            return "Nouveau club inscrit : <strong>{$event['name']}</strong>";
        case 'competitor_registered':
            return "Nouveau compétiteur : <strong>{$event['name']}</strong>";
        case 'competition_validated':
            return "Nouvelle compétition : <strong>{$event['name']}</strong>";
        default:
            return "Nouvel événement système";
    }
}

function markNotificationRead($pdo) {
    $notificationId = $_POST['notification_id'] ?? '';

    if (!$notificationId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de notification requis']);
        return;
    }

    try {
        // Ici on pourrait marquer la notification comme lue dans une table dédiée
        // Pour l'instant, on simule juste une réponse positive
        echo json_encode([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function sendTestNotification($pdo) {
    // Fonction de test pour envoyer une notification manuellement
    $testNotifications = [
        [
            'id' => 'test_' . time(),
            'type' => 'success',
            'message' => 'Test de notification temps réel réussi !',
            'timestamp' => date('Y-m-d H:i:s'),
            'persistent' => true
        ]
    ];

    echo json_encode([
        'success' => true,
        'notifications' => $testNotifications,
        'count' => 1
    ]);
}
?>