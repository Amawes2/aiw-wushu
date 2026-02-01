<?php
// clubs.php - Inscription des clubs pour Wushu Club CI

// Inclure la configuration email
require_once 'config_email.php';

// Configuration de la base de données
$db_file = 'wushuclubci.db';

// Créer la base de données et la table si elle n'existe pas
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS clubs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom_club TEXT NOT NULL UNIQUE,
        maitre TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        telephone TEXT,
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
        statut TEXT DEFAULT 'en_attente'
    )";
    $pdo->exec($sql);
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

// Traitement du formulaire
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_club = clean_input($_POST['nom_club']);
    $maitre = clean_input($_POST['maitre']);
    $email = clean_input($_POST['email']);
    $telephone = clean_input($_POST['telephone']);

    // Validation
    $errors = [];
    if (empty($nom_club)) $errors[] = "Le nom du club est requis.";
    if (empty($maitre)) $errors[] = "Le nom du maître est requis.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email valide requis.";
    if (empty($telephone)) $errors[] = "Le numéro de téléphone est requis.";

    if (empty($errors)) {
        try {
            // Vérifier si le club existe déjà
            $stmt = $pdo->prepare("SELECT id FROM clubs WHERE nom_club = ? OR email = ?");
            $stmt->execute([$nom_club, $email]);
            if ($stmt->rowCount() > 0) {
                $message = "<div class='alert alert-error'>Un club avec ce nom ou cet email existe déjà.</div>";
            } else {
                // Insérer le club
                $stmt = $pdo->prepare("INSERT INTO clubs (nom_club, maitre, email, telephone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom_club, $maitre, $email, $telephone]);

                // Envoyer l'email de confirmation
                $clubData = [
                    'nom_club' => $nom_club,
                    'president' => $maitre,
                    'email' => $email,
                    'telephone' => $telephone,
                    'adresse' => 'À confirmer'
                ];

                if (sendRegistrationNotification('club', $clubData)) {
                    $message = "<div class='alert alert-success'>Club inscrit avec succès ! Un email de confirmation vous a été envoyé.</div>";
                } else {
                    $message = "<div class='alert alert-success'>Club inscrit avec succès ! (Note: l'email de confirmation n'a pas pu être envoyé)</div>";
                }
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
    <title>Inscription Club - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .club-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
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
    </style>
</head>
<body class="ivory-theme">
    <div class="container">
        <div class="club-form">
            <h2 style="text-align: center; color: #e30613; margin-bottom: 30px;">
                <i class="fas fa-users"></i> Inscription d'un Club Wushu Club CI
            </h2>
            
            <?php echo $message; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="nom_club"><i class="fas fa-building"></i> Nom du Club *</label>
                    <input type="text" id="nom_club" name="nom_club" required>
                </div>
                
                <div class="form-group">
                    <label for="maitre"><i class="fas fa-user-tie"></i> Nom du Maître *</label>
                    <input type="text" id="maitre" name="maitre" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email du Club *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="telephone"><i class="fas fa-phone"></i> Téléphone *</label>
                    <input type="tel" id="telephone" name="telephone" required>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> S'inscrire
                </button>
            </form>
            
            <div class="back-link">
                <a href="index.html"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>
</body>
</html>