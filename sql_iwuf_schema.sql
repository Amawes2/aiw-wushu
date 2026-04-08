-- ============================================================================
-- TABLES IWUF - Phase 1: Structuration des Routines et Scoring
-- ============================================================================

-- Tableau des styles IWUF avec durées réglementaires
CREATE TABLE IF NOT EXISTS styles_iwuf (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom_style TEXT NOT NULL UNIQUE,
    description TEXT,
    avec_arme BOOLEAN DEFAULT 0,
    duree_min_self REAL, -- Minutes (durée min pour routines libres)
    duree_max_self REAL,
    duree_min_comp REAL, -- Minutes (durée min pour routines imposées)
    duree_max_comp REAL
);

-- Armes par style IWUF
CREATE TABLE IF NOT EXISTS armes_iwuf (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    style_id INTEGER NOT NULL,
    nom_arme TEXT NOT NULL,
    description TEXT,
    niveau_difficulte TEXT,
    FOREIGN KEY (style_id) REFERENCES styles_iwuf(id)
);

-- Catégories de compétiteurs IWUF
CREATE TABLE IF NOT EXISTS categories_iwuf (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom_categorie TEXT NOT NULL UNIQUE,
    age_min INTEGER,
    age_max INTEGER,
    sexe TEXT, -- 'M', 'F', 'Mixte'
    description TEXT
);

-- Routines des compétiteurs
CREATE TABLE IF NOT EXISTS routines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    competiteur_id INTEGER NOT NULL,
    competition_id INTEGER,
    style_iwuf_id INTEGER NOT NULL,
    arme_id INTEGER,
    type_routine TEXT NOT NULL, -- 'libre', 'imposee', 'duilian', 'groupe'
    duree_prevue REAL, -- en secondes
    nom_routine TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut TEXT DEFAULT 'programmee', -- programmee, en_cours, completee
    FOREIGN KEY (competiteur_id) REFERENCES competiteurs(id),
    FOREIGN KEY (competition_id) REFERENCES competitions(id),
    FOREIGN KEY (style_iwuf_id) REFERENCES styles_iwuf(id),
    FOREIGN KEY (arme_id) REFERENCES armes_iwuf(id)
);

-- Jugements et Scoring IWUF
CREATE TABLE IF NOT EXISTS jugements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    routine_id INTEGER NOT NULL,
    arbitre_id INTEGER,
    juge_numero INTEGER, -- 1-5 juges
    
    -- Scoring groupe A: Difficultés
    score_difficulte_technique REAL DEFAULT 0, -- 0-100
    score_difficulte_connexions REAL DEFAULT 0, -- 0-100
    
    -- Scoring groupe B: Exécution
    score_execution REAL DEFAULT 0, -- 0-100
    
    -- Scoring groupe C: Présentation/Complétion
    score_presentation REAL DEFAULT 0, -- 0-100
    
    -- Scoring final
    score_final REAL DEFAULT 0,
    notes_texte TEXT,
    date_jugement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (routine_id) REFERENCES routines(id),
    FOREIGN KEY (arbitre_id) REFERENCES arbitres(id)
);

-- Arbitres et juges
CREATE TABLE IF NOT EXISTS arbitres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT,
    telephone TEXT,
    niveau_certification TEXT, -- 'International', 'National', 'Regional'
    specialites TEXT, -- Styles spécialisés (JSON array)
    statut TEXT DEFAULT 'actif',
    date_certification DATE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Affectations des arbitres aux compétitions
CREATE TABLE IF NOT EXISTS arbitres_competitions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    arbitre_id INTEGER NOT NULL,
    competition_id INTEGER NOT NULL,
    type_arbitrage TEXT, -- 'juge_technique', 'juge_execution', 'arbitre_principal'
    date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arbitre_id) REFERENCES arbitres(id),
    FOREIGN KEY (competition_id) REFERENCES competitions(id),
    UNIQUE(arbitre_id, competition_id, type_arbitrage)
);

