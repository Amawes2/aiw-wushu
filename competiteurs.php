<?php
// competiteurs.php - Inscription des compétiteurs pour Wushu Club CI

// Inclure la configuration email
require_once 'config_email.php';

// Configuration de la base de données
$db_file = 'wushuclubci.db';

// Créer la base de données et les tables si elles n'existent pas
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Table des compétiteurs
    $sql_competiteurs = "CREATE TABLE IF NOT EXISTS competiteurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        prenom TEXT NOT NULL,
        date_naissance DATE NOT NULL,
        sexe TEXT NOT NULL,
        categorie TEXT NOT NULL,
        style TEXT NOT NULL,
        arme_specialisation TEXT,
        club_id INTEGER,
        email TEXT,
        telephone TEXT,
        role TEXT DEFAULT 'eleve',
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
        statut TEXT DEFAULT 'en_attente',
        FOREIGN KEY (club_id) REFERENCES clubs(id)
    )";
    $pdo->exec($sql_competiteurs);

    // Table des compétitions (si pas déjà créée)
    $sql_competitions = "CREATE TABLE IF NOT EXISTS competitions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        date_debut DATE NOT NULL,
        date_fin DATE NOT NULL,
        lieu TEXT NOT NULL,
        description TEXT,
        statut TEXT DEFAULT 'planifiee'
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

// Récupérer la liste des clubs pour le formulaire
$clubs = [];
try {
    $stmt = $pdo->query("SELECT id, nom_club FROM clubs WHERE statut = 'valide' ORDER BY nom_club");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore l'erreur si la table n'existe pas encore
}

