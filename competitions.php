<?php
// competitions.php - Gestion des compétitions pour Wushu Club CI

// Configuration de la base de données
$db_file = 'wushuclubci.db';

// Créer la base de données et les tables si elles n'existent pas
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Table des compétitions
    $sql_competitions = "CREATE TABLE IF NOT EXISTS competitions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        type TEXT DEFAULT 'championnat',
        date_debut DATE NOT NULL,
        date_fin DATE NOT NULL,
        lieu TEXT NOT NULL,
        description TEXT,
        statut TEXT DEFAULT 'planifiee',
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_competitions);

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Fonction pour nettoyer les données
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Traitement du formulaire d'ajout de compétition
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_competition'])) {
    $nom = clean_input($_POST['nom']);
    $date_debut = clean_input($_POST['date_debut']);
    $date_fin = clean_input($_POST['date_fin']);
    $lieu = clean_input($_POST['lieu']);
    $description = clean_input($_POST['description']);

    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom de la compétition est requis.";
    if (empty($date_debut)) $errors[] = "La date de début est requise.";
    if (empty($date_fin)) $errors[] = "La date de fin est requise.";
    if (empty($lieu)) $errors[] = "Le lieu est requis.";

    // Vérifier que la date de fin n'est pas avant la date de début
    if ($date_debut && $date_fin && strtotime($date_fin) < strtotime($date_debut)) {
        $errors[] = "La date de fin ne peut pas être antérieure à la date de début.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO competitions (nom, date_debut, date_fin, lieu, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $date_debut, $date_fin, $lieu, $description]);
            $message = "<div class='alert alert-success'>Compétition ajoutée avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Erreur lors de l'ajout : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>" . implode("<br>", $errors) . "</div>";
    }
}

// Récupérer toutes les compétitions
try {
    $stmt = $pdo->query("SELECT * FROM competitions ORDER BY date_debut DESC");
    $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $competitions = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétitions Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .competitions-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
        }

        .section-title {
            color: #e30613;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5em;
        }

        .add-competition-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #e30613;
            outline: none;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: #e30613;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #b3050f;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
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

        .competitions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .competition-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .competition-card:hover {
            transform: translateY(-5px);
        }

        .competition-header {
            background: linear-gradient(135deg, #e30613, #b3050f);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .competition-title {
            font-size: 1.3em;
            margin-bottom: 10px;
        }

        .competition-dates {
            font-size: 0.9em;
            opacity: 0.9;
        }

        .competition-body {
            padding: 20px;
        }

        .competition-info {
            margin-bottom: 15px;
        }

        .competition-info i {
            color: #e30613;
            margin-right: 8px;
            width: 16px;
        }

        .competition-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-planifiee {
            background: #fff3cd;
            color: #856404;
        }

        .status-en_cours {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-terminee {
            background: #d4edda;
            color: #155724;
        }

        .status-annulee {
            background: #f8d7da;
            color: #721c24;
        }

        .no-competitions {
            text-align: center;
            color: #666;
            font-style: italic;
            grid-column: 1 / -1;
            padding: 40px;
        }

        .back-link {
            text-align: center;
            margin-top: 40px;
        }

        .back-link a {
            color: #e30613;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="ivory-theme">
    <div class="container">
        <div class="competitions-container">
            <h1 class="section-title">
                <i class="fas fa-trophy"></i> Compétitions Wushu Club CI
            </h1>

            <!-- Formulaire d'ajout (visible seulement pour les admins - à implémenter plus tard) -->
            <div class="add-competition-form" id="add-form" style="display: none;">
                <h2 style="text-align: center; margin-bottom: 30px; color: #e30613;">
                    <i class="fas fa-plus-circle"></i> Ajouter une Compétition
                </h2>

                <?php echo $message; ?>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom"><i class="fas fa-trophy"></i> Nom de la compétition *</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut"><i class="fas fa-calendar-alt"></i> Date de début *</label>
                            <input type="date" id="date_debut" name="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin"><i class="fas fa-calendar-alt"></i> Date de fin *</label>
                            <input type="date" id="date_fin" name="date_fin" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="lieu"><i class="fas fa-map-marker-alt"></i> Lieu *</label>
                            <input type="text" id="lieu" name="lieu" placeholder="Ville, Pays" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-info-circle"></i> Description</label>
                        <textarea id="description" name="description" placeholder="Détails de la compétition, disciplines, etc."></textarea>
                    </div>

                    <input type="hidden" name="ajouter_competition" value="1">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus"></i> Ajouter la Compétition
                    </button>
                </form>
            </div>

            <!-- Liste des compétitions -->
            <div class="competitions-grid">
                <?php if (!empty($competitions)): ?>
                    <?php foreach ($competitions as $competition):
                        // Déterminer le statut basé sur les dates
                        $today = date('Y-m-d');
                        $status = $competition['statut'];

                        if ($status === 'planifiee') {
                            if ($today >= $competition['date_debut'] && $today <= $competition['date_fin']) {
                                $status = 'en_cours';
                            } elseif ($today > $competition['date_fin']) {
                                $status = 'terminee';
                            }
                        }
                    ?>
                        <div class="competition-card">
                            <div class="competition-header">
                                <div class="competition-title"><?php echo htmlspecialchars($competition['nom']); ?></div>
                                <div class="competition-dates">
                                    <?php echo date('d/m/Y', strtotime($competition['date_debut'])); ?> -
                                    <?php echo date('d/m/Y', strtotime($competition['date_fin'])); ?>
                                </div>
                            </div>
                            <div class="competition-body">
                                <div class="competition-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($competition['lieu']); ?>
                                </div>
                                <?php if (!empty($competition['description'])): ?>
                                    <div class="competition-info">
                                        <i class="fas fa-info-circle"></i>
                                        <?php echo htmlspecialchars(substr($competition['description'], 0, 100)) . (strlen($competition['description']) > 100 ? '...' : ''); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="competition-status status-<?php echo $status; ?>">
                                    <?php
                                    switch ($status) {
                                        case 'planifiee': echo 'Planifiée'; break;
                                        case 'en_cours': echo 'En cours'; break;
                                        case 'terminee': echo 'Terminée'; break;
                                        case 'annulee': echo 'Annulée'; break;
                                        default: echo ucfirst($status);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-competitions">
                        <i class="fas fa-calendar-times" style="font-size: 3em; color: #ccc; margin-bottom: 20px;"></i>
                        <p>Aucune compétition programmée pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="back-link">
                <a href="index.html"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>
</body>
</html>