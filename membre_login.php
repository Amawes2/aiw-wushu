<?php
// membre_login.php - Connexion des membres (maîtres)

session_start();

// Configuration de la base de données
$db_file = 'wushuclubci.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Traitement de la connexion
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM competiteurs WHERE username = ? AND role = 'maitre'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['membre_logged_in'] = true;
                $_SESSION['membre_id'] = $user['id'];
                $_SESSION['membre_nom'] = $user['nom'] . ' ' . $user['prenom'];
                header("Location: espace_membre.php");
                exit();
            } else {
                $message = "<div class='alert alert-error'>Numéro d'utilisateur ou mot de passe incorrect.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-error'>Erreur lors de la connexion.</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>Veuillez remplir tous les champs.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Membre - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-form {
            max-width: 400px;
            margin: 100px auto;
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
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2 style="text-align: center; color: #e30613;"><i class="fas fa-sign-in-alt"></i> Connexion Espace Membre</h2>
            <p style="text-align: center; margin-bottom: 30px;">Connectez-vous pour gérer vos élèves</p>

            <?php echo $message; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Numéro d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <a href="index.html">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</body>
</html>