// Traitement du formulaire
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = clean_input($_POST['nom']);
    $prenom = clean_input($_POST['prenom']);
    $date_naissance = clean_input($_POST['date_naissance']);
    $sexe = clean_input($_POST['sexe']);
    $categorie = clean_input($_POST['categorie']);
    $style = clean_input($_POST['style']);
    $arme_specialisation = !empty($_POST['arme_specialisation']) ? clean_input($_POST['arme_specialisation']) : null;
    $club_id = !empty($_POST['club_id']) ? intval($_POST['club_id']) : null;
    $email = clean_input($_POST['email']);
    $telephone = clean_input($_POST['telephone']);
    $role = clean_input($_POST['role']);
    $password = !empty($_POST['password']) ? password_hash(clean_input($_POST['password']), PASSWORD_DEFAULT) : null;

    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis.";
    if (empty($prenom)) $errors[] = "Le prénom est requis.";
    if (empty($date_naissance)) $errors[] = "La date de naissance est requise.";
    if (empty($sexe) || !in_array($sexe, ['M', 'F'])) $errors[] = "Le sexe est invalide.";
    if (empty($categorie)) $errors[] = "La catégorie est requise.";
    if (empty($style)) $errors[] = "Le style est requis.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email valide requis.";
    if (empty($telephone)) $errors[] = "Le numéro de téléphone est requis.";

    // Calculer l'âge
    $birth_date = new DateTime($date_naissance);
    $today = new DateTime();
    $age = $today->diff($birth_date)->y;

    // Validation de la catégorie selon l'âge
    if ($categorie === 'cadet' && ($age < 5 || $age > 14)) $errors[] = "Catégorie Cadet : 5-14 ans.";
    if ($categorie === 'junior' && ($age < 15 || $age > 17)) $errors[] = "Catégorie Junior : 15-17 ans.";
    if ($categorie === 'senior' && $age < 18) $errors[] = "Catégorie Senior : 18 ans et plus.";

    if (empty($errors)) {
        try {
            // Insérer le compétiteur
            $stmt = $pdo->prepare("INSERT INTO competiteurs (nom, prenom, date_naissance, sexe, categorie, style, arme_specialisation, club_id, email, telephone, role, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $date_naissance, $sexe, $categorie, $style, $arme_specialisation, $club_id, $email, $telephone, $role, $password]);

            $competiteur_id = $pdo->lastInsertId();

            // Récupérer le nom du club
            $club_name = 'Non affilié';
            if ($club_id) {
                $stmt_club = $pdo->prepare("SELECT nom_club FROM clubs WHERE id = ?");
                $stmt_club->execute([$club_id]);
                $club_data = $stmt_club->fetch(PDO::FETCH_ASSOC);
                if ($club_data) {
                    $club_name = $club_data['nom_club'];
                }
            }

            // Créer le username par défaut basé sur le nom du club
            $username = strtolower(str_replace([' ', '-', '\''], '_', $club_name)) . '_' . $competiteur_id;

            // Mettre à jour le compétiteur avec le username
            try {
                $stmt_update = $pdo->prepare("UPDATE competiteurs SET username = ? WHERE id = ?");
                $stmt_update->execute([$username, $competiteur_id]);
                $message .= "<br><strong>Username créé : $username</strong>";
            } catch (PDOException $e) {
                $message .= "<br><strong>Erreur UPDATE username : " . $e->getMessage() . "</strong>";
            }

            // Envoyer l'email de confirmation
            $competitorData = [
                'nom' => $nom,
                'prenom' => $prenom,
                'date_naissance' => $date_naissance,
                'categorie' => ucfirst($categorie),
                'style' => ucfirst($style),
                'nom_club' => $club_name,
                'email' => $email,
                'telephone' => $telephone
            ];

            if (sendRegistrationNotification('competitor', $competitorData)) {
                $message = "<div class='alert alert-success'>Compétiteur inscrit avec succès ! Un email de confirmation vous a été envoyé.";
                if ($role === 'maitre') {
                    $message .= "<br>Votre numéro d'utilisateur (username) est : <strong>$username</strong><br>";
                    $message .= " <a href='espace_membre.php'>Cliquez ici pour accéder à votre espace membre.</a>";
                }
                $message .= "</div>";
            } else {
                $message = "<div class='alert alert-success'>Compétiteur inscrit avec succès ! (Note: l'email de confirmation n'a pas pu être envoyé)";
                if ($role === 'maitre') {
                    $message .= "<br>Votre numéro d'utilisateur (username) est : <strong>$username</strong><br>";
                    $message .= " <a href='espace_membre.php'>Cliquez ici pour accéder à votre espace membre.</a>";
                }
                $message .= "</div>";
            }

            // Si c'est un maître, rediriger vers l'espace membre
            if ($role === 'maitre') {
                // Auto-connexion
                session_start();
                $_SESSION['membre_logged_in'] = true;
                $_SESSION['membre_id'] = $competiteur_id;
                $_SESSION['membre_nom'] = $nom . ' ' . $prenom;
                
                header("Location: espace_membre.php");
                echo "<script>window.location.href = 'espace_membre.php';</script>";
                exit();
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Erreur lors de l'inscription : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>" . implode("<br>", $errors) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Compétiteur - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .competiteur-form {
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
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #e30613;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .info-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body class="ivory-theme">
    <div class="container">
        <div class="competiteur-form">
            <h2 style="text-align: center; color: #e30613; margin-bottom: 30px;">
                <i class="fas fa-user-plus"></i> Inscription d'un Compétiteur Wushu Club CI
            </h2>

            <?php echo $message; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
                        <label for="categorie"><i class="fas fa-trophy"></i> Catégorie *</label>
                        <select id="categorie" name="categorie" required>
                            <option value="">Choisir...</option>
                            <option value="cadet">Cadet (5-14 ans)</option>
                            <option value="junior">Junior (15-17 ans)</option>
                            <option value="senior">Senior (18+ ans)</option>
                        </select>
                        <div class="info-text">La catégorie est calculée automatiquement selon l'âge</div>
                    </div>
                <div class="form-row">
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
                </div>

                <div class="form-group">
                    <label for="club_id"><i class="fas fa-building"></i> Club affilié (optionnel)</label>
                    <select id="club_id" name="club_id">
                        <option value="">Aucun club / Indépendant</option>
                        <?php foreach ($clubs as $club): ?>
                            <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['nom_club']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="info-text">Si votre club n'apparaît pas, il doit d'abord être validé par l'administration</div>
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

                <div class="form-group" id="password-group" style="display: none;">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe *</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="role"><i class="fas fa-user-tag"></i> Rôle *</label>
                    <select id="role" name="role" required>
                        <option value="eleve">Élève</option>
                        <option value="maitre">Maître/Coach</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> S'inscrire à la compétition
                </button>
            </form>

            <div class="back-link">
                <a href="index.html"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>

    <script>
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

        // Gestion du champ mot de passe selon le rôle
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const passwordGroup = document.getElementById('password-group');
            const passwordInput = document.getElementById('password');
            
            if (role === 'maitre') {
                passwordGroup.style.display = 'block';
                passwordInput.required = true;
            } else {
                passwordGroup.style.display = 'none';
                passwordInput.required = false;
                passwordInput.value = '';
            }
        });
    </script>
</body>
</html>