<?php
// espace_membre.php - Espace membre pour les maîtres

session_start();

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: membre_login.php');
    exit;
}

// Vérifier si le membre est connecté
if (!isset($_SESSION['membre_logged_in']) || $_SESSION['membre_logged_in'] !== true) {
    header('Location: membre_login.php');
    exit;
}

$maitre_id = $_SESSION['membre_id'];
$maitre_nom = $_SESSION['membre_nom'];

// Configuration de la base de données
$db_file = 'wushuclubci.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Récupérer les infos du maître
$maitre = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM competiteurs WHERE id = ? AND role = 'maitre'");
    $stmt->execute([$maitre_id]);
    $maitre = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

if (!$maitre) {
    session_destroy();
    header('Location: membre_login.php');
    exit;
}

// Récupérer la liste des clubs pour le formulaire
$clubs = [];
try {
    $stmt = $pdo->query("SELECT id, nom_club FROM clubs WHERE statut = 'valide' ORDER BY nom_club");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

// Fonction pour nettoyer les données
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Traitement de l'ajout d'élève
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_eleve'])) {
    $nom = clean_input($_POST['nom']);
    $prenom = clean_input($_POST['prenom']);
    $date_naissance = clean_input($_POST['date_naissance']);
    $sexe = clean_input($_POST['sexe']);
    $style = clean_input($_POST['style']);
    $arme_specialisation = !empty($_POST['arme_specialisation']) ? clean_input($_POST['arme_specialisation']) : null;
    $email = clean_input($_POST['email']);
    $telephone = clean_input($_POST['telephone']);

    // Calculer l'âge et la catégorie
    $birth_date = new DateTime($date_naissance);
    $today = new DateTime();
    $age = $today->diff($birth_date)->y;

    if ($age >= 5 && $age <= 14) {
        $categorie = 'cadet';
    } elseif ($age >= 15 && $age <= 17) {
        $categorie = 'junior';
    } elseif ($age >= 18) {
        $categorie = 'senior';
    } else {
        $categorie = 'trop_jeune';
    }

    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($prenom)) $errors[] = "Le prénom est requis.";
    if (empty($date_naissance)) $errors[] = "La date de naissance est requise.";
    if (empty($sexe) || !in_array($sexe, ['M', 'F'])) $errors[] = "Le sexe est invalide.";
    if ($categorie === 'trop_jeune') $errors[] = "L'élève doit avoir au moins 5 ans.";
    if (empty($style)) $errors[] = "Le style est requis.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email valide requis.";
    if (empty($telephone)) $errors[] = "Le numéro de téléphone est requis.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO competiteurs (nom, prenom, date_naissance, sexe, categorie, style, arme_specialisation, club_id, email, telephone, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'eleve')");
            $stmt->execute([$nom, $prenom, $date_naissance, $sexe, $categorie, $style, $arme_specialisation, $maitre['club_id'], $email, $telephone]);

            $message = "<div class='alert alert-success'>Élève ajouté avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Erreur lors de l'ajout : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>" . implode("<br>", $errors) . "</div>";
    }
}

