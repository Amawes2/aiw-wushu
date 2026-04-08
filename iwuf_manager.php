<?php
/**
 * IWUF Manager - Gestion des routines et compétitions selon les règles IWUF
 * Phase 1: Routines, Scoring, Arbitrage
 */

class IWUFManager {
    private $pdo;
    private $db_file = 'wushuclubci.db';

    public function __construct() {
        try {
            $this->pdo = new PDO("sqlite:" . $this->db_file);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de base de données : " . $e->getMessage());
        }
    }

    // ========================================================================
    // GESTION DES STYLES IWUF
    // ========================================================================

    /**
     * Récupère tous les styles IWUF disponibles
     */
    public function getStylesIWUF() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM styles_iwuf ORDER BY nom_style");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getStylesIWUF: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les détails d'un style IWUF
     */
    public function getStyleIWUF($style_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM styles_iwuf WHERE id = ?");
            $stmt->execute([$style_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getStyleIWUF: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les armes disponibles pour un style
     */
    public function getArmesParStyle($style_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM armes_iwuf 
                WHERE style_id = ? 
                ORDER BY nom_arme
            ");
            $stmt->execute([$style_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getArmesParStyle: " . $e->getMessage());
            return [];
        }
    }

    // ========================================================================
    // GESTION DES CATÉGORIES
    // ========================================================================

    /**
     * Récupère toutes les catégories IWUF
     */
    public function getCategoriesIWUF() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM categories_iwuf 
                ORDER BY age_min ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getCategoriesIWUF: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Détermine la catégorie IWUF selon l'âge du compétiteur
     */
    public function determineCategorieIWUF($date_naissance) {
        try {
            $today = new DateTime();
            $birthDate = new DateTime($date_naissance);
            $age = $today->diff($birthDate)->y;

            $stmt = $this->pdo->prepare("
                SELECT * FROM categories_iwuf 
                WHERE age_min <= ? AND age_max >= ?
                LIMIT 1
            ");
            $stmt->execute([$age, $age]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur determineCategorieIWUF: " . $e->getMessage());
            return null;
        }
    }

    // ========================================================================
    // GESTION DES ROUTINES
    // ========================================================================

    /**
     * Crée une nouvelle routine pour un compétiteur
     */
    public function creerRoutine($competiteur_id, $competition_id, $style_iwuf_id, $arme_id = null, $type_routine = 'libre') {
        try {
            // Obtenir la durée prévue selon le style et le type
            $dureePrevue = $this->getDureePrevueRoutine($style_iwuf_id, $type_routine);

            $stmt = $this->pdo->prepare("
                INSERT INTO routines (competiteur_id, competition_id, style_iwuf_id, arme_id, type_routine, duree_prevue)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([
                $competiteur_id,
                $competition_id,
                $style_iwuf_id,
                $arme_id,
                $type_routine,
                $dureePrevue
            ]);

            if ($success) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur creerRoutine: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère la durée prévue pour une routine selon IWUF
     */
    private function getDureePrevueRoutine($style_id, $type_routine) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM styles_iwuf WHERE id = ?");
            $stmt->execute([$style_id]);
            $style = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$style) return 180; // 3 minutes par défaut

            if ($type_routine === 'libre') {
                $min = $style['duree_min_self'];
                $max = $style['duree_max_self'];
            } else {
                $min = $style['duree_min_comp'];
                $max = $style['duree_max_comp'];
            }

            // Retourne la moyenne en secondes
            return ($min + $max) / 2 * 60;
        } catch (PDOException $e) {
            error_log("Erreur getDureePrevueRoutine: " . $e->getMessage());
            return 180;
        }
    }

    /**
     * Récupère les routines d'une compétition
     */
    public function getRoutinesCompetition($competition_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT r.*, 
                       c.nom, c.prenom,
                       s.nom_style,
                       a.nom_arme
                FROM routines r
                LEFT JOIN competiteurs c ON r.competiteur_id = c.id
                LEFT JOIN styles_iwuf s ON r.style_iwuf_id = s.id
                LEFT JOIN armes_iwuf a ON r.arme_id = a.id
                WHERE r.competition_id = ?
                ORDER BY c.nom, c.prenom
            ");
            $stmt->execute([$competition_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRoutinesCompetition: " . $e->getMessage());
            return [];
        }
    }

    // ========================================================================
    // GESTION DES JUGEMENTS ET SCORING
    // ========================================================================

    /**
     * Crée un nouveau jugement (par un arbitre)
     */
    public function creerJugement($routine_id, $arbitre_id, $juge_numero) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO jugements (routine_id, arbitre_id, juge_numero, score_final)
                VALUES (?, ?, ?, 0)
            ");
            
            $success = $stmt->execute([$routine_id, $arbitre_id, $juge_numero]);

            if ($success) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur creerJugement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour les scores d'un jugement
     */
    public function mettreAJourScores($jugement_id, $scores) {
        try {
            // Calculer le score final selon IWUF
            $score_final = $this->calculerScoreFinal($scores);

            $stmt = $this->pdo->prepare("
                UPDATE jugements SET
                    score_difficulte_technique = ?,
                    score_difficulte_connexions = ?,
                    score_execution = ?,
                    score_presentation = ?,
                    score_final = ?
                WHERE id = ?
            ");

            return $stmt->execute([
                $scores['difficulte_technique'] ?? 0,
                $scores['difficulte_connexions'] ?? 0,
                $scores['execution'] ?? 0,
                $scores['presentation'] ?? 0,
                $score_final,
                $jugement_id
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mettreAJourScores: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcule le score final IWUF
     * Difficulté + Exécution + Présentation
     */
    private function calculerScoreFinal($scores) {
        $difficulte = ($scores['difficulte_technique'] ?? 0) + ($scores['difficulte_connexions'] ?? 0);
        $execution = $scores['execution'] ?? 0;
        $presentation = $scores['presentation'] ?? 0;

        // Score total sur 100 points
        return min(100, $difficulte + $execution + $presentation);
    }

    /**
     * Calcule le score moyen pour une routine (moyenne de tous les juges)
     */
    public function calculerScoreMoyenRoutine($routine_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    AVG(score_difficulte_technique) as diff_tech,
                    AVG(score_difficulte_connexions) as diff_conn,
                    AVG(score_execution) as execution,
                    AVG(score_presentation) as presentation,
                    AVG(score_final) as final
                FROM jugements
                WHERE routine_id = ?
            ");
            $stmt->execute([$routine_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur calculerScoreMoyenRoutine: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère tous les jugements d'une routine
     */
    public function getJugementsRoutine($routine_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT j.*, 
                       a.nom, a.prenom, a.niveau_certification
                FROM jugements j
                LEFT JOIN arbitres a ON j.arbitre_id = a.id
                WHERE j.routine_id = ?
                ORDER BY j.juge_numero
            ");
            $stmt->execute([$routine_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getJugementsRoutine: " . $e->getMessage());
            return [];
        }
    }

    // ========================================================================
    // GESTION DES APPELS
    // ========================================================================

    /**
     * Soumet un appel (maximum 2 par équipe)
     */
    public function soumettreAppel($competition_id, $routine_id, $equipe_id, $type_appel, $description) {
        try {
            // Vérifier que l'équipe ne dépasse pas 2 appels
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as nb_appels FROM appels
                WHERE competition_id = ? AND equipe_id = ?
            ");
            $stmt->execute([$competition_id, $equipe_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['nb_appels'] >= 2) {
                return ['success' => false, 'error' => 'Nombre maximum d\'appels atteint (2 par équipe)'];
            }

            // Créer l'appel
            $stmt = $this->pdo->prepare("
                INSERT INTO appels (competition_id, routine_id, equipe_id, type_appel, description, statut)
                VALUES (?, ?, ?, ?, ?, 'soumis')
            ");

            $success = $stmt->execute([
                $competition_id,
                $routine_id,
                $equipe_id,
                $type_appel,
                $description
            ]);

            if ($success) {
                return ['success' => true, 'appel_id' => $this->pdo->lastInsertId()];
            }
            return ['success' => false, 'error' => 'Erreur lors de la création de l\'appel'];
        } catch (PDOException $e) {
            error_log("Erreur soumettreAppel: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Récupère les appels en attente d'une compétition
     */
    public function getAppelsEnAttente($competition_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, 
                       c.nom, c.prenom,
                       s.nom_style
                FROM appels a
                LEFT JOIN routines r ON a.routine_id = r.id
                LEFT JOIN competiteurs c ON r.competiteur_id = c.id
                LEFT JOIN styles_iwuf s ON r.style_iwuf_id = s.id
                WHERE a.competition_id = ? AND a.statut = 'soumis'
                ORDER BY a.date_appel DESC
            ");
            $stmt->execute([$competition_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAppelsEnAttente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Décide d'un appel (accepté ou rejeté)
     */
    public function deciderAppel($appel_id, $decision, $montant_rembourse = 0, $arbitres_decision = null) {
        try {
            $statut = ($decision === 'accepte') ? 'accepte' : 'rejete';
            $montant = ($decision === 'accepte') ? $montant_rembourse : 0;

            $stmt = $this->pdo->prepare("
                UPDATE appels SET
                    statut = ?,
                    decision_arbitrage = ?,
                    montant_remboursable = ?,
                    arbitres_decision = ?,
                    date_decision = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            return $stmt->execute([
                $statut,
                $decision,
                $montant,
                $arbitres_decision ? json_encode($arbitres_decision) : null,
                $appel_id
            ]);
        } catch (PDOException $e) {
            error_log("Erreur deciderAppel: " . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // GESTION DES RÉSULTATS
    // ========================================================================

    /**
     * Enregistre les résultats finaux d'une routine
     */
    public function enregistrerResultat($routine_id, $competiteur_id, $competition_id, $scores_moyens) {
        try {
            // Récupérer les infos de la routine
            $routine = $this->getRoutineDetails($routine_id);
            if (!$routine) {
                return false;
            }

            // Récupérer la catégorie IWUF du compétiteur
            $categorie_iwuf = $this->determineCategorieIWUF(
                $this->getCompetiteurDateNaissance($competiteur_id)
            );

            $stmt = $this->pdo->prepare("
                INSERT INTO resultats_iwuf 
                (competition_id, routine_id, competiteur_id, category_iwuf_id, style_iwuf_id, 
                 score_difficulte_final, score_execution_final, score_presentation_final, score_total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $competition_id,
                $routine_id,
                $competiteur_id,
                $categorie_iwuf['id'] ?? null,
                $routine['style_iwuf_id'],
                $scores_moyens['diff_tech'] ?? 0,
                $scores_moyens['execution'] ?? 0,
                $scores_moyens['presentation'] ?? 0,
                $scores_moyens['final'] ?? 0
            ]);
        } catch (PDOException $e) {
            error_log("Erreur enregistrerResultat: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les détails d'une routine
     */
    private function getRoutineDetails($routine_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM routines WHERE id = ?");
            $stmt->execute([$routine_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRoutineDetails: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère la date de naissance d'un compétiteur
     */
    private function getCompetiteurDateNaissance($competiteur_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT date_naissance FROM competiteurs WHERE id = ?");
            $stmt->execute([$competiteur_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['date_naissance'] ?? null;
        } catch (PDOException $e) {
            error_log("Erreur getCompetiteurDateNaissance: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les résultats finaux d'une compétition
     */
    public function getResultatsCompetition($competition_id, $categorie_id = null) {
        try {
            $sql = "
                SELECT r.*, 
                       c.nom, c.prenom,
                       ca.nom_categorie,
                       s.nom_style
                FROM resultats_iwuf r
                LEFT JOIN competiteurs c ON r.competiteur_id = c.id
                LEFT JOIN categories_iwuf ca ON r.category_iwuf_id = ca.id
                LEFT JOIN styles_iwuf s ON r.style_iwuf_id = s.id
                WHERE r.competition_id = ?
            ";

            $params = [$competition_id];

            if ($categorie_id) {
                $sql .= " AND r.category_iwuf_id = ?";
                $params[] = $categorie_id;
            }

            $sql .= " ORDER BY r.category_iwuf_id, r.score_total DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getResultatsCompetition: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Attribue les médailles selon les résultats
     */
    public function attribuerMedailles($competition_id, $categorie_id = null) {
        try {
            $resultats = $this->getResultatsCompetition($competition_id, $categorie_id);

            $rang = 1;
            $medailles = ['Or', 'Argent', 'Bronze'];
            $rangs_traites = [];

            foreach ($resultats as $resultat) {
                $medaille = isset($medailles[$rang - 1]) ? $medailles[$rang - 1] : null;
                $points = (3 - $rang + 1) > 0 ? (3 - $rang + 1) : 0; // Or=3, Argent=2, Bronze=1

                $stmt = $this->pdo->prepare("
                    UPDATE resultats_iwuf SET
                        rang = ?,
                        medaille = ?,
                        points_podium = ?
                    WHERE id = ?
                ");

                $stmt->execute([$rang, $medaille, $points, $resultat['id']]);

                if ($rang < 3) $rang++;
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur attribuerMedailles: " . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // GESTION DES ARBITRES
    // ========================================================================

    /**
     * Récupère tous les arbitres disponibles
     */
    public function getArbitres($niveau = null) {
        try {
            $sql = "SELECT * FROM arbitres WHERE statut = 'actif'";
            $params = [];

            if ($niveau) {
                $sql .= " AND niveau_certification = ?";
                $params[] = $niveau;
            }

            $sql .= " ORDER BY nom, prenom";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getArbitres: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Affecte un arbitre à une compétition
     */
    public function affecterArbitreCompetition($arbitre_id, $competition_id, $type_arbitrage) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT OR IGNORE INTO arbitres_competitions (arbitre_id, competition_id, type_arbitrage)
                VALUES (?, ?, ?)
            ");

            return $stmt->execute([$arbitre_id, $competition_id, $type_arbitrage]);
        } catch (PDOException $e) {
            error_log("Erreur affecterArbitreCompetition: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les arbitres affectés à une compétition
     */
    public function getArbitresCompetition($competition_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ac.*, a.nom, a.prenom, a.niveau_certification
                FROM arbitres_competitions ac
                JOIN arbitres a ON ac.arbitre_id = a.id
                WHERE ac.competition_id = ?
                ORDER BY ac.type_arbitrage, a.nom
            ");
            $stmt->execute([$competition_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getArbitresCompetition: " . $e->getMessage());
            return [];
        }
    }
}

?>
