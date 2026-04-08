<?php
/**
 * Handle IWUF Requests - Traitement des formulaires et actions IWUF
 * POST/GET processing pour l'administration des compétitions IWUF
 */

session_start();

// Vérifier l'authentification
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    die('Non autorisé');
}

require_once 'iwuf_manager.php';
require_once 'iwuf_scoring.php';

$iwuf = new IWUFManager();
$pdo = new PDO("sqlite:wushuclubci.db");

$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        // ====================================================================
        // GESTION DES COMPÉTITIONS
        // ====================================================================

        case 'save_competition':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $nom = $_POST['nom'] ?? '';
            $date_debut = $_POST['date_debut'] ?? '';
            $date_fin = $_POST['date_fin'] ?? '';
            $lieu = $_POST['lieu'] ?? '';
            $type = $_POST['type'] ?? 'championnat';
            $description = $_POST['description'] ?? '';

            $stmt = $pdo->prepare("
                INSERT INTO competitions (nom, date_debut, date_fin, lieu, type, description, statut)
                VALUES (?, ?, ?, ?, ?, ?, 'planifiee')
            ");

            if ($stmt->execute([$nom, $date_debut, $date_fin, $lieu, $type, $description])) {
                $competition_id = $pdo->lastInsertId();
                header("Location: competitions_iwuf_admin.php?action=view_competition&competition_id=$competition_id");
                exit;
            }
            break;

        // ====================================================================
        // GESTION DES ROUTINES
        // ====================================================================

        case 'create_routine':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $competiteur_id = $_POST['competiteur_id'] ?? null;
            $competition_id = $_POST['competition_id'] ?? null;
            $style_iwuf_id = $_POST['style_iwuf_id'] ?? null;
            $arme_id = $_POST['arme_id'] ?? null;
            $type_routine = $_POST['type_routine'] ?? 'libre';

            if (!$competiteur_id || !$competition_id || !$style_iwuf_id) {
                throw new Exception('Données manquantes');
            }

            $routine_id = $iwuf->creerRoutine(
                $competiteur_id,
                $competition_id,
                $style_iwuf_id,
                $arme_id,
                $type_routine
            );

            if ($routine_id) {
                $response = [
                    'success' => true,
                    'message' => 'Routine créée avec succès',
                    'routine_id' => $routine_id
                ];
            }
            break;

        // ====================================================================
        // GESTION DES JUGEMENTS
        // ====================================================================

        case 'save_jugement':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $routine_id = $_POST['routine_id'] ?? null;
            $arbitre_id = $_POST['arbitre_id'] ?? null;
            $juge_numero = $_POST['juge_numero'] ?? 1;

            $scores = [
                'difficulte_technique' => (float)($_POST['difficulte_technique'] ?? 0),
                'difficulte_connexions' => (float)($_POST['difficulte_connexions'] ?? 0),
                'execution' => (float)($_POST['execution'] ?? 0),
                'presentation' => (float)($_POST['presentation'] ?? 0)
            ];

            // Créer le jugement
            $jugement_id = $iwuf->creerJugement($routine_id, $arbitre_id, $juge_numero);

            if ($jugement_id) {
                // Mettre à jour les scores
                $iwuf->mettreAJourScores($jugement_id, $scores);

                $response = [
                    'success' => true,
                    'message' => 'Jugement enregistré',
                    'jugement_id' => $jugement_id
                ];
            }
            break;

        case 'update_scores':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $jugement_id = $_POST['jugement_id'] ?? null;
            $scores = [
                'difficulte_technique' => (float)($_POST['difficulte_technique'] ?? 0),
                'difficulte_connexions' => (float)($_POST['difficulte_connexions'] ?? 0),
                'execution' => (float)($_POST['execution'] ?? 0),
                'presentation' => (float)($_POST['presentation'] ?? 0)
            ];

            if ($iwuf->mettreAJourScores($jugement_id, $scores)) {
                $response = [
                    'success' => true,
                    'message' => 'Scores mis à jour'
                ];
            } else {
                throw new Exception('Erreur lors de la mise à jour');
            }
            break;

        // ====================================================================
        // GESTION DES APPELS
        // ====================================================================

        case 'submit_appeal':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $competition_id = $_POST['competition_id'] ?? null;
            $routine_id = $_POST['routine_id'] ?? null;
            $equipe_id = $_POST['equipe_id'] ?? null;
            $type_appel = $_POST['type_appel'] ?? null;
            $description = $_POST['description'] ?? '';

            // Valider l'appel
            $validation = IWUFScoring::validerAppel($type_appel, []);

            if (!$validation['valide']) {
                throw new Exception('Type d\'appel invalide');
            }

            $result = $iwuf->soumettreAppel(
                $competition_id,
                $routine_id,
                $equipe_id,
                $type_appel,
                $description
            );

            $response = $result;
            break;

        case 'decide_appeal':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $appel_id = $_POST['appel_id'] ?? null;
            $decision = $_POST['decision'] ?? null; // 'accepte' ou 'rejete'
            $montant_rembourse = $_POST['montant_rembourse'] ?? 0;

            if (!in_array($decision, ['accepte', 'rejete'])) {
                throw new Exception('Décision invalide');
            }

            if ($iwuf->deciderAppel($appel_id, $decision, $montant_rembourse)) {
                $response = [
                    'success' => true,
                    'message' => 'Appel traité: ' . $decision
                ];
            }
            break;

        // ====================================================================
        // GESTION DES ARBITRES
        // ====================================================================

        case 'assign_arbitre':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $arbitre_id = $_POST['arbitre_id'] ?? null;
            $competition_id = $_POST['competition_id'] ?? null;
            $type_arbitrage = $_POST['type_arbitrage'] ?? 'juge_technique';

            if ($iwuf->affecterArbitreCompetition($arbitre_id, $competition_id, $type_arbitrage)) {
                $response = [
                    'success' => true,
                    'message' => 'Arbitre affecté à la compétition'
                ];
            }
            break;

        // ====================================================================
        // GESTION DES RÉSULTATS
        // ====================================================================

        case 'finalize_routine':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $routine_id = $_POST['routine_id'] ?? null;
            $competition_id = $_POST['competition_id'] ?? null;
            $competiteur_id = $_POST['competiteur_id'] ?? null;

            // Calculer les scores moyens
            $scores_moyens = $iwuf->calculerScoreMoyenRoutine($routine_id);

            if ($iwuf->enregistrerResultat($routine_id, $competiteur_id, $competition_id, $scores_moyens)) {
                $response = [
                    'success' => true,
                    'message' => 'Routine finalisée et résultats enregistrés'
                ];
            }
            break;

        case 'attribuer_medailles':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $competition_id = $_POST['competition_id'] ?? null;
            $categorie_id = $_POST['categorie_id'] ?? null;

            if ($iwuf->attribuerMedailles($competition_id, $categorie_id)) {
                $response = [
                    'success' => true,
                    'message' => 'Médailles attribuées'
                ];
            }
            break;

        // ====================================================================
        // RAPPORT DE SCORING
        // ====================================================================

        case 'rapport_scoring':
            $routine_id = $_GET['routine_id'] ?? null;

            if (!$routine_id) {
                throw new Exception('Routine non trouvée');
            }

            // Récupérer les données de la routine
            $stmt = $pdo->prepare("
                SELECT r.*, c.nom, c.prenom, s.nom_style
                FROM routines r
                LEFT JOIN competiteurs c ON r.competiteur_id = c.id
                LEFT JOIN styles_iwuf s ON r.style_iwuf_id = s.id
                WHERE r.id = ?
            ");
            $stmt->execute([$routine_id]);
            $routine = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$routine) {
                throw new Exception('Routine non trouvée');
            }

            // Récupérer tous les jugements
            $jugements = $iwuf->getJugementsRoutine($routine_id);

            // Générer le rapport
            $rapport = IWUFScoring::genererRapportScoring($routine, $jugements);

            $response = [
                'success' => true,
                'rapport' => $rapport
            ];
            break;

        // ====================================================================
        // VALIDATION D'ARME
        // ====================================================================

        case 'valider_arme':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            $type_arme = $_POST['type_arme'] ?? null;
            $specifications = [
                'longueur_min_cm' => $_POST['longueur_cm'] ?? 0,
                'ruban_min_cm' => $_POST['ruban_cm'] ?? 0,
                'houppe_min_cm' => $_POST['houppe_cm'] ?? 0
            ];

            $validation = IWUFScoring::validerArme($type_arme, $specifications);
            $response = $validation;
            break;

        default:
            throw new Exception('Action non reconnue: ' . $action);
    }
} catch (Exception $e) {
    error_log('IWUF Error: ' . $e->getMessage());
    $response = [
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ];
}

// Retourner la réponse
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>
