<?php
/**
 * IWUF Scoring System - Calcul des scores selon les règles IWUF
 * Implémente les 3 groupes de juges (Difficulté, Exécution, Présentation)
 */

class IWUFScoring {
    
    // Groupes de juges IWUF
    const GROUPE_A = 'difficulte';        // Juges de difficulté
    const GROUPE_B = 'execution';         // Juges d'exécution
    const GROUPE_C = 'presentation';      // Juges de présentation

    // Erreurs d'exécution (déductions standards)
    const ERREURS_EXECUTION = [
        'hors_temps_court' => 0.5,        // Temps trop court
        'hors_temps_long' => 0.5,         // Temps trop long
        'sortie_tapis' => 1.0,            // Sorti du tapis
        'mouvements_interdits' => 2.0,    // Mouvements interdits
        'arme_mal_tenue' => 0.5,          // Arme mal tenue
        'costume_non_conforme' => 0.5     // Costume non conforme
    ];

    /**
     * Calcule le score de difficulté (Groupe A)
     * Basé sur : techniques difficiles + connexions difficiles
     */
    public static function calculerDifficulte($techniques_difficiles = 0, $connexions_difficiles = 0, $deductions = 0) {
        // Score max 100 points
        $score = ($techniques_difficiles * 3) + ($connexions_difficiles * 2);
        $score = min(100, $score); // Plafonner à 100

        // Appliquer les déductions
        $score -= $deductions;
        $score = max(0, $score); // Minimum 0

        return round($score, 2);
    }

    /**
     * Calcule le score d'exécution (Groupe B)
     * Basé sur : qualité du mouvement, harmonie, équilibre
     */
    public static function calculerExecution($qualite_mouvement = 100, $deductions = 0) {
        // Commencer à 100 points
        $score = $qualite_mouvement;

        // Appliquer les déductions
        $score -= $deductions;
        $score = max(0, $score); // Minimum 0

        return round($score, 2);
    }

    /**
     * Calcule le score de présentation (Groupe C)
     * Basé sur : présentation générale, costume, musique
     */
    public static function calculerPresentation($presentation_generale = 100, $deductions = 0) {
        // Commencer à 100 points
        $score = $presentation_generale;

        // Appliquer les déductions
        $score -= $deductions;
        $score = max(0, $score); // Minimum 0

        return round($score, 2);
    }

    /**
     * Calcule le score final selon le système IWUF
     * Moyenne des juges pour chaque groupe, puis somme
     */
    public static function calculerScoreFinal($scores_groupe_a = [], $scores_groupe_b = [], $scores_groupe_c = []) {
        // Calculer les moyennes pour chaque groupe
        $moyenne_a = !empty($scores_groupe_a) ? array_sum($scores_groupe_a) / count($scores_groupe_a) : 0;
        $moyenne_b = !empty($scores_groupe_b) ? array_sum($scores_groupe_b) / count($scores_groupe_b) : 0;
        $moyenne_c = !empty($scores_groupe_c) ? array_sum($scores_groupe_c) / count($scores_groupe_c) : 0;

        // Score total = moyenne de tous les groupes
        $total = ($moyenne_a + $moyenne_b + $moyenne_c) / 3;

        return round($total, 2);
    }

    /**
     * Applique les déductions standards selon le type d'erreur
     */
    public static function calculerDeductions($erreurs = []) {
        $total_deductions = 0;

        foreach ($erreurs as $erreur => $nombre) {
            if (isset(self::ERREURS_EXECUTION[$erreur])) {
                $total_deductions += self::ERREURS_EXECUTION[$erreur] * $nombre;
            }
        }

        return $total_deductions;
    }

    /**
     * Contrôle la validité de la durée d'une routine
     */
    public static function validerDuree($duree_reelle, $duree_min, $duree_max) {
        $deduction = 0;

        if ($duree_reelle < $duree_min) {
            // Trop court
            $deduction = 0.5;
        } elseif ($duree_reelle > $duree_max) {
            // Trop long
            $deduction = 0.5;
        }

        return [
            'valide' => $deduction === 0,
            'deduction' => $deduction,
            'message' => $deduction > 0 ? 'Durée non conforme' : 'Durée conforme'
        ];
    }

    /**
     * Gère les cas d'égalité selon les règles IWUF
     * Retourne le classement final après application des critères de départage
     */
    public static function resoudreEgalite($competiteur1, $competiteur2) {
        // Critères de départage IWUF (dans l'ordre):
        // 1. Score de difficulté le plus élevé
        if ($competiteur1['score_difficulte'] != $competiteur2['score_difficulte']) {
            return $competiteur1['score_difficulte'] > $competiteur2['score_difficulte'] ? 1 : 2;
        }

        // 2. Nombre de techniques difficiles réussies
        if ($competiteur1['techniques_difficiles'] != $competiteur2['techniques_difficiles']) {
            return $competiteur1['techniques_difficiles'] > $competiteur2['techniques_difficiles'] ? 1 : 2;
        }

        // 3. Score d'exécution le plus élevé
        if ($competiteur1['score_execution'] != $competiteur2['score_execution']) {
            return $competiteur1['score_execution'] > $competiteur2['score_execution'] ? 1 : 2;
        }

        // 4. Score de présentation le plus élevé
        if ($competiteur1['score_presentation'] != $competiteur2['score_presentation']) {
            return $competiteur1['score_presentation'] > $competiteur2['score_presentation'] ? 1 : 2;
        }

        // 5. Classement en préliminaires (s'il y en a)
        if (isset($competiteur1['rang_preliminaires']) && isset($competiteur2['rang_preliminaires'])) {
            return $competiteur1['rang_preliminaires'] < $competiteur2['rang_preliminaires'] ? 1 : 2;
        }

        // 6. Égalité complète
        return 0; // Égalité
    }

