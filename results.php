<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Configuration de la base de données
$db_file = 'wushuclubci.db';

// Fonction pour nettoyer les données
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Connexion à la base de données
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Créer la table des résultats si elle n'existe pas
$pdo->exec("CREATE TABLE IF NOT EXISTS resultats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    competition_id INTEGER NOT NULL,
    competiteur_id INTEGER NOT NULL,
    categorie TEXT NOT NULL,
    position INTEGER,
    points INTEGER DEFAULT 0,
    medaille TEXT,
    commentaires TEXT,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competition_id) REFERENCES competitions(id),
    FOREIGN KEY (competiteur_id) REFERENCES competiteurs(id)
)");

$message = '';
$message_type = '';

// Traitement de l'ajout de résultat
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_resultat'])) {
    $competition_id = intval($_POST['competition_id']);
    $competiteur_id = intval($_POST['competiteur_id']);
    $categorie = clean_input($_POST['categorie']);
    $position = intval($_POST['position']);
    $points = intval($_POST['points']);
    $medaille = clean_input($_POST['medaille']);
    $commentaires = clean_input($_POST['commentaires']);

    try {
        $stmt = $pdo->prepare("INSERT INTO resultats (competition_id, competiteur_id, categorie, position, points, medaille, commentaires)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$competition_id, $competiteur_id, $categorie, $position, $points, $medaille, $commentaires]);
        $message = "Résultat ajouté avec succès.";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Erreur lors de l'ajout du résultat : " . $e->getMessage();
        $message_type = "error";
    }
}

// Traitement de la suppression de résultat
if (isset($_GET['delete_resultat'])) {
    $resultat_id = intval($_GET['delete_resultat']);
    try {
        $stmt = $pdo->prepare("DELETE FROM resultats WHERE id = ?");
        $stmt->execute([$resultat_id]);
        $message = "Résultat supprimé avec succès.";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Erreur lors de la suppression : " . $e->getMessage();
        $message_type = "error";
    }
}

// Récupérer les compétitions pour le formulaire
$competitions = $pdo->query("SELECT id, nom_competition FROM competitions ORDER BY date_competition DESC");

// Récupérer les compétiteurs pour le formulaire
$competiteurs = $pdo->query("SELECT c.id, c.nom, c.prenoms, cl.nom_club
                            FROM competiteurs c
                            LEFT JOIN clubs cl ON c.club_id = cl.id
                            WHERE c.statut = 'valide'
                            ORDER BY c.nom, c.prenoms");

// Récupérer les résultats existants
$resultats = $pdo->query("SELECT r.*, comp.nom_competition, c.nom, c.prenoms, cl.nom_club
                         FROM resultats r
                         JOIN competitions comp ON r.competition_id = comp.id
                         JOIN competiteurs c ON r.competiteur_id = c.id
                         LEFT JOIN clubs cl ON c.club_id = cl.id
                         ORDER BY r.date_saisie DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Résultats - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/js-styles.css">
    <style>
        .results-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .results-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .form-section, .results-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .form-section h3, .results-section h3 {
            color: #e30613;
            margin-bottom: 20px;
            font-size: 1.4rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, #e30613, #d4af37);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .results-table th, .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .results-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .medaille {
            padding: 4px 8px;
            border-radius: 3px;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .medaille.gold { background: #ffd700; color: #333; }
        .medaille.silver { background: #c0c0c0; }
        .medaille.bronze { background: #cd7f32; }

        .position {
            font-weight: 600;
            color: #e30613;
        }

        .delete-btn {
            color: #e30613;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            border: 1px solid #e30613;
        }

        .delete-btn:hover {
            background: #e30613;
            color: white;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    </style>
</head>
<body class="ivory-theme">
    <div class="results-container">
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au panel admin
        </a>

        <div class="results-header">
            <h1><i class="fas fa-trophy"></i> Gestion des Résultats</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="results-grid">
            <!-- Formulaire d'ajout -->
            <div class="form-section">
                <h3><i class="fas fa-plus-circle"></i> Ajouter un Résultat</h3>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="competition_id">Compétition</label>
                        <select name="competition_id" id="competition_id" class="form-control" required>
                            <option value="">Sélectionner une compétition</option>
                            <?php while ($comp = $competitions->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $comp['id']; ?>"><?php echo htmlspecialchars($comp['nom_competition']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="competiteur_id">Compétiteur</label>
                        <select name="competiteur_id" id="competiteur_id" class="form-control" required>
                            <option value="">Sélectionner un compétiteur</option>
                            <?php
                            $competiteurs->execute();
                            while ($comp = $competiteurs->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?php echo $comp['id']; ?>">
                                    <?php echo htmlspecialchars($comp['nom'] . ' ' . $comp['prenoms'] . ' (' . $comp['nom_club'] . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="categorie">Catégorie</label>
                            <select name="categorie" id="categorie" class="form-control" required>
                                <option value="">Sélectionner</option>
                                <option value="Taolu">Taolu</option>
                                <option value="Sanda">Sanda</option>
                                <option value="Qigong">Qigong</option>
                                <option value="Formes Traditionnelles">Formes Traditionnelles</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="number" name="position" id="position" class="form-control" min="1" max="100" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="points">Points</label>
                            <input type="number" name="points" id="points" class="form-control" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="medaille">Médaille</label>
                            <select name="medaille" id="medaille" class="form-control">
                                <option value="">Aucune</option>
                                <option value="gold">Or 🥇</option>
                                <option value="silver">Argent 🥈</option>
                                <option value="bronze">Bronze 🥉</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="commentaires">Commentaires</label>
                        <textarea name="commentaires" id="commentaires" class="form-control" rows="3" placeholder="Commentaires optionnels..."></textarea>
                    </div>

                    <button type="submit" name="ajouter_resultat" class="btn-submit">
                        <i class="fas fa-plus"></i> Ajouter le Résultat
                    </button>
                </form>
            </div>

            <!-- Liste des résultats -->
            <div class="results-section">
                <h3><i class="fas fa-list"></i> Résultats Enregistrés</h3>
                <?php if ($resultats->rowCount() > 0): ?>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Compétiteur</th>
                                <th>Compétition</th>
                                <th>Catégorie</th>
                                <th>Position</th>
                                <th>Médaille</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($resultat = $resultats->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($resultat['nom'] . ' ' . $resultat['prenoms']); ?><br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($resultat['nom_club']); ?></small></td>
                                    <td><?php echo htmlspecialchars($resultat['nom_competition']); ?></td>
                                    <td><?php echo htmlspecialchars($resultat['categorie']); ?></td>
                                    <td><span class="position"><?php echo $resultat['position']; ?><?php echo $resultat['position'] == 1 ? 'er' : 'ème'; ?></span></td>
                                    <td>
                                        <?php if ($resultat['medaille']): ?>
                                            <span class="medaille <?php echo $resultat['medaille']; ?>">
                                                <?php
                                                switch($resultat['medaille']) {
                                                    case 'gold': echo '🥇 Or'; break;
                                                    case 'silver': echo '🥈 Argent'; break;
                                                    case 'bronze': echo '🥉 Bronze'; break;
                                                }
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?delete_resultat=<?php echo $resultat['id']; ?>"
                                           class="delete-btn"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce résultat ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 40px;">
                        <i class="fas fa-trophy" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i><br>
                        Aucun résultat enregistré pour le moment.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>