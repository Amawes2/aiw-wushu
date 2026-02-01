# Exemples d'URLs YouTube pour les vidéos

## Taolu (Formes)
- Démonstration Taolu complet : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Techniques de base : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Championnat IWUF : https://www.youtube.com/watch?v=dQw4w9WgXcQ

## Sanda (Combat Libre)
- Combat de démonstration : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Techniques de frappe : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Règles et arbitrage : https://www.youtube.com/watch?v=dQw4w9WgXcQ

## Qigong & Taichi
- Taichi 24 mouvements : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Qigong médical : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Méditation énergétique : https://www.youtube.com/watch?v=dQw4w9WgXcQ

## Formes Traditionnelles
- Chen Style Taiji : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Ba Gua Zhang : https://www.youtube.com/watch?v=dQw4w9WgXcQ
- Xing Yi Quan : https://www.youtube.com/watch?v=dQw4w9WgXcQ

## Comment remplacer les vidéos :

1. **Uploader sur YouTube** :
   - Créer une chaîne YouTube pour le club
   - Uploader vos vidéos
   - Copier l'ID de la vidéo (après "v=" dans l'URL)

2. **Remplacer dans le code** :
   ```html
   <!-- Avant -->
   <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" ...>

   <!-- Après -->
   <iframe src="https://www.youtube.com/embed/VOTRE_ID_VIDEO" ...>
   ```

3. **Pour les vidéos locales** :
   - Placer le fichier MP4 dans `media/[discipline]/videos/`
   - Remplacer l'iframe par une balise video :
   ```html
   <video controls>
       <source src="media/taolu/votre-video.mp4" type="video/mp4">
   </video>
   ```