# Gestion des Médias - Wushu Club CI

## 📁 Structure des Médias

```
media/
├── taolu/
│   ├── images/
│   └── videos/
├── sanda/
│   ├── images/
│   └── videos/
├── qigong_taichi/
│   ├── images/
│   └── videos/
└── formes_traditionnelles/
    ├── images/
    └── videos/
```

## 🖼️ Images

### Formats Supportés
- **JPG/JPEG** : Pour les photos de haute qualité
- **PNG** : Pour les images avec transparence
- **WebP** : Format moderne optimisé pour le web

### Nommage des Fichiers
- Utilisez des noms descriptifs en minuscules
- Séparez les mots par des tirets (-)
- Exemples :
  - `taolu-compagnon-competition.jpg`
  - `sanda-combat-championnat.png`
  - `taichi-mouvement-fluide.webp`

### Dimensions Recommandées
- **Galerie photos** : 800x600px minimum, format 4:3
- **Images hero** : 1920x600px minimum, format 16:9
- **Miniatures** : 300x200px, format 3:2

## 🎥 Vidéos

### Formats Supportés
- **MP4** : Format universel pour le web
- **WebM** : Format optimisé pour les navigateurs modernes
- **YouTube/Vimeo** : Intégration directe via iframe

### Durée Recommandée
- **Démonstrations techniques** : 30-60 secondes
- **Combats complets** : 2-3 minutes maximum
- **Tutoriels** : 5-10 minutes

### Hébergement
1. **YouTube/Vimeo** (Recommandé)
   - Upload gratuit et illimité
   - Streaming optimisé
   - Intégration facile via iframe

2. **Serveur local**
   - Stockage dans `media/[discipline]/videos/`
   - Utilisation de la balise `<video>` HTML5

## 📝 Intégration dans les Pages

### Images dans les Pages PHP
```php
<!-- Image avec fallback -->
<img src="media/taolu/<?php echo $imageName; ?>"
     alt="Description de l'image"
     onerror="this.src='https://via.placeholder.com/300x200/333/fff?text=Image+non+trouvée'">
```

### Vidéos YouTube
```html
<div class="video-container">
    <iframe src="https://www.youtube.com/embed/VIDEO_ID"
            frameborder="0"
            allowfullscreen>
    </iframe>
</div>
```

### Vidéos Locales
```html
<video controls poster="media/taolu/affiche-video.jpg">
    <source src="media/taolu/demo-taolu.mp4" type="video/mp4">
    <source src="media/taolu/demo-taolu.webm" type="video/webm">
    Votre navigateur ne supporte pas la lecture vidéo.
</video>
```

## 🛠️ Outils Recommandés

### Édition d'Images
- **GIMP** (Gratuit) : Éditeur professionnel
- **Photoshop** : Logiciel commercial
- **TinyPNG** : Optimisation en ligne

### Édition Vidéo
- **DaVinci Resolve** (Gratuit) : Montage professionnel
- **Shotcut** (Gratuit) : Éditeur open-source
- **CapCut** (Mobile) : Montage rapide

### Conversion Vidéo
- **FFmpeg** : Outil en ligne de commande
- **HandBrake** : Interface graphique
- **CloudConvert** : Conversion en ligne

## 📋 Checklist d'Ajout de Médias

### Pour une Nouvelle Image :
1. ✅ Redimensionner selon les recommandations
2. ✅ Optimiser la taille (max 500KB)
3. ✅ Nommer correctement le fichier
4. ✅ Placer dans le bon dossier
5. ✅ Tester l'affichage dans la page
6. ✅ Vérifier le fallback si nécessaire

### Pour une Nouvelle Vidéo :
1. ✅ Compresser pour le web (max 50MB)
2. ✅ Créer une affiche (thumbnail)
3. ✅ Uploader sur YouTube/Vimeo OU convertir en MP4/WebM
4. ✅ Récupérer l'ID YouTube ou placer le fichier local
5. ✅ Intégrer dans la page correspondante
6. ✅ Tester la lecture sur différents navigateurs

## 🔄 Mise à Jour des Pages

Après ajout de médias, vérifiez que les chemins dans les fichiers PHP sont corrects :

- `taolu.php` → `media/taolu/`
- `sanda.php` → `media/sanda/`
- `qigong_taichi.php` → `media/qigong_taichi/`
- `formes_traditionnelles.php` → `media/formes_traditionnelles/`

## 📞 Support

Pour toute question sur l'ajout de médias, contactez l'administrateur du site.