<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qigong & Taichi - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .discipline-detail {
            padding: 100px 0 50px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .discipline-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/qigong-hero.jpg');
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
        .practice-sequence {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #4a90e2;
        }
        .sequence-step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .step-number {
            background: #e30613;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
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
                    <i class="fas fa-yin-yang"></i>
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
            <h1><i class="fas fa-yin-yang"></i> Qigong & Taichi (气功 & 太极)</h1>
            <p>L'Art de l'Énergie Vitale et de l'Harmonie</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="discipline-detail">
        <div class="container">
            <div class="discipline-content">
                <div class="discipline-intro">
                    <h1>Qigong & Taichi - La Voie de l'Énergie</h1>
                    <p>Le Qigong et le Taichi représentent l'essence méditative et énergétique du wushu traditionnel. Ces pratiques millénaires harmonisent le corps et l'esprit, cultivent l'énergie vitale (Qi) et favorisent la santé physique et mentale.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-info-circle"></i> Présentation des Disciplines</h2>

                    <h3>Le Qigong (气功) - L'Art de Cultiver l'Énergie</h3>
                    <p>Le Qigong, littéralement "travail de l'énergie", est une pratique ancestrale chinoise qui vise à cultiver, équilibrer et diriger l'énergie vitale (Qi) dans le corps. Cette discipline combine mouvements lents, respiration contrôlée et méditation pour harmoniser le corps et l'esprit.</p>

                    <h3>Le Taichi (太极) - L'Art du Yin et Yang</h3>
                    <p>Le Taichi, ou Taiji Quan, est une forme de méditation en mouvement qui incarne la philosophie du yin et yang. À travers des séquences fluides et circulaires, le pratiquant apprend à équilibrer les forces opposées et complémentaires de l'univers.</p>

                    <div class="content-grid">
                        <div class="content-item">
                            <h4><i class="fas fa-leaf"></i> Philosophie</h4>
                            <p>Harmonie entre corps et esprit, équilibre du yin et yang, circulation fluide de l'énergie Qi à travers les méridiens du corps.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-history"></i> Origines</h4>
                            <p>Pratiques ancestrales chinoises datant de plus de 2000 ans, évoluant des arts martiaux vers des disciplines de santé et longévité.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-heartbeat"></i> Bienfaits Santé</h4>
                            <p>Amélioration cardiovasculaire, réduction du stress, augmentation de la souplesse, renforcement du système immunitaire.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-users"></i> Accessibilité</h4>
                            <p>Pratiquable par tous âges et conditions physiques, adapté aux débutants comme aux pratiquants expérimentés.</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-calendar-alt"></i> Histoire et Évolution</h2>

                    <h3>Les Origines du Qigong (Antiquité - Moyen Âge)</h3>
                    <p>Le Qigong trouve ses racines dans les pratiques taoïstes et bouddhistes de la Chine ancienne. Les premiers textes mentionnant des exercices respiratoires et méditatifs datent du IIIe siècle avant J.-C. Au Moyen Âge, le Qigong se développe comme méthode de longévité et de santé.</p>

                    <h3>L'Âge d'Or du Taichi (XIIIe - XIXe siècle)</h3>
                    <p>Le Taichi émerge au XIIIe siècle avec le moine Zhang Sanfeng, qui aurait observé le combat entre un serpent et un oiseau, inspirant les mouvements fluides du Taichi. Au XIXe siècle, la famille Yang codifie les formes modernes du Taichi Quan.</p>

                    <h3>La Renaissance Moderne (XXe siècle)</h3>
                    <p>Après la Révolution Culturelle, le Qigong et le Taichi connaissent un renouveau. L'IWUF intègre ces disciplines dans le wushu moderne, créant des standards internationaux pour la pratique compétitive et thérapeutique.</p>

                    <h3>Reconnaissance Scientifique</h3>
                    <p>Les études scientifiques modernes confirment les bienfaits du Qigong et Taichi sur la santé : réduction du stress, amélioration de l'équilibre, prévention des chutes chez les seniors, et effets positifs sur diverses pathologies chroniques.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-gavel"></i> Pratiques et Techniques</h2>

                    <h3>Les Styles de Qigong</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Qigong Médical</h4>
                            <p>Exercices thérapeutiques pour traiter diverses affections : hypertension, diabète, problèmes respiratoires, troubles du sommeil.</p>
                        </div>
                        <div class="content-item">
                            <h4>Qigong Martial</h4>
                            <p>Pratiques énergétiques des arts martiaux internes, développant la force interne (Jin) et la sensibilité énergétique.</p>
                        </div>
                        <div class="content-item">
                            <h4>Qigong Spirituel</h4>
                            <p>Méditations profondes et exercices de purification énergétique pour l'éveil spirituel et la réalisation personnelle.</p>
                        </div>
                        <div class="content-item">
                            <h4>Qigong Dynamique</h4>
                            <p>Formes plus actives combinant mouvements, respiration et visualisation pour une circulation énergétique optimale.</p>
                        </div>
                    </div>

                    <h3>Les Styles de Taichi</h3>
                    <p>Le Taichi compte plusieurs écoles majeures :</p>
                    <ul>
                        <li><strong>Style Yang :</strong> Le plus pratiqué mondialement, fluide et accessible</li>
                        <li><strong>Style Chen :</strong> Plus martial avec des mouvements explosifs (fajin)</li>
                        <li><strong>Style Wu :</strong> Élégant et compact, très technique</li>
                        <li><strong>Style Sun :</strong> Intègre des éléments d'autres arts martiaux</li>
                        <li><strong>Style Hao :</strong> Rare et très traditionnel</li>
                    </ul>

                    <h3>Séquence de Pratique du Taichi</h3>
                    <div class="practice-sequence">
                        <div class="sequence-step">
                            <div class="step-number">1</div>
                            <div>
                                <strong>Préparation (Wu Ji) :</strong> Debout immobile, respiration naturelle, centrage de l'esprit.
                            </div>
                        </div>
                        <div class="sequence-step">
                            <div class="step-number">2</div>
                            <div>
                                <strong>Ouverture (Tai Ji) :</strong> Séparation du yin et yang, premiers mouvements circulaires.
                            </div>
                        </div>
                        <div class="sequence-step">
                            <div class="step-number">3</div>
                            <div>
                                <strong>Forme Simple :</strong> Apprentissage des postures de base et transitions fluides.
                            </div>
                        </div>
                        <div class="sequence-step">
                            <div class="step-number">4</div>
                            <div>
                                <strong>Forme Complète :</strong> Enchaînement de 24, 48 ou 108 mouvements selon le style.
                            </div>
                        </div>
                        <div class="sequence-step">
                            <div class="step-number">5</div>
                            <div>
                                <strong>Clôture :</strong> Retour au calme, recentrage énergétique.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-video"></i> Démonstrations Vidéo</h2>
                    <p>Découvrez la grâce et la fluidité du Qigong et Taichi.</p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Démonstration : Taichi Style Yang - Forme des 24 mouvements</em></p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Qigong Médical : Exercices pour la santé et la longévité</em></p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-images"></i> Galerie Photos</h2>
                    <div class="image-gallery">
                        <div class="gallery-item">
                            <img src="images/taichi1.jpg" alt="Pratique Taichi" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Pratique+Taichi'">
                            <p>Pratique collective du Taichi</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/qigong1.jpg" alt="Exercices Qigong" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Exercices+Qigong'">
                            <p>Exercices de Qigong énergétique</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/taichi2.jpg" alt="Mouvements Taichi" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Mouvements+Taichi'">
                            <p>Mouvements fluides du Taichi</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/qigong2.jpg" alt="Méditation Qigong" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Meditation+Qigong'">
                            <p>Méditation et respiration</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-graduation-cap"></i> Formation et Bienfaits</h2>

                    <h3>Bienfaits pour la Santé</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Physiques</h4>
                            <ul>
                                <li>Amélioration de la souplesse articulaire</li>
                                <li>Renforcement musculaire harmonieux</li>
                                <li>Meilleur équilibre et coordination</li>
                                <li>Prévention des chutes (seniors)</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Mentaux</h4>
                            <ul>
                                <li>Réduction significative du stress</li>
                                <li>Amélioration de la concentration</li>
                                <li>Meilleure gestion des émotions</li>
                                <li>Développement de la pleine conscience</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Énergétiques</h4>
                            <ul>
                                <li>Circulation harmonieuse du Qi</li>
                                <li>Équilibre yin/yang</li>
                                <li>Renforcement du système immunitaire</li>
                                <li>Prévention des maladies chroniques</li>
                            </ul>
                        </div>
                        <div class="content-item">
                            <h4>Sociaux</h4>
                            <ul>
                                <li>Pratique en groupe favorisant les liens</li>
                                <li>Transmission intergénérationnelle</li>
                                <li>Intégration culturelle</li>
                                <li>Partage de valeurs traditionnelles</li>
                            </ul>
                        </div>
                    </div>

                    <h3>Niveaux de Pratique</h3>
                    <ul>
                        <li><strong>Débutant :</strong> Apprentissage des mouvements de base, respiration consciente</li>
                        <li><strong>Intermédiaire :</strong> Maîtrise des séquences complètes, compréhension énergétique</li>
                        <li><strong>Avancé :</strong> Pratique spontanée (Wu Wei), enseignement, applications martiales</li>
                        <li><strong>Expert :</strong> Maîtrise interne, transmission spirituelle, innovation stylistique</li>
                    </ul>

                    <h3>Équipement et Conditions</h3>
                    <p>Le Qigong et Taichi se pratiquent généralement :</p>
                    <ul>
                        <li>Vêtements amples et confortables</li>
                        <li>Chaussures plates ou pieds nus</li>
                        <li>Espace ouvert (intérieur/extérieur)</li>
                        <li>Tapis de sol (optionnel)</li>
                        <li>Environnement calme et naturel</li>
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