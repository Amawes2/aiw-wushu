<?php
// admin.php - Panel d'administration pour Wushu Club CI

require_once 'functions.php';
require_once 'config_email.php';

// Configuration de la base de données
$db_file = 'wushuclubci.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

session_start();

// Initialiser la variable logged_in
$logged_in = false;

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Définir la variable logged_in pour les vérifications
$logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Vérifier le timeout de session (30 minutes)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 1800)) {
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}

// Traitement de l'ajout de compétition
if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_competition'])) {
    $nom = clean_input($_POST['competition_nom']);
    $type = clean_input($_POST['competition_type']);
    $date_debut = clean_input($_POST['competition_date_debut']);
    $date_fin = clean_input($_POST['competition_date_fin']);
    $lieu = clean_input($_POST['competition_lieu']);
    $description = clean_input($_POST['competition_description']);
    
    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($type)) $errors[] = "Le type est requis.";
    if (empty($date_debut)) $errors[] = "La date de début est requise.";
    if (empty($date_fin)) $errors[] = "La date de fin est requise.";
    if (empty($lieu)) $errors[] = "Le lieu est requis.";
    
    if ($date_debut && $date_fin && strtotime($date_fin) < strtotime($date_debut)) {
        $errors[] = "La date de fin ne peut pas être antérieure à la date de début.";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO competitions (nom, type, date_debut, date_fin, lieu, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $type, $date_debut, $date_fin, $lieu, $description]);
            $message = "Compétition ajoutée avec succès.";
        } catch (PDOException $e) {
            $message = "Erreur lors de l'ajout de la compétition : " . $e->getMessage();
        }
    } else {
        $message = "Erreurs : " . implode(", ", $errors);
    }
}

// Traitement de la modification de compétition
if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifier_competition'])) {
    $id = intval($_POST['competition_id']);
    $nom = clean_input($_POST['edit_nom']);
    $type = clean_input($_POST['edit_type']);
    $date_debut = clean_input($_POST['edit_date_debut']);
    $date_fin = clean_input($_POST['edit_date_fin']);
    $lieu = clean_input($_POST['edit_lieu']);
    $description = clean_input($_POST['edit_description']);
    
    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($type)) $errors[] = "Le type est requis.";
    if (empty($date_debut)) $errors[] = "La date de début est requise.";
    if (empty($date_fin)) $errors[] = "La date de fin est requise.";
    if (empty($lieu)) $errors[] = "Le lieu est requis.";
    
    if ($date_debut && $date_fin && strtotime($date_fin) < strtotime($date_debut)) {
        $errors[] = "La date de fin ne peut pas être antérieure à la date de début.";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE competitions SET nom = ?, type = ?, date_debut = ?, date_fin = ?, lieu = ?, description = ? WHERE id = ?");
            $stmt->execute([$nom, $type, $date_debut, $date_fin, $lieu, $description, $id]);
            $message = "Compétition modifiée avec succès.";
        } catch (PDOException $e) {
            $message = "Erreur lors de la modification de la compétition : " . $e->getMessage();
        }
    } else {
        $message = "Erreurs : " . implode(", ", $errors);
    }
}

