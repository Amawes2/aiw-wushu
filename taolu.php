<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taolu - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .discipline-detail {
            padding: 100px 0 50px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .discipline-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/taolu-hero.jpg');
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
            <h1><i class="fas fa-fist-raised"></i> Taolu (套路)</h1>
            <p>L'Art des Formes et des Enchaînements Techniques</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="discipline-detail">
        <div class="container">
            <div class="discipline-content">
                <div class="discipline-intro">
                    <h1>Taolu - L'Art Martial Codifié</h1>
                    <p>Le Taolu représente l'essence artistique du wushu moderne. Cette discipline transforme les techniques de combat en chorégraphies élégantes et précises, combinant grâce, puissance et maîtrise technique.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-info-circle"></i> Présentation du Taolu</h2>
                    <p>Le Taolu (套路), littéralement "forme" ou "enchaînement", est la composante artistique et technique du wushu moderne. Il s'agit d'une séquence codifiée de mouvements martiaux exécutés selon des règles précises, transformant les techniques de combat en une performance artistique.</p>

                    <div class="content-grid">
                        <div class="content-item">
                            <h4><i class="fas fa-star"></i> Définition</h4>
                            <p>Le Taolu est un enchaînement de mouvements martiaux exécutés de manière continue et fluide. Il combine des techniques de poings, de jambes, d'équilibre et parfois d'armes traditionnelles chinoises.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-history"></i> Origines</h4>
                            <p>Bien que les formes martiales existent depuis des siècles en Chine, le Taolu moderne tel que nous le connaissons a été standardisé dans les années 1950 pour les compétitions sportives.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-trophy"></i> Compétitions</h4>
                            <p>Le Taolu est la discipline reine des championnats de wushu. Les compétiteurs sont jugés sur la difficulté technique, la présentation artistique et l'exécution parfaite.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-users"></i> Pratique</h4>
                            <p>Accessible à tous les niveaux, du débutant à l'expert, le Taolu développe la coordination, la mémoire musculaire et la concentration.</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-calendar-alt"></i> Histoire et Évolution</h2>
                    <h3>Les Origines Anciennes</h3>
                    <p>Les formes martiales (Taolu) trouvent leurs racines dans les arts martiaux traditionnels chinois. Dès la dynastie Tang (618-907), des séquences de mouvements codifiées étaient utilisées pour transmettre les techniques martiales de maître à disciple.</p>

                    <h3>La Standardisation Moderne (1950s-1970s)</h3>
                    <p>Dans les années 1950, le gouvernement chinois commence à standardiser les formes martiales pour créer un sport national. Le wushu devient une discipline olympique potentielle, nécessitant des règles claires et des critères d'évaluation objectifs.</p>

                    <h3>L'Ère Contemporaine (1980s-présent)</h3>
                    <p>Avec la création de l'IWUF (International Wushu Federation) en 1990, le Taolu évolue vers une discipline internationale. Les règles se modernisent, intégrant de nouveaux critères techniques et artistiques.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-gavel"></i> Règles IWUF et Compétitions</h2>
                    <h3>Catégories de Compétition</h3>
                    <table class="rules-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Description</th>
                                <th>Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Changquan</strong></td>
                                <td>Forme longue du Nord - Puissance et amplitude</td>
                                <td>1min 20s - 1min 40s</td>
                            </tr>
                            <tr>
                                <td><strong>Nanquan</strong></td>
                                <td>Forme courte du Sud - Précision et rapidité</td>
                                <td>1min 10s - 1min 30s</td>
                            </tr>
                            <tr>
                                <td><strong>Taijiquan</strong></td>
                                <td>Boxe de l'ombre - Fluidité et équilibre</td>
                                <td>4min - 5min</td>
                            </tr>
                            <tr>
                                <td><strong>Armes</strong></td>
                                <td>Sabre, épée, bâton, lance, éventail</td>
                                <td>Variable selon l'arme</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3>Critères d'Évaluation</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Technique (40%)</h4>
                            <ul>
                                <li>Précision des mouvements</li>
                                <li>Maîtrise des techniques</li>
                                <li>Difficulté exécutée</li>
                                <li>Coordination</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Artistique (30%)</h4>
                            <ul>
                                <li>Fluidité des transitions</li>
                                <li>Expression faciale</li>
                                <li>Présentation générale</li>
                                <li>Harmonie du mouvement</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Difficulté (30%)</h4>
                            <ul>
                                <li>Complexité des enchaînements</li>
                                <li>Risques techniques</li>
                                <li>Innovation</li>
                                <li>Niveau d'expertise requis</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-video"></i> Démonstrations Vidéo</h2>
                    <p>Découvrez la beauté et la précision du Taolu à travers ces démonstrations de champions internationaux.</p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Vidéo de démonstration : Changquan masculin - Championnat du Monde 2023</em></p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Techniques avancées : Taolu avec épée Jian - Performance professionnelle</em></p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-images"></i> Galerie Photos</h2>
                    <div class="image-gallery">
                        <div class="gallery-item">
                            <img src="images/taolu1.jpg" alt="Taolu Changquan" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Taolu+Changquan'">
                            <p>Changquan - Forme du Nord</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/taolu2.jpg" alt="Taolu Nanquan" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Taolu+Nanquan'">
                            <p>Nanquan - Forme du Sud</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/taolu3.jpg" alt="Taolu avec armes" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Taolu+Armes'">
                            <p>Taolu avec armes traditionnelles</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/taolu4.jpg" alt="Taolu compétition" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Competition+Taolu'">
                            <p>Compétition internationale</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-graduation-cap"></i> Formation et Pratique</h2>
                    <h3>Avantages du Taolu</h3>
                    <ul>
                        <li><strong>Développement physique :</strong> Améliore la force, la flexibilité et l'équilibre</li>
                        <li><strong>Concentration mentale :</strong> Développe la mémoire et la discipline</li>
                        <li><strong>Expression artistique :</strong> Combine technique et esthétique</li>
                        <li><strong>Préparation compétitive :</strong> Base pour les compétitions internationales</li>
                    </ul>

                    <h3>Niveaux de Pratique</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Débutant</h4>
                            <p>Apprentissage des mouvements de base, coordination et équilibre. Focus sur la précision plutôt que la vitesse.</p>
                        </div>
                        <div class="content-item">
                            <h4>Intermédiaire</h4>
                            <p>Maîtrise des enchaînements complets, introduction aux sauts et techniques complexes.</p>
                        </div>
                        <div class="content-item">
                            <h4>Avancé</h4>
                            <p>Perfectionnement technique, préparation aux compétitions, création de formes personnelles.</p>
                        </div>
                    </div>
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