// Traitement de la suppression d'élève
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer_eleve'])) {
    $eleve_id = intval($_POST['eleve_id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM competiteurs WHERE id = ? AND club_id = ? AND role = 'eleve'");
        $stmt->execute([$eleve_id, $maitre['club_id']]);
        
        if ($stmt->rowCount() > 0) {
            $message = "<div class='alert alert-success'>Élève supprimé avec succès !</div>";
        } else {
            $message = "<div class='alert alert-error'>Élève non trouvé ou accès non autorisé.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-error'>Erreur lors de la suppression.</div>";
    }
}

// Récupérer la liste des élèves du maître
$eleves = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM competiteurs WHERE club_id = ? AND role = 'eleve' ORDER BY date_inscription DESC");
    $stmt->execute([$maitre['club_id']]);
    $eleves = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Membre - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .member-dashboard {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e30613;
        }
        .add-student-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-section {
            display: none;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
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
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #e30613;
            outline: none;
        }
        .btn-submit {
            background: #e30613;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .students-table th, .students-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .students-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .logout-btn {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="member-dashboard">
            <div class="dashboard-header">
                <div>
                    <h2><i class="fas fa-user-shield"></i> Espace Membre</h2>
                    <p>Maître: <?php echo htmlspecialchars($maitre_nom); ?> | Club: <?php echo htmlspecialchars($maitre['club_id'] ? 'Club affilié' : 'Indépendant'); ?></p>
                </div>
                <div>
                    <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                </div>
            </div>

            <?php echo $message; ?>

            <div style="margin-bottom: 20px;">
                <button onclick="toggleForm()" class="add-student-btn">
                    <i class="fas fa-plus"></i> Ajouter un Élève
                </button>
            </div>

            <!-- Formulaire d'ajout d'élève -->
            <div id="add-form" class="form-section">
                <h3><i class="fas fa-user-plus"></i> Ajouter un Élève</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom"><i class="fas fa-user"></i> Nom *</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom"><i class="fas fa-user"></i> Prénom *</label>
                            <input type="text" id="prenom" name="prenom" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_naissance"><i class="fas fa-calendar"></i> Date de naissance *</label>
                            <input type="date" id="date_naissance" name="date_naissance" required>
                            <div id="age-display" style="margin-top: 5px; font-weight: bold; color: #e30613;">Âge: -- ans</div>
                        </div>
                        <div class="form-group">
                            <label for="sexe"><i class="fas fa-venus-mars"></i> Sexe *</label>
                            <select id="sexe" name="sexe" required>
                                <option value="">Choisir...</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="categorie"><i class="fas fa-trophy"></i> Catégorie</label>
                            <input type="text" id="categorie" name="categorie_display" readonly>
                            <input type="hidden" id="categorie_hidden" name="categorie">
                        </div>
                        <div class="form-group">
                            <label for="style"><i class="fas fa-fist-raised"></i> Style *</label>
                            <select id="style" name="style" required>
                                <option value="">Choisir...</option>
                                <option value="chang_quan">Chang Quan</option>
                                <option value="nan_quan">Nan Quan</option>
                                <option value="taichi">Taichi</option>
                                <option value="shaolin">Shaolin (forme traditionnelle)</option>
                                <option value="sanda">Sanda (par catégorie de poids)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="arme_specialisation"><i class="fas fa-shield-alt"></i> Spécialisation Arme (optionnel)</label>
                        <select id="arme_specialisation" name="arme_specialisation">
                            <option value="">Aucune</option>
                            <optgroup label="Chang Quan">
                                <option value="dao_shu">Dao Shu (sabre)</option>
                                <option value="gun_shu">Gun Shu (bâton)</option>
                                <option value="jian_shu">Jian Shu (épée)</option>
                                <option value="qiang_shu">Qiang Shu (lance)</option>
                            </optgroup>
                            <optgroup label="Nan Quan">
                                <option value="nan_dao">Nan Dao (sabre du sud)</option>
                                <option value="gun_dao">Gun Dao (bâton)</option>
                            </optgroup>
                            <optgroup label="Taichi">
                                <option value="taiji_jian">Taiji Jian (épée)</option>
                                <option value="tai_chi_shan">Tai Chi Shan (éventail)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telephone"><i class="fas fa-phone"></i> Téléphone *</label>
                            <input type="tel" id="telephone" name="telephone" required>
                        </div>
                    </div>

                    <button type="submit" name="ajouter_eleve" class="btn-submit">
                        <i class="fas fa-plus"></i> Ajouter cet Élève
                    </button>
                </form>
            </div>

            <!-- Liste des élèves -->
            <div>
                <h3><i class="fas fa-list"></i> Mes Élèves (<?php echo count($eleves); ?>)</h3>
                <?php if (empty($eleves)): ?>
                    <p style="text-align: center; padding: 40px; color: #666; background: #f8f9fa; border-radius: 8px;">Aucun élève enregistré pour le moment.</p>
                <?php else: ?>
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Date de naissance</th>
                                <th>Âge</th>
                                <th>Catégorie</th>
                                <th>Style</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eleves as $eleve): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></td>
                                    <td><?php echo date_diff(date_create($eleve['date_naissance']), date_create('today'))->y; ?> ans</td>
                                    <td><?php echo ucfirst($eleve['categorie']); ?></td>
                                    <td><?php echo ucfirst($eleve['style']); ?></td>
                                    <td><?php echo htmlspecialchars($eleve['email']); ?></td>
                                    <td><?php echo htmlspecialchars($eleve['telephone']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?');">
                                            <input type="hidden" name="eleve_id" value="<?php echo $eleve['id']; ?>">
                                            <button type="submit" name="supprimer_eleve" class="action-btn btn-danger">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('add-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        // Calculer l'âge et la catégorie automatiquement
        document.getElementById('date_naissance').addEventListener('change', function() {
            const birthDate = new Date(this.value);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            document.getElementById('age-display').textContent = 'Âge: ' + age + ' ans';

            let categorie = '';
            if (age >= 5 && age <= 14) {
                categorie = 'cadet';
            } else if (age >= 15 && age <= 17) {
                categorie = 'junior';
            } else if (age >= 18) {
                categorie = 'senior';
            } else {
                categorie = 'trop jeune';
            }

            document.getElementById('categorie').value = categorie.charAt(0).toUpperCase() + categorie.slice(1);
            document.getElementById('categorie_hidden').value = categorie;
        });

        // Gestion des armes selon le style
        document.getElementById('style').addEventListener('change', function() {
            const style = this.value;
            const armeSelect = document.getElementById('arme_specialisation');
            const options = armeSelect.querySelectorAll('option');
            
            // Masquer toutes les options d'armes
            options.forEach(option => {
                if (option.value !== '') {
                    option.style.display = 'none';
                }
            });
            
            // Afficher les options selon le style
            if (style === 'chang_quan') {
                document.querySelectorAll('optgroup[label="Chang Quan"] option').forEach(option => {
                    option.style.display = 'block';
                });
            } else if (style === 'nan_quan') {
                document.querySelectorAll('optgroup[label="Nan Quan"] option').forEach(option => {
                    option.style.display = 'block';
                });
            } else if (style === 'taichi') {
                document.querySelectorAll('optgroup[label="Taichi"] option').forEach(option => {
                    option.style.display = 'block';
                });
            } else {
                // Pour shaolin et sanda, pas d'armes spécifiques
                armeSelect.value = '';
            }
        });
    </script>
</body>
</html>