// Traitement des actions sur les clubs et compétiteurs
if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if (isset($_POST['club_id'])) {
        $club_id = intval($_POST['club_id']);
        
        if ($action === 'valider') {
            $stmt = $pdo->prepare("UPDATE clubs SET statut = 'valide' WHERE id = ?");
            $stmt->execute([$club_id]);

            // Récupérer les infos du club pour l'email
            $stmt_info = $pdo->prepare("SELECT nom_club, email FROM clubs WHERE id = ?");
            $stmt_info->execute([$club_id]);
            $club_info = $stmt_info->fetch(PDO::FETCH_ASSOC);

            if ($club_info && sendValidationNotification('club', $club_info['email'], $club_info['nom_club'])) {
                $message = "Club validé avec succès. Email de confirmation envoyé.";
            } else {
                $message = "Club validé avec succès.";
            }

            // Déclencher une notification temps réel
            echo "<script>
                if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('realtime-notification', {
                        detail: {
                            type: 'success',
                            message: 'Club validé : {$club_info['nom_club']}',
                            persistent: false
                        }
                    }));
                }
            </script>";
        } elseif ($action === 'rejeter') {
            $stmt = $pdo->prepare("UPDATE clubs SET statut = 'rejete' WHERE id = ?");
            $stmt->execute([$club_id]);
            $message = "Club rejeté.";
        } elseif ($action === 'supprimer') {
            $stmt = $pdo->prepare("DELETE FROM clubs WHERE id = ?");
            $stmt->execute([$club_id]);
            $message = "Club supprimé.";
        }
    } elseif (isset($_POST['competiteur_id'])) {
        $competiteur_id = intval($_POST['competiteur_id']);
        
        if ($action === 'valider_competiteur') {
            $stmt = $pdo->prepare("UPDATE competiteurs SET statut = 'valide' WHERE id = ?");
            $stmt->execute([$competiteur_id]);

            // Récupérer les infos du compétiteur pour l'email
            $stmt_info = $pdo->prepare("SELECT nom, prenom, email FROM competiteurs WHERE id = ?");
            $stmt_info->execute([$competiteur_id]);
            $competitor_info = $stmt_info->fetch(PDO::FETCH_ASSOC);

            if ($competitor_info && sendValidationNotification('competitor', $competitor_info['email'], $competitor_info['prenom'] . ' ' . $competitor_info['nom'])) {
                $message = "Compétiteur validé avec succès. Email de confirmation envoyé.";
            } else {
                $message = "Compétiteur validé avec succès.";
            }

            // Déclencher une notification temps réel
            echo "<script>
                if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('realtime-notification', {
                        detail: {
                            type: 'info',
                            message: 'Compétiteur validé : {$competitor_info['prenom']} {$competitor_info['nom']}',
                            persistent: false
                        }
                    }));
                }
            </script>";
        } elseif ($action === 'rejeter_competiteur') {
            $stmt = $pdo->prepare("UPDATE competiteurs SET statut = 'rejete' WHERE id = ?");
            $stmt->execute([$competiteur_id]);
            $message = "Compétiteur rejeté.";
        } elseif ($action === 'supprimer_competiteur') {
            $stmt = $pdo->prepare("DELETE FROM competiteurs WHERE id = ?");
            $stmt->execute([$competiteur_id]);
            $message = "Compétiteur supprimé.";
        }
    } elseif (isset($_POST['competition_id'])) {
        $competition_id = intval($_POST['competition_id']);
        
        if ($action === 'annuler_competition') {
            $stmt = $pdo->prepare("UPDATE competitions SET statut = 'annulee' WHERE id = ?");
            $stmt->execute([$competition_id]);
            $message = "Compétition annulée.";
        } elseif ($action === 'supprimer_competition') {
            $stmt = $pdo->prepare("DELETE FROM competitions WHERE id = ?");
            $stmt->execute([$competition_id]);
            $message = "Compétition supprimée.";
        }
    }
}