-- Système d'appels et arbitrage
CREATE TABLE IF NOT EXISTS appels (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    competition_id INTEGER NOT NULL,
    routine_id INTEGER NOT NULL,
    equipe_id INTEGER,
    type_appel TEXT NOT NULL, -- 'difficulte', 'execution', 'temps'
    description TEXT NOT NULL,
    montant_appel REAL DEFAULT 200, -- USD par défaut
    statut TEXT DEFAULT 'soumis', -- soumis, en_examen, accepte, rejete
    decision_arbitrage TEXT,
    arbitres_decision TEXT, -- JSON: votes des arbitres
    montant_remboursable REAL DEFAULT 0,
    date_appel DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_decision DATETIME,
    FOREIGN KEY (competition_id) REFERENCES competitions(id),
    FOREIGN KEY (routine_id) REFERENCES routines(id)
);

-- Résultats finaux avec classements IWUF
CREATE TABLE IF NOT EXISTS resultats_iwuf (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    competition_id INTEGER NOT NULL,
    routine_id INTEGER NOT NULL,
    competiteur_id INTEGER NOT NULL,
    category_iwuf_id INTEGER,
    style_iwuf_id INTEGER,
    
    -- Scores finaux (moyenne des juges)
    score_difficulte_final REAL,
    score_execution_final REAL,
    score_presentation_final REAL,
    score_total REAL,
    
    -- Classement
    rang INTEGER,
    medaille TEXT, -- 'or', 'argent', 'bronze'
    points_podium INTEGER,
    
    date_resultat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competition_id) REFERENCES competitions(id),
    FOREIGN KEY (routine_id) REFERENCES routines(id),
    FOREIGN KEY (competiteur_id) REFERENCES competiteurs(id),
    FOREIGN KEY (category_iwuf_id) REFERENCES categories_iwuf(id),
    FOREIGN KEY (style_iwuf_id) REFERENCES styles_iwuf(id)
);

-- Données d'erreurs lors de la notation (pour les statistiques)
CREATE TABLE IF NOT EXISTS erreurs_jugement (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    jugement_id INTEGER,
    type_erreur TEXT, -- 'hors_temps', 'difficulte_non_validee', 'mouvements_interdits'
    points_deduits REAL DEFAULT 0,
    description TEXT,
    date_enregistrement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jugement_id) REFERENCES jugements(id)
);

-- ============================================================================
-- INSERTION DES DONNÉES IWUF STANDARDS
-- ============================================================================

-- Styles IWUF avec durées réglementaires (en minutes)
INSERT OR IGNORE INTO styles_iwuf (nom_style, description, avec_arme, duree_min_self, duree_max_self, duree_min_comp, duree_max_comp) VALUES
('Chang Quan', 'Boxe du Long Poing (Style du Nord)', 1, 2.5, 3.5, 1.0, 1.5),
('Nan Quan', 'Poing du Sud', 1, 2.5, 3.5, 1.0, 1.5),
('Taichi', 'Tai Chi Chuan (Forme Traditionnelle)', 1, 2.75, 3.25, 4.0, 6.0),
('Shaolin', 'Arts Martiaux du Temple Shaolin', 1, 2.5, 3.5, 1.0, 1.5),
('Sanda', 'Combat Libre', 0, NULL, NULL, NULL, NULL);

-- Catégories d'âge IWUF
INSERT OR IGNORE INTO categories_iwuf (nom_categorie, age_min, age_max, sexe, description) VALUES
('Enfant', 5, 8, 'Mixte', 'Enfants jusqu''à 8 ans'),
('Cadet', 9, 14, 'Mixte', 'Cadets 9-14 ans'),
('Junior', 15, 17, 'Mixte', 'Juniors 15-17 ans'),
('Senior', 18, 35, 'Mixte', 'Seniors 18-35 ans'),
('Master 35+', 36, 150, 'Mixte', 'Masters 35 ans et plus');
