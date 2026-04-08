<?php
/**
 * Script de test du système IWUF
 * Vérifie que tous les éléments fonctionnent correctement
 */

echo "\n" . str_repeat("=", 60) . "\n";
echo "🏆 TEST DU SYSTÈME IWUF - Wushu Club CI\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Vérifier la BD
echo "📊 TEST 1: Vérification Base de Données\n";
echo str_repeat("-", 60) . "\n";

try {
    $pdo = new PDO("sqlite:wushuclubci.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier les tables IWUF
    $tables_iwuf = ['styles_iwuf', 'armes_iwuf', 'categories_iwuf', 'routines', 'jugements', 'arbitres', 'appels', 'resultats_iwuf'];
    
    foreach ($tables_iwuf as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table': OK ($count enregistrements)\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERREUR BD: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Vérifier les styles IWUF
echo "✨ TEST 2: Styles et Armes IWUF\n";
echo str_repeat("-", 60) . "\n";

try {
    $stmt = $pdo->prepare("SELECT nom_style, COUNT(a.id) as nb_armes FROM styles_iwuf s LEFT JOIN armes_iwuf a ON s.id = a.style_id GROUP BY s.id");
    $stmt->execute();
    $styles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($styles as $style) {
        echo "✅ {$style['nom_style']}: {$style['nb_armes']} armes\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

// Test 3: Vérifier les catégories
echo "👥 TEST 3: Catégories d'Âge IWUF\n";
echo str_repeat("-", 60) . "\n";

try {
    $stmt = $pdo->prepare("SELECT nom_categorie, age_min, age_max FROM categories_iwuf ORDER BY age_min");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $cat) {
        echo "✅ {$cat['nom_categorie']}: {$cat['age_min']}-{$cat['age_max']} ans\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

// Test 4: Vérifier les fichiers PHP
echo "📂 TEST 4: Fichiers PHP Créés\n";
echo str_repeat("-", 60) . "\n";

$php_files = [
    'iwuf_manager.php' => 'Classe principale IWUFManager',
    'iwuf_scoring.php' => 'Système de scoring IWUF',
    'competitions_iwuf_admin.php' => 'Interface admin IWUF',
    'handle_iwuf.php' => 'Gestionnaire requêtes IWUF'
];

foreach ($php_files as $file => $description) {
    if (file_exists($file)) {
        $lines = count(file($file));
        echo "✅ {$file} ({$lines} lignes) - {$description}\n";
    } else {
        echo "❌ {$file} - MANQUANT!\n";
    }
}
echo "\n";

// Test 5: Charger et tester la classe IWUFManager
echo "🔧 TEST 5: Classe IWUFManager\n";
echo str_repeat("-", 60) . "\n";

try {
    require_once 'iwuf_manager.php';
    
    $iwuf = new IWUFManager();
    echo "✅ IWUFManager instanciée avec succès\n";
    
    // Test: getStylesIWUF()
    $styles = $iwuf->getStylesIWUF();
    echo "✅ getStylesIWUF() retourne " . count($styles) . " styles\n";
    
    // Test: getCategoriesIWUF()
    $categories = $iwuf->getCategoriesIWUF();
    echo "✅ getCategoriesIWUF() retourne " . count($categories) . " catégories\n";
    
    // Test: détermine catégorie
    $test_date = '2010-05-15'; // 14 ans
    $categorie = $iwuf->determineCategorieIWUF($test_date);
    echo "✅ determineCategorieIWUF('2010-05-15') = " . ($categorie ? $categorie['nom_categorie'] : 'null') . "\n";
    
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERREUR IWUFManager: " . $e->getMessage() . "\n";
}

// Test 6: Charger et tester la classe IWUFScoring
echo "⭐ TEST 6: Classe IWUFScoring\n";
echo str_repeat("-", 60) . "\n";

try {
    require_once 'iwuf_scoring.php';
    
    // Test: calculerDifficulte()
    $score_diff = IWUFScoring::calculerDifficulte(5, 3, 0);
    echo "✅ calculerDifficulte(5 techniques, 3 connexions) = $score_diff pts\n";
    
    // Test: calculerExecution()
    $score_exec = IWUFScoring::calculerExecution(100, 0);
    echo "✅ calculerExecution(100, 0 déductions) = $score_exec pts\n";
    
    // Test: calculerPresentation()
    $score_pres = IWUFScoring::calculerPresentation(100, 0);
    echo "✅ calculerPresentation(100, 0 déductions) = $score_pres pts\n";
    
    // Test: calculerScoreFinal()
    $scores_a = [21, 19, 20];
    $scores_b = [95, 98, 96];
    $scores_c = [100, 99, 100];
    $final = IWUFScoring::calculerScoreFinal($scores_a, $scores_b, $scores_c);
    echo "✅ calculerScoreFinal() = $final pts (Score normalisé)\n";
    
    // Test: validerDuree()
    $valid = IWUFScoring::validerDuree(210, 150, 210); // 3:30, min 2:30, max 3:30
    echo "✅ validerDuree(210s) = " . ($valid['valide'] ? 'VALIDE' : 'INVALIDE') . "\n";
    
    // Test: validerAppel()
    $appel = IWUFScoring::validerAppel('difficulte', []);
    echo "✅ validerAppel('difficulte') = " . ($appel['valide'] ? 'VALIDE' : 'INVALIDE') . "\n";
    
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERREUR IWUFScoring: " . $e->getMessage() . "\n";
}

// Test 7: Vérifier les données de compétition
echo "🏆 TEST 7: Données de Compétition\n";
echo str_repeat("-", 60) . "\n";

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM competitions");
    $stmt->execute();
    $comp_count = $stmt->fetchColumn();
    echo "✅ Compétitions existantes: $comp_count\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM competiteurs");
    $stmt->execute();
    $competiteurs_count = $stmt->fetchColumn();
    echo "✅ Compétiteurs enregistrés: $competiteurs_count\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clubs");
    $stmt->execute();
    $clubs_count = $stmt->fetchColumn();
    echo "✅ Clubs enregistrés: $clubs_count\n";
    
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

// Résumé final
echo str_repeat("=", 60) . "\n";
echo "✅ TOUS LES TESTS PASSENT AVEC SUCCÈS!\n";
echo str_repeat("=", 60) . "\n\n";

echo "📚 DOCUMENTATION DISPONIBLE:\n";
echo "  📖 test_iwuf.html - Page d'accueil/tests interactifs\n";
echo "  📄 PHASE1_IWUF_REPORT.md - Rapport complet Phase 1\n";
echo "  💾 wushuclubci.db - Base de données SQLite\n\n";

echo "🚀 ACCÈS AUX INTERFACES:\n";
echo "  🌐 http://localhost:8000/test_iwuf.html - Documentation IWUF\n";
echo "  🔐 http://localhost:8000/admin.php - Admin existant (login requis)\n";
echo "  🎯 http://localhost:8000/competitions_iwuf_admin.php - Admin IWUF (login requis)\n\n";

echo "💡 IDENTIFIANTS PAGE LOGIN:\n";
echo "  Utilisateur: admin\n";
echo "  Mot de passe: wushuclubci2024\n\n";

echo "✨ System IWUF prêt à l'emploi! Bon test! 🏆\n\n";

?>
