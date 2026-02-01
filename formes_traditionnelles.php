<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formes Traditionnelles - Wushu Club CI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .discipline-detail {
            padding: 100px 0 50px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .discipline-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/traditionnel-hero.jpg');
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
        .styles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .style-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .style-card:hover {
            transform: translateY(-5px);
        }
        .style-card i {
            font-size: 2em;
            color: #e30613;
            margin-bottom: 10px;
        }
        .style-card h4 {
            color: #333;
            margin-bottom: 10px;
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
                    <i class="fas fa-scroll"></i>
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
            <h1><i class="fas fa-scroll"></i> Formes Traditionnelles (传统套路)</h1>
            <p>L'Héritage Ancestral des Arts Martiaux Chinois</p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="discipline-detail">
        <div class="container">
            <div class="discipline-content">
                <div class="discipline-intro">
                    <h1>Formes Traditionnelles - Le Patrimoine Martial</h1>
                    <p>Les formes traditionnelles représentent l'essence même des arts martiaux chinois ancestraux. Ces séquences codifiées de mouvements transmettent non seulement des techniques de combat, mais aussi une philosophie de vie, une culture et un héritage spirituel millénaire.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-info-circle"></i> Présentation des Formes Traditionnelles</h2>
                    <p>Les formes traditionnelles, ou "Tao Lu" en chinois, sont des séquences chorégraphiées de mouvements martiaux qui constituent le cœur des arts martiaux chinois. Chaque forme raconte une histoire, transmet une stratégie de combat et incarne une philosophie particulière.</p>

                    <div class="content-grid">
                        <div class="content-item">
                            <h4><i class="fas fa-book-open"></i> Définition</h4>
                            <p>Séquences codifiées de mouvements martiaux exécutés dans un ordre précis, combinant attaques, défenses, déplacements et transitions fluides.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-history"></i> Origines</h4>
                            <p>Racines dans les arts martiaux chinois anciens, formalisés durant la dynastie Qing (1644-1912) pour préserver et transmettre les techniques.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-graduation-cap"></i> Transmission</h4>
                            <p>Apprentissage par cœur et répétition, transmission maître-élève préservant l'authenticité des mouvements et leur signification profonde.</p>
                        </div>
                        <div class="content-item">
                            <h4><i class="fas fa-yin-yang"></i> Philosophie</h4>
                            <p>Chaque forme incarne des principes taoïstes : équilibre yin/yang, fluidité du Tao, harmonie entre force et souplesse.</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-calendar-alt"></i> Histoire et Évolution</h2>

                    <h3>Les Origines (Dynasties Anciennes)</h3>
                    <p>Les premières formes martiales apparaissent durant la dynastie Zhou (1046-256 av. J.-C.) avec les "Six Arts Classiques" enseignés aux nobles. Les arts martiaux se développent parallèlement aux philosophies taoïste et confucéenne, intégrant des éléments spirituels et éthiques.</p>

                    <h3>La Codification (Dynastie Qing)</h3>
                    <p>C'est durant la dynastie Qing que les formes traditionnelles prennent leur forme moderne. Les maîtres martiales, face aux persécutions impériales, codifient leurs techniques sous forme de séquences chorégraphiées pour préserver leur art. Cette période voit l'émergence des grandes familles martiales.</p>

                    <h3>La Renaissance Moderne (XXe siècle)</h3>
                    <p>Après la Révolution Culturelle (1966-1976) qui détruisit beaucoup de traditions, les formes anciennes connaissent un renouveau. L'IWUF établit des standards pour la compétition tout en préservant l'authenticité des formes traditionnelles.</p>

                    <h3>Préservation Contemporaine</h3>
                    <p>Aujourd'hui, les formes traditionnelles sont inscrites au patrimoine culturel immatériel de l'UNESCO. Elles continuent d'évoluer tout en préservant leur essence, adaptées aux besoins modernes tout en gardant leur profondeur philosophique.</p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-fist-raised"></i> Principaux Styles Traditionnels</h2>

                    <h3>Arts Martiaux Internes (Nei Jia)</h3>
                    <div class="styles-grid">
                        <div class="style-card">
                            <i class="fas fa-yin-yang"></i>
                            <h4>Taiji Quan</h4>
                            <p>La "Boxe Suprême" - art de l'équilibre parfait entre force et souplesse, méditation en mouvement.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-mountain"></i>
                            <h4>Xing Yi Quan</h4>
                            <p>La "Boxe de la Forme et de l'Intention" - mouvements directs inspirés des cinq éléments naturels.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-water"></i>
                            <h4>Ba Gua Zhang</h4>
                            <p>La "Paume des Huit Trigrammes" - déplacements circulaires autour d'un centre, stratégie défensive.</p>
                        </div>
                    </div>

                    <h3>Arts Martiaux Externes (Wai Jia)</h3>
                    <div class="styles-grid">
                        <div class="style-card">
                            <i class="fas fa-dragon"></i>
                            <h4>Long Quan</h4>
                            <p>La "Boxe du Dragon" - mouvements sinueux et puissants, symbolisant la force et la sagesse.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-tiger"></i>
                            <h4>Hu Quan</h4>
                            <p>La "Boxe du Tigre" - puissance explosive et détermination, représentant la force brute.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-crane"></i>
                            <h4>He Quan</h4>
                            <p>La "Boxe de la Grue" - élégance et précision, symbolisant la grâce et l'équilibre.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-monkey"></i>
                            <h4>Hou Quan</h4>
                            <p>La "Boxe du Singe" - agilité et malice, mouvements imprévisibles et acrobatiques.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-snake"></i>
                            <h4>She Quan</h4>
                            <p>La "Boxe du Serpent" - souplesse et fluidité, attaques indirectes et défenses circulaires.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-horse"></i>
                            <h4>Ma Quan</h4>
                            <p>La "Boxe du Cheval" - stabilité et puissance des postérieurs, charges frontales.</p>
                        </div>
                    </div>

                    <h3>Armes Traditionnelles</h3>
                    <div class="styles-grid">
                        <div class="style-card">
                            <i class="fas fa-sword"></i>
                            <h4>Jian (Épée)</h4>
                            <p>Arme noble et élégante, symbolisant la précision et la rapidité de l'esprit.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-gavel"></i>
                            <h4>Dao (Sabre)</h4>
                            <p>Arme puissante et directe, représentant la force décisive et le courage.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-umbrella"></i>
                            <h4>Gun (Lance)</h4>
                            <p>Arme de longue portée, maîtrisant l'espace et la distance de combat.</p>
                        </div>
                        <div class="style-card">
                            <i class="fas fa-shield"></i>
                            <h4>Qiang (Hallebarde)</h4>
                            <p>Arme polyvalente combinant estoc, taille et parade, symbole de l'équilibre.</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-gavel"></i> Pratique et Compétition</h2>

                    <h3>Structure d'une Forme Traditionnelle</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Ouverture (Kai Shi)</h4>
                            <p>Salutation rituelle et centrage énergétique, établissant la connexion avec l'espace et l'adversaire imaginaire.</p>
                        </div>
                        <div class="content-item">
                            <h4>Corps Central (Zheng Ti)</h4>
                            <p>Séquence principale des mouvements, alternant attaques, défenses, déplacements et transitions harmonieuses.</p>
                        </div>
                        <div class="content-item">
                            <h4>Clôture (Shou Shi)</h4>
                            <p>Retour au calme, recentrage énergétique et salutation finale marquant la fin de la forme.</p>
                        </div>
                        <div class="content-item">
                            <h4>Respiration (Hu Xi)</h4>
                            <p>Coordination parfaite entre mouvement et souffle, cultivant l'énergie interne (Qi) tout au long de la forme.</p>
                        </div>
                    </div>

                    <h3>Critères de Jugement en Compétition</h3>
                    <ul>
                        <li><strong>Technique (Gong Fu) :</strong> Précision des mouvements, puissance, équilibre</li>
                        <li><strong>Style (Feng Ge) :</strong> Authenticité du style pratiqué, respect des traditions</li>
                        <li><strong>Expression (Shen Yun) :</strong> Fluidité, rythme, présence énergétique</li>
                        <li><strong>Présentation (Biao Yan) :</strong> Attitude, concentration, charisme</li>
                        <li><strong>Difficulté (Nan Du) :</strong> Complexité technique et athlétique</li>
                    </ul>

                    <h3>Catégories de Compétition</h3>
                    <p>Les formes traditionnelles sont divisées en plusieurs catégories selon l'arme utilisée :</p>
                    <ul>
                        <li><strong>Sans Arme (Quan Shu) :</strong> Boxe traditionnelle, paumes, poings</li>
                        <li><strong>Armes Courtes (Duan Bing) :</strong> Épée, sabre, bâton court</li>
                        <li><strong>Armes Longues (Chang Bing) :</strong> Lance, hallebarde, bâton long</li>
                        <li><strong>Armes Flexibles (Ruan Bing) :</strong> Chaîne à neuf sections, éventail</li>
                        <li><strong>Armes Doubles (Shuang Bing) :</strong> Deux sabres, deux épées</li>
                    </ul>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-video"></i> Démonstrations Vidéo</h2>
                    <p>Découvrez la beauté et la puissance des formes traditionnelles.</p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Démonstration : Chen Style Taiji Quan - Ancienne forme familiale</em></p>

                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <p><em>Ba Gua Zhang : Marche des Huit Trigrammes avec applications martiales</em></p>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-images"></i> Galerie Photos</h2>
                    <div class="image-gallery">
                        <div class="gallery-item">
                            <img src="images/traditionnel1.jpg" alt="Formes Traditionnelles" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Formes+Traditionnelles'">
                            <p>Pratique collective des formes anciennes</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/traditionnel2.jpg" alt="Arts Martiaux Internes" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Arts+Martiaux+Internes'">
                            <p>Taiji Quan - Mouvements fluides</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/traditionnel3.jpg" alt="Armes Traditionnelles" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Armes+Traditionnelles'">
                            <p>Maîtrise des armes ancestrales</p>
                        </div>
                        <div class="gallery-item">
                            <img src="images/traditionnel4.jpg" alt="Transmission" onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Transmission'">
                            <p>Transmission maître-élève</p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2><i class="fas fa-graduation-cap"></i> Formation et Philosophie</h2>

                    <h3>Les Trois Trésors (San Bao)</h3>
                    <div class="content-grid">
                        <div class="content-item">
                            <h4>Jing (精) - Essence</h4>
                            <p>L'énergie vitale fondamentale, cultivée à travers la pratique régulière et la modération dans tous les aspects de la vie.</p>
                        </div>
                        <div class="content-item">
                            <h4>Qi (气) - Énergie</h4>
                            <p>Le souffle vital qui circule dans le corps, harmonisé par la respiration contrôlée et les mouvements fluides.</p>
                        </div>
                        <div class="content-item">
                            <h4>Shen (神) - Esprit</h4>
                            <p>La conscience éveillée et l'intention claire, développées par la méditation et la concentration profonde.</p>
                        </div>
                    </div>

                    <h3>Principe de Non-Résistance (Wu Wei)</h3>
                    <p>Concept taoïste central : "agir sans agir", utiliser la force de l'adversaire contre lui-même, suivre le cours naturel des choses plutôt que de s'y opposer.</p>

                    <h3>Les Cinq Vertus Cardinales</h3>
                    <ul>
                        <li><strong>Ren (仁) - Humanité :</strong> Compassion et bienveillance envers autrui</li>
                        <li><strong>Yi (义) - Justice :</strong> Sens du devoir et intégrité morale</li>
                        <li><strong>Li (礼) - Rituels :</strong> Respect des traditions et des convenances</li>
                        <li><strong>Zhi (智) - Sagesse :</strong> Discernement et connaissance profonde</li>
                        <li><strong>Xin (信) - Fidélité :</strong> Honnêteté et fiabilité dans les engagements</li>
                    </ul>

                    <h3>Niveaux de Maîtrise</h3>
                    <p>La progression dans les formes traditionnelles suit une hiérarchie spirituelle :</p>
                    <ul>
                        <li><strong>Li (理) - Compréhension Technique :</strong> Apprentissage des mouvements et séquences</li>
                        <li><strong>Qi (气) - Maîtrise Énergétique :</strong> Développement de la force interne</li>
                        <li><strong>Shen (神) - Éveil Spirituel :</strong> Intégration de la philosophie martiale</li>
                        <li><strong>Dao (道) - Unité Cosmique :</strong> Fusion complète avec l'art et l'univers</li>
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