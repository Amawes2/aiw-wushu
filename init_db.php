<?php
// init_db.php - Script d'initialisation de la base de données Wushu Club CI

$db_file = 'wushuclubci.db';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion à la base de données réussie.<br>";

    // Table des clubs
    $sql_clubs = "CREATE TABLE IF NOT EXISTS clubs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom_club TEXT NOT NULL UNIQUE,
        maitre TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        telephone TEXT,
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
        statut TEXT DEFAULT 'en_attente'
    )";
    $pdo->exec($sql_clubs);
    echo "Table 'clubs' créée ou déjà existante.<br>";

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
    echo "Table 'competitions' créée ou déjà existante.<br>";

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
        password TEXT,
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
        statut TEXT DEFAULT 'en_attente',
        FOREIGN KEY (club_id) REFERENCES clubs(id)
    )";
    $pdo->exec($sql_competiteurs);
    echo "Table 'competiteurs' créée ou déjà existante.<br>";

    // Ajouter la colonne username si elle n'existe pas
    try {
        $pdo->exec("ALTER TABLE competiteurs ADD COLUMN username TEXT");
        echo "Colonne 'username' ajoutée à la table 'competiteurs'.<br>";
    } catch (PDOException $e) {
        // Colonne déjà existante, ignorer
    }

    // Table des résultats
    $sql_resultats = "CREATE TABLE IF NOT EXISTS resultats (
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
    )";
    $pdo->exec($sql_resultats);
    echo "Table 'resultats' créée ou déjà existante.<br>";

    // Table des logs
    $sql_logs = "CREATE TABLE IF NOT EXISTS logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        action TEXT NOT NULL,
        details TEXT,
        user_id INTEGER,
        ip_address TEXT,
        date_action DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_logs);
    echo "Table 'logs' créée ou déjà existante.<br>";

    // Table des administrateurs (optionnel, pour une authentification plus robuste)
    $sql_admins = "CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        email TEXT,
        role TEXT DEFAULT 'admin',
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_admins);
    echo "Table 'admins' créée ou déjà existante.<br>";

    // Insérer un admin par défaut si la table est vide
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $default_username = 'admin';
        $default_password = password_hash('fiamc2024', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admins (username, password_hash) VALUES ('$default_username', '$default_password')");
        echo "Administrateur par défaut ajouté.<br>";
    }

    echo "Initialisation de la base de données terminée avec succès.";

} catch (PDOException $e) {
    die("Erreur lors de l'initialisation de la base de données : " . $e->getMessage());
}
?></content>
<parameter name="filePath">/workspaces/aiw-wushu/init_db.php