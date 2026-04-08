<?php
/**
 * Administration des Compétitions IWUF
 * Interface pour gérer routines, jugements, scoring et arbitrage
 */

session_start();

// Vérifier l'authentification
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'iwuf_manager.php';
require_once 'iwuf_scoring.php';

$iwuf = new IWUFManager();
$pdo = new PDO("sqlite:wushuclubci.db");

// Déterminer l'action à effectuer
$action = isset($_GET['action']) ? $_GET['action'] : 'competitions';
$competition_id = isset($_GET['competition_id']) ? (int)$_GET['competition_id'] : null;
$routine_id = isset($_GET['routine_id']) ? (int)$_GET['routine_id'] : null;

// Variables pour le rendu
$page_title = '';
$content = '';

// ============================================================================
// GESTION DES COMPÉTITIONS IWUF
// ============================================================================

switch ($action) {
    case 'competitions':
        $page_title = "🏆 Compétitions IWUF";
        
        // Récupérer toutes les compétitions
        $stmt = $pdo->prepare("SELECT * FROM competitions ORDER BY date_debut DESC");
        $stmt->execute();
        $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="iwuf-section">
            <h2><?php echo $page_title; ?></h2>
            
            <a href="?action=new_competition" class="btn btn-primary">➕ Nouvelle Compétition</a>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Dates</th>
                        <th>Lieu</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competitions as $comp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($comp['nom']); ?></td>
                            <td><?php echo $comp['date_debut'] . ' → ' . $comp['date_fin']; ?></td>
                            <td><?php echo htmlspecialchars($comp['lieu']); ?></td>
                            <td><span class="badge"><?php echo $comp['statut']; ?></span></td>
                            <td>
                                <a href="?action=view_competition&competition_id=<?php echo $comp['id']; ?>" class="btn btn-sm">📋 Détails</a>
                                <a href="?action=edit_competition&competition_id=<?php echo $comp['id']; ?>" class="btn btn-sm">✏️ Éditer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        break;

    case 'view_competition':
        if (!$competition_id) {
            echo "❌ Compétition non trouvée";
            break;
        }

        // Récupérer les infos de la compétition
        $stmt = $pdo->prepare("SELECT * FROM competitions WHERE id = ?");
        $stmt->execute([$competition_id]);
        $competition = $stmt->fetch(PDO::FETCH_ASSOC);

        $page_title = "🏆 " . htmlspecialchars($competition['nom']);
        ?>
        <div class="iwuf-section">
            <h2><?php echo $page_title; ?></h2>

            <div class="tabs">
                <button onclick="switchTab('details')" class="tab-btn active">📋 Détails</button>
                <button onclick="switchTab('routines')" class="tab-btn">🤸 Routines</button>
                <button onclick="switchTab('jugements')" class="tab-btn">🏅 Jugements</button>
                <button onclick="switchTab('resultats')" class="tab-btn">🎖️ Résultats</button>
                <button onclick="switchTab('arbitres')" class="tab-btn">👨‍⚖️ Arbitres</button>
                <button onclick="switchTab('appels')" class="tab-btn">📢 Appels</button>
            </div>

            <!-- Tab: Détails -->
            <div id="details" class="tab-content active">
                <div class="info-box">
                    <p><strong>Nom:</strong> <?php echo htmlspecialchars($competition['nom']); ?></p>
                    <p><strong>Dates:</strong> <?php echo $competition['date_debut'] . ' → ' . $competition['date_fin']; ?></p>
                    <p><strong>Lieu:</strong> <?php echo htmlspecialchars($competition['lieu']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($competition['type']); ?></p>
                    <p><strong>Statut:</strong> <span class="badge"><?php echo $competition['statut']; ?></span></p>
                    <textarea readonly style="width: 100%; height: 100px; margin-top: 10px;"><?php echo htmlspecialchars($competition['description']); ?></textarea>
                </div>
            </div>

            <!-- Tab: Routines -->
            <div id="routines" class="tab-content">
                <a href="?action=new_routine&competition_id=<?php echo $competition_id; ?>" class="btn btn-primary">➕ Ajouter une Routine</a>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Compétiteur</th>
                            <th>Style</th>
                            <th>Arme</th>
                            <th>Type</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $routines = $iwuf->getRoutinesCompetition($competition_id);
                        foreach ($routines as $routine):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($routine['nom'] . ' ' . $routine['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($routine['nom_style']); ?></td>
                                <td><?php echo htmlspecialchars($routine['nom_arme'] ?? '-'); ?></td>
                                <td><span class="badge"><?php echo $routine['type_routine']; ?></span></td>
                                <td><?php echo round($routine['duree_prevue'] / 60, 2) . ' min'; ?></td>
                                <td><?php echo $routine['statut']; ?></td>
                                <td>
                                    <a href="?action=view_routine&routine_id=<?php echo $routine['id']; ?>&competition_id=<?php echo $competition_id; ?>" class="btn btn-sm">👁️ Voir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tab: Jugements -->
            <div id="jugements" class="tab-content">
                <p>Interface de jugement en développement...</p>
            </div>

            <!-- Tab: Résultats -->
            <div id="resultats" class="tab-content">
                <?php
                $categories = $iwuf->getCategoriesIWUF();
                ?>
                <div class="results-container">
                    <?php foreach ($categories as $categorie): ?>
                        <div class="category-results">
                            <h4><?php echo htmlspecialchars($categorie['nom_categorie']); ?> (<?php echo $categorie['age_min']; ?>-<?php echo $categorie['age_max']; ?> ans)</h4>
                            
                            <?php
                            $resultats = $iwuf->getResultatsCompetition($competition_id, $categorie['id']);
                            ?>
                            
                            <table class="table-small">
                                <thead>
                                    <tr>
                                        <th>Rang</th>
                                        <th>Compétiteur</th>
                                        <th>Style</th>
                                        <th>Score</th>
                                        <th>Médaille</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultats as $resultat): ?>
                                        <tr>
                                            <td><?php echo $resultat['rang']; ?></td>
                                            <td><?php echo htmlspecialchars($resultat['nom'] . ' ' . $resultat['prenom']); ?></td>
                                            <td><?php echo htmlspecialchars($resultat['nom_style']); ?></td>
                                            <td><?php echo round($resultat['score_total'], 2); ?></td>
                                            <td>
                                                <?php 
                                                if ($resultat['medaille']) {
                                                    echo "🥇 🥈 🥉"[['Or' => 0, 'Argent' => 1, 'Bronze' => 2][$resultat['medaille']] ?? 3];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button onclick="location.href='?action=attribuer_medailles&competition_id=<?php echo $competition_id; ?>'" class="btn btn-success">🏅 Attribuer les Médailles</button>
            </div>

            <!-- Tab: Arbitres -->
            <div id="arbitres" class="tab-content">
                <a href="?action=assign_arbitre&competition_id=<?php echo $competition_id; ?>" class="btn btn-primary">➕ Affecter un Arbitre</a>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Arbitre</th>
                            <th>Niveau</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $arbitres = $iwuf->getArbitresCompetition($competition_id);
                        foreach ($arbitres as $arbitre):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($arbitre['nom'] . ' ' . $arbitre['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($arbitre['niveau_certification']); ?></td>
                                <td><?php echo htmlspecialchars($arbitre['type_arbitrage']); ?></td>
                                <td><?php echo substr($arbitre['date_affectation'], 0, 10); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger">✕ Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tab: Appels -->
            <div id="appels" class="tab-content">
                <h4>📢 Appels en Attente</h4>
                <?php 
                $appels = $iwuf->getAppelsEnAttente($competition_id);
                ?>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Compétiteur</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appels as $appel): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appel['nom'] . ' ' . $appel['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($appel['type_appel']); ?></td>
                                <td><?php echo htmlspecialchars($appel['description']); ?></td>
                                <td><span class="badge"><?php echo $appel['statut']; ?></span></td>
                                <td>
                                    <button onclick="deciderAppel(<?php echo $appel['id']; ?>, 'accepte')" class="btn btn-sm btn-success">✓ Accepter</button>
                                    <button onclick="deciderAppel(<?php echo $appel['id']; ?>, 'rejete')" class="btn btn-sm btn-danger">✕ Rejeter</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function switchTab(tabName) {
                const contents = document.querySelectorAll('.tab-content');
                contents.forEach(c => c.classList.remove('active'));
                document.getElementById(tabName).classList.add('active');

                const buttons = document.querySelectorAll('.tab-btn');
                buttons.forEach(b => b.classList.remove('active'));
                event.target.classList.add('active');
            }

            function deciderAppel(appealId, decision) {
                if (confirm('Êtes-vous sûr de cette décision ?')) {
                    // TODO: Implémente l'appel AJAX
                    alert('Appel ' + appealId + ': ' + decision);
                }
            }
        </script>
        <?php
        break;

    case 'new_competition':
        $page_title = "➕ Nouvelle Compétition IWUF";
        ?>
        <div class="iwuf-section">
            <h2><?php echo $page_title; ?></h2>
            
            <form method="POST" action="handle_iwuf.php?action=save_competition">
                <div class="form-group">
                    <label>Nom de la Compétition:</label>
                    <input type="text" name="nom" required>
                </div>

                <div class="form-group">
                    <label>Date de Début:</label>
                    <input type="date" name="date_debut" required>
                </div>

                <div class="form-group">
                    <label>Date de Fin:</label>
                    <input type="date" name="date_fin" required>
                </div>

                <div class="form-group">
                    <label>Lieu:</label>
                    <input type="text" name="lieu" required>
                </div>

                <div class="form-group">
                    <label>Type:</label>
                    <select name="type">
                        <option>Championnat</option>
                        <option>Open</option>
                        <option>Challenge</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="5"></textarea>
                </div>

                <button type="submit" class="btn btn-success">💾 Créer</button>
                <a href="?action=competitions" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
        <?php
        break;

    case 'attribuer_medailles':
        if ($competition_id) {
            $iwuf->attribuerMedailles($competition_id);
            echo "<div class='alert alert-success'>✅ Médailles attribuées avec succès !</div>";
            echo "<a href='?action=view_competition&competition_id=$competition_id' class='btn'>Retour</a>";
        }
        break;

    default:
        echo "<p>Action non reconnue</p>";
}

?>

<style>
.iwuf-section {
    padding: 20px;
    background: #f5f5f5;
    border-radius: 8px;
    margin: 20px 0;
}

.tabs {
    display: flex;
    gap: 10px;
    margin: 20px 0;
    border-bottom: 2px solid #ddd;
}

.tab-btn {
    padding: 10px 15px;
    background: #eee;
    border: none;
    cursor: pointer;
    border-radius: 4px 4px 0 0;
    transition: all 0.3s;
}

.tab-btn.active {
    background: #007bff;
    color: white;
}

.tab-content {
    display: none;
    padding: 20px 0;
}

.tab-content.active {
    display: block;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background: white;
}

.table th, .table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background: #f0f0f0;
    font-weight: bold;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    background: #007bff;
    color: white;
    border-radius: 4px;
    font-size: 0.85em;
}

.btn {
    display: inline-block;
    padding: 8px 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    margin: 5px;
}

.btn-primary { background: #007bff; }
.btn-success { background: #28a745; }
.btn-danger { background: #dc3545; }
.btn-secondary { background: #6c757d; }
.btn-sm { padding: 4px 8px; font-size: 0.85em; }

.form-group {
    margin: 15px 0;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1em;
}

.info-box {
    background: white;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #007bff;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin: 10px 0;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.results-container {
    display: grid;
    gap: 20px;
}

.category-results {
    background: white;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #28a745;
}

.table-small {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9em;
}

.table-small th, .table-small td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}

.table-small th {
    background: #f0f0f0;
}
</style>
