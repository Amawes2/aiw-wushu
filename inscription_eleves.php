<?php
// inscription_eleves.php - Inscription des élèves par le maître

// Inclure la configuration email
require_once 'config_email.php';

// Configuration de la base de données
$db_file = 'wushuclubci.db';

// Créer la base de données et les tables si elles n'existent pas
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Table des compétiteurs (déjà créée)
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Vérifier si maitre_id est fourni
$maitre_id = isset($_GET['maitre_id']) ? intval($_GET['maitre_id']) : 0;
if (!$maitre_id) {
    die("Accès non autorisé.");
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
    die("Maître non trouvé.");
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

// Traitement du formulaire d'ajout d'élève
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
            // Insérer l'élève
            $stmt = $pdo->prepare("INSERT INTO competiteurs (nom, prenom, date_naissance, sexe, categorie, style, arme_specialisation, club_id, email, telephone, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'eleve')");
            $stmt->execute([$nom, $prenom, $date_naissance, $sexe, $categorie, $style, $arme_specialisation, $maitre['club_id'], $email, $telephone]);

            $message = "<div class='alert alert-success'>Élève inscrit avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Erreur lors de l'inscription : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>" . implode("<br>", $errors) . "</div>";
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
    <title>Inscription Élèves - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .eleve-form {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
            width: 100%;
            margin-bottom: 20px;
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
        .eleves-list {
            margin-top: 50px;
        }
        .eleves-list h3 {
            color: #e30613;
            margin-bottom: 20px;
        }
        .eleve-item {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid #e30613;
        }
        .age-display {
            font-weight: bold;
            color: #e30613;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="eleve-form">
            <h2><i class="fas fa-users"></i> Inscription des Élèves</h2>
            <p>Maître: <?php echo htmlspecialchars($maitre['nom'] . ' ' . $maitre['prenom']); ?> (Club: <?php echo htmlspecialchars($maitre['club_id'] ? 'Club affilié' : 'Indépendant'); ?>)</p>

            <?php echo $message; ?>

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
                        <div id="age-display" class="age-display">Âge: -- ans</div>
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

            <div class="eleves-list">
                <h3><i class="fas fa-list"></i> Élèves inscrits</h3>
                <?php if (empty($eleves)): ?>
                    <p>Aucun élève inscrit pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($eleves as $eleve): ?>
                        <div class="eleve-item">
                            <strong><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']); ?></strong><br>
                            Date de naissance: <?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?> (Âge: <?php echo date_diff(date_create($eleve['date_naissance']), date_create('today'))->y; ?> ans)<br>
                            Catégorie: <?php echo ucfirst($eleve['categorie']); ?> | Style: <?php echo ucfirst($eleve['style']); ?><br>
                            Email: <?php echo htmlspecialchars($eleve['email']); ?> | Tél: <?php echo htmlspecialchars($eleve['telephone']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="back-link">
                <a href="competiteurs.php"><i class="fas fa-arrow-left"></i> Retour à l'inscription</a>
            </div>
        </div>
    </div>

    <script>
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