// Récupérer les statistiques
if ($logged_in) {
    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubs");
    $stats['total_clubs'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as en_attente FROM clubs WHERE statut = 'en_attente'");
    $stats['en_attente'] = $stmt->fetch()['en_attente'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as valides FROM clubs WHERE statut = 'valide'");
    $stats['valides'] = $stmt->fetch()['valides'];

    // Statistiques compétiteurs
    $stmt = $pdo->query("SELECT COUNT(*) as total_competiteurs FROM competiteurs");
    $stats['total_competiteurs'] = $stmt->fetch()['total_competiteurs'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as competiteurs_attente FROM competiteurs WHERE statut = 'en_attente'");
    $stats['competiteurs_attente'] = $stmt->fetch()['competiteurs_attente'];

    // Statistiques compétitions
    $stmt = $pdo->query("SELECT COUNT(*) as total_competitions FROM competitions");
    $stats['total_competitions'] = $stmt->fetch()['total_competitions'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as competitions_planifiees FROM competitions WHERE statut = 'planifiee'");
    $stats['competitions_planifiees'] = $stmt->fetch()['competitions_planifiees'];
    
    // Récupérer la liste des clubs
    $stmt = $pdo->query("SELECT * FROM clubs ORDER BY date_inscription DESC");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer la liste des compétiteurs
    $stmt = $pdo->query("SELECT c.*, cl.nom_club FROM competiteurs c LEFT JOIN clubs cl ON c.club_id = cl.id ORDER BY c.date_inscription DESC");
    $competiteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer la liste des compétitions
    $stmt = $pdo->query("SELECT * FROM competitions ORDER BY date_debut DESC");
    $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-login {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .admin-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #e30613;
            margin: 10px 0;
        }
        .clubs-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .clubs-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .clubs-table th, .clubs-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .clubs-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 500;
        }
        .status-en_attente { background: #fff3cd; color: #856404; }
        .status-valide { background: #d4edda; color: #155724; }
        .status-rejete { background: #f8d7da; color: #721c24; }
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            margin-right: 5px;
        }
        .btn-valider { background: #28a745; color: white; }
        .btn-rejeter { background: #dc3545; color: white; }
        .btn-supprimer { background: #6c757d; color: white; }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #e30613;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body class="ivory-theme">
    <div class="container">
        <?php if (!$logged_in): ?>
            <!-- Formulaire de connexion -->
            <div class="admin-login">
                <h2 style="text-align: center; color: #e30613; margin-bottom: 30px;">
                    <i class="fas fa-lock"></i> Connexion Administration Wushu Club CI
                </h2>
                
                <?php if (isset($login_error)): ?>
                    <div style="color: #e74c3c; margin-bottom: 15px; text-align: center;">
                        <?php echo $login_error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div style="margin-bottom: 15px;">
                        <label for="username" style="display: block; margin-bottom: 5px; font-weight: 600;">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="password" style="display: block; margin-bottom: 5px; font-weight: 600;">Mot de passe</label>
                        <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <input type="hidden" name="login" value="1">
                    <button type="submit" style="width: 100%; background: #e30613; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="index.html" style="color: #e30613; text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Retour au site public
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Panel d'administration -->
            <a href="?logout=1" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
            
            <h1 style="text-align: center; color: #e30613; margin-bottom: 30px;">
                <i class="fas fa-cogs"></i> Panel d'Administration Wushu Club CI
            </h1>
            
            <div style="text-align: center; margin-bottom: 20px;">
                <a href="dashboard.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    <i class="fas fa-chart-line"></i> Voir le Dashboard Détaillé
                </a>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- Statistiques -->
            <div class="admin-dashboard">
                <div class="stat-card">
                    <i class="fas fa-users" style="font-size: 2em; color: #e30613;"></i>
                    <div class="stat-number"><?php echo $stats['total_clubs']; ?></div>
                    <div>Clubs Inscrits</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-clock" style="font-size: 2em; color: #ffc107;"></i>
                    <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
                    <div>Clubs En Attente</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-check-circle" style="font-size: 2em; color: #28a745;"></i>
                    <div class="stat-number"><?php echo $stats['valides']; ?></div>
                    <div>Clubs Validés</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-user-friends" style="font-size: 2em; color: #17a2b8;"></i>
                    <div class="stat-number"><?php echo $stats['total_competiteurs']; ?></div>
                    <div>Compétiteurs</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-trophy" style="font-size: 2em; color: #fd7e14;"></i>
                    <div class="stat-number"><?php echo $stats['total_competitions']; ?></div>
                    <div>Compétitions</div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-user-clock" style="font-size: 2em; color: #ffc107;"></i>
                    <div class="stat-number"><?php echo $stats['competiteurs_attente']; ?></div>
                    <div>Compétiteurs En Attente</div>
                </div>
            </div>
            
            <?php if ($stats['en_attente'] > 0 || $stats['competiteurs_attente'] > 0): ?>
                <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffeaa7; text-align: center;">
                    <i class="fas fa-bell"></i> <strong>Notifications :</strong> 
                    <?php if ($stats['en_attente'] > 0): ?> <?php echo $stats['en_attente']; ?> club(s) en attente de validation. <?php endif; ?>
                    <?php if ($stats['competiteurs_attente'] > 0): ?> <?php echo $stats['competiteurs_attente']; ?> compétiteur(s) en attente de validation. <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Section Aide et Explications -->
            <div class="help-section" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; border-left: 5px solid #e30613;">
                <h3 style="color: #e30613; margin-bottom: 15px;"><i class="fas fa-info-circle"></i> Guide d'utilisation du panel administrateur</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <h4 style="color: #333;"><i class="fas fa-users"></i> Gestion des Clubs</h4>
                        <p style="font-size: 0.9em; color: #666;">Les clubs s'inscrivent via le site public. Vous devez <strong>valider</strong> ou <strong>rejeter</strong> leurs demandes. Un email de confirmation est envoyé automatiquement lors de la validation.</p>
                    </div>
                    <div>
                        <h4 style="color: #333;"><i class="fas fa-user-friends"></i> Gestion des Compétiteurs</h4>
                        <p style="font-size: 0.9em; color: #666;">Les compétiteurs s'inscrivent eux-mêmes via les formulaires. Vérifiez leurs informations et <strong>validez</strong> leur participation aux compétitions.</p>
                    </div>
                    <div>
                        <h4 style="color: #333;"><i class="fas fa-trophy"></i> Gestion des Compétitions</h4>
                        <p style="font-size: 0.9em; color: #666;">Vous pouvez <strong>ajouter</strong> de nouvelles compétitions, les <strong>modifier</strong> ou les <strong>annuler</strong>. Publiez les informations pour que les compétiteurs puissent s'inscrire.</p>
                    </div>
                    <div>
                        <h4 style="color: #333;"><i class="fas fa-chart-bar"></i> Statistiques</h4>
                        <p style="font-size: 0.9em; color: #666;">Surveillez les inscriptions en cours. Les éléments "en attente" nécessitent votre attention pour validation.</p>
                    </div>
                </div>
                <p style="font-size: 0.85em; color: #888; margin-top: 15px;"><i class="fas fa-lightbulb"></i> <strong>Conseil :</strong> Vérifiez régulièrement les nouvelles inscriptions pour maintenir une base de données à jour.</p>
            </div>
            
            <!-- Navigation entre sections -->
            <div class="section-nav" style="margin-bottom: 30px; text-align: center;">
                <button onclick="showSection('clubs')" id="btn-clubs" class="section-btn active" style="background: #e30613; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin: 0 10px; cursor: pointer; position: relative;">
                    Gestion des Clubs
                    <?php if ($stats['en_attente'] > 0): ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: #ffc107; color: #000; border-radius: 50%; padding: 2px 6px; font-size: 0.8em; font-weight: bold;"><?php echo $stats['en_attente']; ?></span>
                    <?php endif; ?>
                </button>
                <button onclick="showSection('competiteurs')" id="btn-competiteurs" class="section-btn" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin: 0 10px; cursor: pointer; position: relative;">
                    Gestion des Compétiteurs
                    <?php if ($stats['competiteurs_attente'] > 0): ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: #ffc107; color: #000; border-radius: 50%; padding: 2px 6px; font-size: 0.8em; font-weight: bold;"><?php echo $stats['competiteurs_attente']; ?></span>
                    <?php endif; ?>
                </button>
                <button onclick="showSection('competitions')" id="btn-competitions" class="section-btn" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin: 0 10px; cursor: pointer;">Gestion des Compétitions</button>
                <a href="results.php" class="section-btn" style="background: #ffd700; color: #333; padding: 10px 20px; border: none; border-radius: 5px; margin: 0 10px; text-decoration: none; display: inline-block;"><i class="fas fa-trophy"></i> Gestion des Résultats</a>
            </div>
            
            <!-- Section Clubs -->
            <div id="section-clubs" class="admin-section">
                <h2 style="color: #e30613; margin-bottom: 20px;"><i class="fas fa-users"></i> Clubs Inscrits</h2>
                
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #007bff;">
                    <p style="margin: 0; font-size: 0.9em;"><i class="fas fa-info-circle"></i> <strong>Actions disponibles :</strong> 
                    <strong>Valider</strong> pour approuver l'inscription (email envoyé automatiquement), 
                    <strong>Rejeter</strong> pour refuser, 
                    <strong>Supprimer</strong> pour retirer définitivement.</p>
                </div>
                
                <!-- Recherche -->
                <div style="margin-bottom: 20px; text-align: right;">
                    <input type="text" id="search-clubs" placeholder="Rechercher dans les clubs..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 250px;" onkeyup="filterTable('clubs-table', this.value)">
                </div>
                
                <!-- Liste des clubs -->
                <div class="clubs-table">
                    <table id="clubs-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom du Club</th>
                                <th>Maître</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Date d'Inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clubs as $club): ?>
                                <tr>
                                    <td><?php echo $club['id']; ?></td>
                                    <td><?php echo htmlspecialchars($club['nom_club']); ?></td>
                                    <td><?php echo htmlspecialchars($club['maitre']); ?></td>
                                    <td><?php echo htmlspecialchars($club['email']); ?></td>
                                    <td><?php echo htmlspecialchars($club['telephone']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($club['date_inscription'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $club['statut']; ?>">
                                            <?php 
                                            if ($club['statut'] == 'en_attente') echo 'En attente';
                                            elseif ($club['statut'] == 'valide') echo 'Validé';
                                            elseif ($club['statut'] == 'rejete') echo 'Rejeté';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($club['statut'] == 'en_attente'): ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="club_id" value="<?php echo $club['id']; ?>">
                                                <button type="submit" name="action" value="valider" class="action-btn btn-valider" onclick="return confirm('Êtes-vous sûr de vouloir valider ce club ? Un email de confirmation sera envoyé.')">
                                                    <i class="fas fa-check"></i> Valider
                                                </button>
                                                <button type="submit" name="action" value="rejeter" class="action-btn btn-rejeter" onclick="return confirm('Êtes-vous sûr de vouloir rejeter ce club ?')">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="club_id" value="<?php echo $club['id']; ?>">
                                            <button type="submit" name="action" value="supprimer" class="action-btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce club ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($clubs)): ?>
                    <p style="text-align: center; margin-top: 20px; color: #666;">Aucun club inscrit pour le moment.</p>
                <?php endif; ?>
            </div>
            
            <!-- Section Compétiteurs -->
            <div id="section-competiteurs" class="admin-section" style="display: none;">
                <h2 style="color: #e30613; margin-bottom: 20px;"><i class="fas fa-user-friends"></i> Compétiteurs Inscrits</h2>
                
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #007bff;">
                    <p style="margin: 0; font-size: 0.9em;"><i class="fas fa-info-circle"></i> <strong>Actions disponibles :</strong> 
                    <strong>Valider</strong> pour approuver la participation, 
                    <strong>Rejeter</strong> pour refuser, 
                    <strong>Supprimer</strong> pour retirer. Les compétiteurs s'inscrivent via le site et attendent votre validation.</p>
                </div>
                
                <!-- Recherche -->
                <div style="margin-bottom: 20px; text-align: right;">
                    <input type="text" id="search-competiteurs" placeholder="Rechercher dans les compétiteurs..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 250px;" onkeyup="filterTable('competiteurs-table', this.value)">
                    <button onclick="exportCompetiteursToCSV()" style="margin-left: 10px; padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-download"></i> Exporter CSV
                    </button>
                </div>
                
                <!-- Liste des compétiteurs -->
                <div class="clubs-table">
                    <table id="competiteurs-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Âge</th>
                                <th>Catégorie</th>
                                <th>Style</th>
                                <th>Arme</th>
                                <th>Club</th>
                                <th>Email</th>
                                <th>Date d'Inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($competiteurs as $competiteur): 
                                $birth_date = new DateTime($competiteur['date_naissance']);
                                $today = new DateTime();
                                $age = $today->diff($birth_date)->y;
                            ?>
                                <tr>
                                    <td><?php echo $competiteur['id']; ?></td>
                                    <td><?php echo htmlspecialchars($competiteur['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($competiteur['prenom']); ?></td>
                                    <td><?php echo $age; ?> ans</td>
                                    <td><?php echo ucfirst($competiteur['categorie']); ?></td>
                                    <td><?php echo ucfirst($competiteur['style']); ?></td>
                                    <td><?php echo ucfirst($competiteur['arme_specialisation'] ?? 'Aucune'); ?></td>
                                    <td><?php echo htmlspecialchars($competiteur['nom_club'] ?? 'Indépendant'); ?></td>
                                    <td><?php echo htmlspecialchars($competiteur['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($competiteur['date_inscription'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $competiteur['statut']; ?>">
                                            <?php 
                                            if ($competiteur['statut'] == 'en_attente') echo 'En attente';
                                            elseif ($competiteur['statut'] == 'valide') echo 'Validé';
                                            elseif ($competiteur['statut'] == 'rejete') echo 'Rejeté';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($competiteur['statut'] == 'en_attente'): ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="competiteur_id" value="<?php echo $competiteur['id']; ?>">
                                                <button type="submit" name="action" value="valider_competiteur" class="action-btn btn-valider" onclick="return confirm('Êtes-vous sûr de vouloir valider ce compétiteur ?')">
                                                    <i class="fas fa-check"></i> Valider
                                                </button>
                                                <button type="submit" name="action" value="rejeter_competiteur" class="action-btn btn-rejeter" onclick="return confirm('Êtes-vous sûr de vouloir rejeter ce compétiteur ?')">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="competiteur_id" value="<?php echo $competiteur['id']; ?>">
                                            <button type="submit" name="action" value="supprimer_competiteur" class="action-btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compétiteur ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($competiteurs)): ?>
                    <p style="text-align: center; margin-top: 20px; color: #666;">Aucun compétiteur inscrit pour le moment.</p>
                <?php endif; ?>
            </div>
            
            <!-- Section Compétitions -->
            <div id="section-competitions" class="admin-section" style="display: none;">
                <h2 style="color: #e30613; margin-bottom: 20px;"><i class="fas fa-trophy"></i> Compétitions</h2>
                
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #007bff;">
                    <p style="margin: 0; font-size: 0.9em;"><i class="fas fa-info-circle"></i> <strong>Gestion des événements :</strong> 
                    Ajoutez de nouvelles compétitions pour que les compétiteurs puissent s'inscrire. Modifiez ou annulez les compétitions existantes selon les besoins.</p>
                </div>
                
                <!-- Bouton pour ajouter une compétition -->
                <div style="margin-bottom: 20px; text-align: right;">
                    <button onclick="showAddCompetitionForm()" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-plus"></i> Ajouter une Compétition
                    </button>
                </div>
                
                <!-- Formulaire d'ajout de compétition (masqué par défaut) -->
                <div id="add-competition-form" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                    <h3 style="color: #e30613; margin-bottom: 15px;"><i class="fas fa-plus-circle"></i> Nouvelle Compétition</h3>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nom *</label>
                            <input type="text" name="competition_nom" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Type *</label>
                            <select name="competition_type" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Choisir...</option>
                                <option value="coupe">Coupe de Côte d'Ivoire</option>
                                <option value="championnat">Championnat de Côte d'Ivoire</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Lieu *</label>
                            <input type="text" name="competition_lieu" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Date début *</label>
                            <input type="date" name="competition_date_debut" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Date fin *</label>
                            <input type="date" name="competition_date_fin" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Description</label>
                            <textarea name="competition_description" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
                        </div>
                        <div style="grid-column: 1 / -1; text-align: right;">
                            <button type="submit" name="ajouter_competition" value="1" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
                                <i class="fas fa-save"></i> Ajouter
                            </button>
                            <button type="button" onclick="hideAddCompetitionForm()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Liste des compétitions -->
                <div class="clubs-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                <tr>
                                    <td><?php echo $competition['id']; ?></td>
                                    <td><?php echo htmlspecialchars($competition['nom']); ?></td>
                                    <td><?php echo $competition['type'] === 'coupe' ? 'Coupe de Côte d\'Ivoire' : 'Championnat de Côte d\'Ivoire'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($competition['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($competition['date_fin'])); ?></td>
                                    <td><?php echo htmlspecialchars($competition['lieu']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $status; ?>">
                                            <?php 
                                            switch ($status) {
                                                case 'planifiee': echo 'Planifiée'; break;
                                                case 'en_cours': echo 'En cours'; break;
                                                case 'terminee': echo 'Terminée'; break;
                                                case 'annulee': echo 'Annulée'; break;
                                                default: echo ucfirst($status);
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($status === 'planifiee'): ?>
                                            <button type="button" onclick="openEditModal(<?php echo $competition['id']; ?>, '<?php echo addslashes($competition['nom']); ?>', '<?php echo $competition['type']; ?>', '<?php echo $competition['date_debut']; ?>', '<?php echo $competition['date_fin']; ?>', '<?php echo addslashes($competition['lieu']); ?>', '<?php echo addslashes($competition['description']); ?>')" class="action-btn" style="background: #17a2b8; color: white;">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="competition_id" value="<?php echo $competition['id']; ?>">
                                                <button type="submit" name="action" value="annuler_competition" class="action-btn btn-rejeter" onclick="return confirm('Êtes-vous sûr d\'annuler cette compétition ?')">
                                                    <i class="fas fa-ban"></i> Annuler
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="competition_id" value="<?php echo $competition['id']; ?>">
                                            <button type="submit" name="action" value="supprimer_competition" class="action-btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette compétition ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($competitions)): ?>
                    <p style="text-align: center; margin-top: 20px; color: #666;">Aucune compétition créée pour le moment.</p>
                <?php endif; ?>
                
                <!-- Modal de modification de compétition -->
                <div id="edit-competition-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 20px; border-radius: 10px; width: 90%; max-width: 500px;">
                        <h3 style="color: #e30613; margin-bottom: 15px;"><i class="fas fa-edit"></i> Modifier la Compétition</h3>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="competition_id" id="edit-competition-id">
                            <div style="margin-bottom: 10px;">
                                <label for="edit-nom">Nom :</label>
                                <input type="text" id="edit-nom" name="edit_nom" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label for="edit-type">Type :</label>
                                <select id="edit-type" name="edit_type" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="coupe">Coupe de Côte d'Ivoire</option>
                                    <option value="championnat">Championnat de Côte d'Ivoire</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label for="edit-date_debut">Date de début :</label>
                                <input type="date" id="edit-date_debut" name="edit_date_debut" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label for="edit-date_fin">Date de fin :</label>
                                <input type="date" id="edit-date_fin" name="edit_date_fin" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label for="edit-lieu">Lieu :</label>
                                <input type="text" id="edit-lieu" name="edit_lieu" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label for="edit-description">Description :</label>
                                <textarea id="edit-description" name="edit_description" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; height: 80px;"></textarea>
                            </div>
                            <div style="text-align: right;">
                                <button type="button" onclick="closeEditModal()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                                <button type="submit" name="modifier_competition" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filterTable(tableId, searchTerm) {
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            const searchLower = searchTerm.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(searchLower) > -1) {
                        found = true;
                        break;
                    }
                }

                rows[i].style.display = found ? '' : 'none';
            }
        }

        function showSection(section) {
            // Masquer toutes les sections
            document.getElementById('section-clubs').style.display = 'none';
            document.getElementById('section-competiteurs').style.display = 'none';
            document.getElementById('section-competitions').style.display = 'none';
            
            // Désactiver tous les boutons
            document.getElementById('btn-clubs').classList.remove('active');
            document.getElementById('btn-competiteurs').classList.remove('active');
            document.getElementById('btn-competitions').classList.remove('active');
            document.getElementById('btn-clubs').style.background = '#6c757d';
            document.getElementById('btn-competiteurs').style.background = '#6c757d';
            document.getElementById('btn-competitions').style.background = '#6c757d';
            
            // Afficher la section sélectionnée
            document.getElementById('section-' + section).style.display = 'block';
            document.getElementById('btn-' + section).classList.add('active');
            document.getElementById('btn-' + section).style.background = '#e30613';
        }

        function showAddCompetitionForm() {
            document.getElementById('add-competition-form').style.display = 'block';
        }

        function hideAddCompetitionForm() {
            document.getElementById('add-competition-form').style.display = 'none';
        }

        function openEditModal(id, nom, type, dateDebut, dateFin, lieu, description) {
            document.getElementById('edit-competition-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-type').value = type;
            document.getElementById('edit-date_debut').value = dateDebut;
            document.getElementById('edit-date_fin').value = dateFin;
            document.getElementById('edit-lieu').value = lieu;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-competition-modal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('edit-competition-modal').style.display = 'none';
        }
    </script>
</body>
</html>