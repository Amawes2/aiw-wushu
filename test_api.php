<?php
// test_api.php - Test simple de l'API
echo "Test de l'API des notifications\n\n";

try {
    require_once 'functions.php';

    $pdo = new PDO("sqlite:wushuclubci.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connexion à la base de données réussie\n";

    // Tester la fonction getGeneralStats
    $stats = getGeneralStats($pdo);
    echo "✅ Fonction getGeneralStats fonctionne\n";
    echo "Stats: " . json_encode($stats) . "\n\n";

    // Tester l'API directement
    require_once 'api/notifications.php';

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>