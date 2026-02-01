<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanda - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .discipline-detail {
            padding: 100px 0 50px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .discipline-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/sanda-hero.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            text-align: center;
            color: white;
        }
        .discipline-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }
        .discipline-intro {
            text-align: center;
            margin-bottom: 50px;
        }
        .discipline-intro h1 {
            font-size: 3em;
            color: #e30613;
            margin-bottom: 20px;
        }
        .discipline-intro p {
            font-size: 1.2em;
            line-height: 1.6;
            color: #666;
        }
        .content-section {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .content-section h2 {
            color: #e30613;
            font-size: 2em;
            margin-bottom: 20px;
            border-bottom: 3px solid #e30613;
            padding-bottom: 10px;
        }
        .content-section h3 {
            color: #333;
            font-size: 1.5em;
            margin: 30px 0 15px;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }
        .content-item {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #e30613;
        }
        .content-item h4 {
            color: #e30613;
            margin-bottom: 10px;
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 10px;
            margin: 20px 0;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .gallery-item {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        .rules-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .rules-table th, .rules-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .rules-table th {
            background: #e30613;
            color: white;
        }
        .back-link {
            text-align: center;
            margin-top: 50px;
        }
        .back-link a {
            color: #e30613;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo">
                    <i class="fas fa-fist-raised"></i>
                    <span>Wushu Club CI</span>
                </div>
                <ul class="nav-links">
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="index.html#disciplines">Disciplines</a></li>
                    <li><a href="competitions.php">Compétitions</a></li>
                    <li><a href="clubs.php">Inscriptions</a></li>
                    <li><a href="membre_login.php">Espace Membre</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="discipline-hero">
        <div class="container">
            <h1><i class="fas fa-user-shield"></i> Sanda (散打)</h1>
            <p>Le Combat Libre Chinois Moderne</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="discipline-detail">
        <div class="container">
            <div class="discipline-content">
                <div class="discipline-intro">
                    <h1>Sanda - L'Art du Combat Libre</h1>
                    <p>Le Sanda représente la dimension combative du wushu moderne. Cette discipline de combat libre combine techniques de frappe, projections et contrôles, offrant un système complet d'autodéfense et de compétition sportive.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-info-circle"></i> Présentation du Sanda</h2>
                    <p>Le Sanda (散打), littéralement "combat libre" ou "boxe libre", est la discipline de combat du wushu moderne. Il s'agit d'un art martial complet qui intègre des techniques de poings, pieds, projections et contrôles au sol, pratiqué dans un cadre sportif avec règles de sécurité.</p>

                    <div class="content-grid">
                        <div class="content-item">
                            <h4><i class="fas fa-hand-rock"></i> Définition</h4>
                            <p>Art martial chinois de combat libre opposant deux adversaires dans un espace clos, avec protections et arbitres. Combine striking et grappling.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-history"></i> Origines</h4>
                            <p>Émerge dans les années 1920-1930 en Chine comme évolution des arts martiaux traditionnels vers un sport de combat moderne et réglementé.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-trophy"></i> Compétitions</h4>
                            <p>Sport de combat international avec championnats mondiaux. Catégories de poids, rounds de 2 minutes, système de points.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-shield-alt"></i> Autodéfense</h4>
                            <p>Techniques pratiques d'autodéfense urbaine, efficaces contre plusieurs agresseurs et dans des situations réelles.</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-calendar-alt"></i> Histoire et Évolution</h2>
                    <h3>Les Racines Martiales (1920s-1950s)</h3>
                    <p>Le Sanda trouve ses origines dans les arts martiaux chinois traditionnels. Dans les années 1920, des réformateurs chinois commencent à adapter les techniques martiales ancestrales aux besoins modernes, créant des systèmes de combat plus directs et efficaces.</p>

                    <h3>La Période de Standardisation (1950s-1970s)</h3>
                    <p>Après 1949, le gouvernement chinois promeut le wushu comme sport national. Le Sanda émerge comme discipline de combat libre, inspiré du judo, de la boxe et des arts martiaux locaux. Les premières compétitions organisées datent des années 1950.</p>

                    <h3>L'Ère Internationale (1980s-présent)</h3>
                    <p>Avec l'ouverture de la Chine dans les années 1980, le Sanda se développe internationalement. L'IWUF (1990) établit les règles internationales modernes, intégrant protections avancées et catégories de poids standardisées.</p>

                    <h3>Évolution Technique</h3>
                    <p>Le Sanda a évolué d'un combat traditionnel vers un sport moderne, incorporant des techniques de striking avancées, des projections sophistiquées et des contrôles au sol, tout en maintenant l'essence des arts martiaux chinois.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-gavel"></i> Règles IWUF et Compétitions</h2>
                    <h3>Catégories de Poids</h3>
                    <table class="rules-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Poids (Hommes)</th>
                                <th>Poids (Femmes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Léger</strong></td>
                                <td>56-60 kg</td>
                                <td>48-52 kg</td>
                            </tr>
                            <tr>
                                <td><strong>Moyen</strong></td>
                                <td>65-70 kg</td>
                                <td>52-56 kg</td>
                            </tr>
                            <tr>
                                <td><strong>Lourd</strong></td>
                                <td>75-80 kg</td>
                                <td>60-65 kg</td>
                            </tr>
                            <tr>
                                <td><strong>Super Lourd</strong></td>
                                <td>85-90 kg</td>
                                <td>70+ kg</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3>Règles du Combat</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Durée et Rounds</h4>
                            <ul>
                                <li>2 rounds de 2 minutes chacun</li>
                                <li>1 minute de repos entre rounds</li>
                                <li>Prolongation en cas d'égalité</li>
                                <li>Système de points décisif</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Techniques Autorisées</h4>
                            <ul>
                                <li>Coups de poing et pied</li>
                                <li>Projections et balayages</li>
                                <li>Contrôles et clés articulaires</li>
                                <li>Montées en garde (sans étranglement)</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Équipement de Protection</h4>
                            <ul>
                                <li>Casque avec grille faciale</li>
                                <li>Protège-dents obligatoire</li>
                                <li>Gants de boxe</li>
                                <li>Coquille génitale</li>
                                <li>Protège-tibias et pieds</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Système de Points</h4>
                            <ul>
                                <li>Coup net à la tête : 3 points</li>
                                <li>Coup net au corps : 2 points</li>
                                <li>Projection au sol : 2 points</li>
                                <li>Avertissement : -1 point</li>
                            </ul>
                        </div>
                    </div>

                    <h3>Zones Interdites</h3>
                    <p>Certaines techniques sont strictement interdites pour des raisons de sécurité :</p>
                    <ul>
                        <li>Coups à l'arrière de la tête, nuque et colonne vertébrale</li>
                        <li>Coups de genou au visage</li>
                        <li>Étranglements et clés cervicales</li>
                        <li>Coups dans les yeux, gorge ou parties génitales</li>
                        <li>Morsures, griffures et autres actes antisportifs</li>
                    </ul>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-video"></i> Démonstrations Vidéo</h2>
                    <p>Découvrez l'intensité et la technique du Sanda à travers ces combats de haut niveau.</p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Vidéo de démonstration : Combat Sanda - Championnat du Monde 2023</em></p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Techniques de frappe : Entraînement Sanda professionnel</em></p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-images"></i> Galerie Photos</h2>
                    <div class="image-gallery">
                        <div class="gallery-item">
                            <img src="images/sanda1.jpg" alt="Combat Sanda" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Combat+Sanda'">
                            <p>Combat en cours avec protections</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/sanda2.jpg" alt="Techniques Sanda" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Techniques+Sanda'">
                            <p>Techniques de frappe et projection</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/sanda3.jpg" alt="Équipement Sanda" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Equipement+Sanda'">
                            <p>Équipement de protection complet</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/sanda4.jpg" alt="Entraînement Sanda" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Entrainement+Sanda'">
                            <p>Entraînement intensif</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-graduation-cap"></i> Formation et Pratique</h2>
                    <h3>Avantages du Sanda</h3>
                    <ul>
                        <li><strong>Condition physique :</strong> Développement cardiovasculaire, force et endurance</li>
                        <li><strong>Techniques de combat :</strong> Efficacité en situation réelle d'autodéfense</li>
                        <li><strong>Confiance en soi :</strong> Gestion du stress et des confrontations</li>
                        <li><strong>Discipline mentale :</strong> Concentration et prise de décision rapide</li>
                    </ul>

                    <h3>Niveaux de Pratique</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Débutant</h4>
                            <p>Apprentissage des bases : positionnement, coups simples, esquives. Focus sur la sécurité et la coordination.</p>
                        </div>
                        <div class="content-item">
                            <h4>Intermédiaire</h4>
                            <p>Combinaisons techniques, sparring contrôlé, introduction aux projections et défenses avancées.</p>
                        </div>
                        <div class="content-item">
                            <h4>Avancé</h4>
                            <p>Compétition, stratégies complexes, perfectionnement technique, préparation mentale.</p>
                        </div>
                    </div>

                    <h3>Équipement Requis</h3>
                    <p>Pour la pratique sécurisée du Sanda :</p>
                    <ul>
                        <li>Vêtement d'entraînement confortable</li>
                        <li>Gants de boxe (pour sparring)</li>
                        <li>Protège-tibias et pieds</li>
                        <li>Coquille de protection</li>
                        <li>Boucheur (optionnel pour l'entraînement)</li>
                    </ul>
                </div>

                <div class="back-link">
                    <a href="index.html#disciplines"><i class="fas fa-arrow-left"></i> Retour aux Disciplines</a>
                </div>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
</body>
</html>