    /**
     * Attire normalisé d'élimination des extrêmes
     * Utilisé avant le calcul de la moyenne (élimine la note la plus haute et la plus basse)
     */
    public static function calculerNormalisee($scores = []) {
        if (count($scores) <= 2) {
            return count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
        }

        // Éliminer la note la plus haute et la plus basse
        sort($scores);
        array_shift($scores); // Élimine le plus bas
        array_pop($scores);   // Élimine le plus haut

        // Moyenne des notes restantes
        $moyenne = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;

        return round($moyenne, 2);
    }

    /**
     * Gère les appels concernant les jugements
     * Retourne si l'appel est valide et les actions à prendre
     */
    public static function validerAppel($type_appel, $routine_data) {
        $appel_valide = false;
        $message = '';

        switch ($type_appel) {
            case 'difficulte':
                // Vérification : la difficulté a-t-elle été mal évaluée ?
                $appel_valide = true;
                $message = 'Appel concernant l\'évaluation de la difficulté';
                break;

            case 'execution':
                // Vérification : l'exécution a-t-elle été mal évaluée ?
                $appel_valide = true;
                $message = 'Appel concernant l\'évaluation de l\'exécution';
                break;

            case 'temps':
                // Vérification : la durée a-t-elle été mal comptée ?
                $appel_valide = true;
                $message = 'Appel concernant la limitation de durée';
                break;

            default:
                $message = 'Type d\'appel non reconnu';
        }

        return [
            'valide' => $appel_valide,
            'message' => $message,
            'type' => $type_appel,
            'montant_appel' => 200 // USD standard
        ];
    }

    /**
     * Génère un rapport détaillé de scoring pour une routine
     */
    public static function genererRapportScoring($routine_data, $jugements) {
        $rapport = [
            'routine_id' => $routine_data['id'],
            'competiteur' => $routine_data['competiteur_nom'],
            'style' => $routine_data['style_nom'],
            'type_routine' => $routine_data['type_routine'],
            'duree_prevue' => $routine_data['duree_prevue'],
            'nombre_juges' => count($jugements),
            'jugements_details' => [],
            'moyennes' => [],
            'score_final' => 0
        ];

        $scores_groupe_a = [];
        $scores_groupe_b = [];
        $scores_groupe_c = [];

        foreach ($jugements as $jugement) {
            $rapport['jugements_details'][] = [
                'juge' => $jugement['juge_nom'],
                'niveau' => $jugement['juge_niveau'],
                'difficulte' => $jugement['score_difficulte_technique'] + $jugement['score_difficulte_connexions'],
                'execution' => $jugement['score_execution'],
                'presentation' => $jugement['score_presentation'],
                'total' => $jugement['score_final']
            ];

            $scores_groupe_a[] = $jugement['score_difficulte_technique'] + $jugement['score_difficulte_connexions'];
            $scores_groupe_b[] = $jugement['score_execution'];
            $scores_groupe_c[] = $jugement['score_presentation'];
        }

        // Calculer les moyennes normalisées (enlever les extrêmes)
        $rapport['moyennes'] = [
            'difficulte' => self::calculerNormalisee($scores_groupe_a),
            'execution' => self::calculerNormalisee($scores_groupe_b),
            'presentation' => self::calculerNormalisee($scores_groupe_c)
        ];

        // Score final
        $rapport['score_final'] = self::calculerScoreFinal($scores_groupe_a, $scores_groupe_b, $scores_groupe_c);

        return $rapport;
    }

    /**
     * Valide que les armes utilisées sont conformes aux normes IWUF
     */
    public static function validerArme($type_arme, $specifications) {
        $normes = [
            'dao' => [
                'longueur_min_cm' => 50,
                'ruban_min_cm' => 30
            ],
            'jian' => [
                'longueur_min_cm' => 50,
                'ruban_min_cm' => 30
            ],
            'gun' => [
                'longueur_min_cm' => 150 // Hauteur du compétiteur
            ],
            'qiang' => [
                'longueur_min_cm' => 200,
                'houppe_min_cm' => 20
            ],
            'shan' => [
                'longueur_min_cm' => 45
            ]
        ];

        if (!isset($normes[$type_arme])) {
            return ['valide' => false, 'erreur' => 'Type d\'arme non reconnu'];
        }

        $norme = $normes[$type_arme];
        foreach ($norme as $spec => $valeur_min) {
            if (isset($specifications[$spec]) && $specifications[$spec] < $valeur_min) {
                return [
                    'valide' => false,
                    'erreur' => "Arme non conforme: $spec insuffisant"
                ];
            }
        }

        return ['valide' => true, 'message' => 'Arme conforme aux normes IWUF'];
    }
}

?>
