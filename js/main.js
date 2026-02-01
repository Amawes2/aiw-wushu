/**
 * Wushu Club CI - Fédération Ivoirienne des Arts Martiaux Chinois
 * Fichier JavaScript principal pour l'interactivité du site
 * Version 1.0 - 31 Janvier 2026
 */

// ==========================================
// GESTION DU DOM ET UTILITAIRES
// ==========================================

/**
 * Classe utilitaire pour les opérations DOM communes
 */
class WushuClubCIDom {
    /**
     * Sélectionne un élément par sélecteur CSS
     */
    static $(selector, context = document) {
        return context.querySelector(selector);
    }

    /**
     * Sélectionne tous les éléments par sélecteur CSS
     */
    static $$(selector, context = document) {
        return Array.from(context.querySelectorAll(selector));
    }

    /**
     * Ajoute une classe CSS à un élément
     */
    static addClass(element, className) {
        element.classList.add(className);
    }

    /**
     * Supprime une classe CSS d'un élément
     */
    static removeClass(element, className) {
        element.classList.remove(className);
    }

    /**
     * Bascule une classe CSS sur un élément
     */
    static toggleClass(element, className) {
        element.classList.toggle(className);
    }

    /**
     * Vérifie si un élément a une classe CSS
     */
    static hasClass(element, className) {
        return element.classList.contains(className);
    }
}

// ==========================================
// VALIDATION DE FORMULAIRES
// ==========================================

/**
 * Classe pour la validation des formulaires
 */
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = {};
        this.init();
    }

    init() {
        // Validation en temps réel
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });

        // Validation à la soumission
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                this.showErrors();
            }
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        let isValid = true;
        let errorMessage = '';

        switch (name) {
            case 'nom':
            case 'prenom':
                if (!value) {
                    errorMessage = 'Ce champ est requis';
                    isValid = false;
                } else if (value.length < 2) {
                    errorMessage = 'Minimum 2 caractères';
                    isValid = false;
                }
                break;

            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!value) {
                    errorMessage = 'L\'email est requis';
                    isValid = false;
                } else if (!emailRegex.test(value)) {
                    errorMessage = 'Format d\'email invalide';
                    isValid = false;
                }
                break;

            case 'telephone':
                const phoneRegex = /^[0-9+\-\s()]+$/;
                if (!value) {
                    errorMessage = 'Le téléphone est requis';
                    isValid = false;
                } else if (!phoneRegex.test(value)) {
                    errorMessage = 'Format de téléphone invalide';
                    isValid = false;
                }
                break;

            case 'date_naissance':
                if (!value) {
                    errorMessage = 'La date de naissance est requise';
                    isValid = false;
                } else {
                    const birthDate = new Date(value);
                    const today = new Date();
                    const age = today.getFullYear() - birthDate.getFullYear();
                    if (age < 8 || age > 100) {
                        errorMessage = 'Âge invalide (8-100 ans)';
                        isValid = false;
                    }
                }
                break;
        }

        if (!isValid) {
            this.errors[name] = errorMessage;
            this.showFieldError(field, errorMessage);
        } else {
            delete this.errors[name];
            this.clearFieldError(field);
        }

        return isValid;
    }

    validateForm() {
        this.errors = {};
        const inputs = this.form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showFieldError(field, message) {
        // Supprimer l'ancien message d'erreur
        this.clearFieldError(field);

        // Ajouter la classe d'erreur
        WushuClubCIDom.addClass(field, 'field-error');

        // Créer et afficher le message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error-message';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #e30613;
            font-size: 0.8em;
            margin-top: 5px;
            display: block;
        `;

        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }

    clearFieldError(field) {
        WushuClubCIDom.removeClass(field, 'field-error');
        const errorMessage = field.parentNode.querySelector('.field-error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }

    showErrors() {
        // Afficher un résumé des erreurs en haut du formulaire
        const errorSummary = Object.values(this.errors).join('<br>');
        if (errorSummary) {
            NotificationManager.show(errorSummary, 'error');
        }
    }
}

// ==========================================
// ANIMATIONS ET SCROLL
// ==========================================

/**
 * Classe pour gérer les animations au scroll
 */
class ScrollAnimator {
    constructor() {
        this.animatedElements = FIAMCDom.$$('[data-animate]');
        this.init();
    }

    init() {
        // Déclencher immédiatement les animations pour les éléments visibles
        setTimeout(() => this.checkScroll(), 100);
        window.addEventListener('scroll', () => this.checkScroll());
    }

    checkScroll() {
        const scrollTop = window.pageYOffset;
        const windowHeight = window.innerHeight;

        this.animatedElements.forEach(element => {
            const elementTop = element.offsetTop;
            const elementHeight = element.offsetHeight;
            const animationType = element.dataset.animate;

            if (scrollTop + windowHeight > elementTop + elementHeight / 4) {
                // Vérifier si l'animation n'a pas déjà été appliquée
                if (!element.classList.contains(`animate-${animationType}`)) {
                    FIAMCDom.addClass(element, `animate-${animationType}`);
                    
                    // Appliquer le délai d'animation si défini
                    const delay = element.style.animationDelay;
                    if (delay) {
                        element.style.animationDelay = delay;
                    }
                }
            }
        });
    }
}

// ==========================================
// NOTIFICATIONS
// ==========================================

/**
 * Gestionnaire de notifications toast
 */
class NotificationManager {
    static init() {
        // Créer le conteneur de notifications
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }

    static show(message, type = 'info', duration = 5000) {
        const container = FIAMCDom.$('#notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            background: ${this.getBackgroundColor(type)};
            color: white;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        `;

        notification.innerHTML = `
            <i class="${this.getIcon(type)}" style="margin-right: 10px;"></i>
            ${message}
            <button onclick="this.parentElement.remove()" style="
                position: absolute;
                top: 5px;
                right: 10px;
                background: none;
                border: none;
                color: white;
                font-size: 20px;
                cursor: pointer;
                opacity: 0.7;
            ">&times;</button>
        `;

        container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Auto-suppression
        if (duration > 0) {
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }
            }, duration);
        }

        // Fermeture au clic
        notification.addEventListener('click', () => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        });
    }

    static getBackgroundColor(type) {
        switch (type) {
            case 'success': return '#28a745';
            case 'error': return '#dc3545';
            case 'warning': return '#ffc107';
            case 'info': default: return '#17a2b8';
        }
    }

    static getIcon(type) {
        switch (type) {
            case 'success': return 'fas fa-check-circle';
            case 'error': return 'fas fa-exclamation-circle';
            case 'warning': return 'fas fa-exclamation-triangle';
            case 'info': default: return 'fas fa-info-circle';
        }
    }
}

// ==========================================
// FILTRES ET RECHERCHE
// ==========================================

/**
 * Classe pour filtrer et rechercher dans les listes
 */
class FilterManager {
    constructor(container, filterSelector, searchSelector) {
        this.container = container;
        this.items = FIAMCDom.$$('.competition-card, .competiteur-item', container);
        this.filterSelect = FIAMCDom.$(filterSelector);
        this.searchInput = FIAMCDom.$(searchSelector);

        if (this.filterSelect) {
            this.filterSelect.addEventListener('change', () => this.filter());
        }

        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => this.search());
        }
    }

    filter() {
        const filterValue = this.filterSelect.value;
        this.items.forEach(item => {
            const itemStatus = item.dataset.status || '';
            const shouldShow = !filterValue || itemStatus === filterValue;
            item.style.display = shouldShow ? 'block' : 'none';
        });
    }

    search() {
        const searchTerm = this.searchInput.value.toLowerCase();
        this.items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const shouldShow = !searchTerm || text.includes(searchTerm);
            item.style.display = shouldShow ? 'block' : 'none';
        });
    }
}

// ==========================================
// MENU MOBILE
// ==========================================

/**
 * Gestion du menu mobile
 */
class MobileMenu {
    constructor() {
        this.menuToggle = FIAMCDom.$('.menu-toggle');
        this.nav = FIAMCDom.$('.main-nav');
        this.init();
    }

    init() {
        if (this.menuToggle && this.nav) {
            this.menuToggle.addEventListener('click', () => this.toggle());
        }

        // Fermer le menu en cliquant en dehors
        document.addEventListener('click', (e) => {
            if (!this.nav.contains(e.target) && !this.menuToggle.contains(e.target)) {
                this.close();
            }
        });

        // Fermer le menu à la redimension
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                this.close();
            }
        });
    }

    toggle() {
        FIAMCDom.toggleClass(this.nav, 'mobile-open');
    }

    close() {
        FIAMCDom.removeClass(this.nav, 'mobile-open');
    }
}

// ==========================================
// CARROUSEL D'IMAGES
// ==========================================

/**
 * Carrousel d'images pour la galerie
 */
class ImageCarousel {
    constructor(container) {
        this.container = container;
        this.images = FIAMCDom.$$('img', container);
        this.currentIndex = 0;
        this.autoPlayInterval = null;
        this.init();
    }

    init() {
        if (this.images.length > 1) {
            this.createControls();
            this.showImage(0);
            this.startAutoPlay();
        }
    }

    createControls() {
        const controls = document.createElement('div');
        controls.className = 'carousel-controls';
        controls.style.cssText = `
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        `;

        // Boutons précédent/suivant
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.style.cssText = `
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        `;
        prevBtn.addEventListener('click', () => this.prev());

        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.style.cssText = prevBtn.style.cssText;
        nextBtn.addEventListener('click', () => this.next());

        controls.appendChild(prevBtn);
        controls.appendChild(nextBtn);
        this.container.style.position = 'relative';
        this.container.appendChild(controls);
    }

    showImage(index) {
        this.images.forEach((img, i) => {
            img.style.display = i === index ? 'block' : 'none';
        });
        this.currentIndex = index;
    }

    next() {
        const nextIndex = (this.currentIndex + 1) % this.images.length;
        this.showImage(nextIndex);
    }

    prev() {
        const prevIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.showImage(prevIndex);
    }

    startAutoPlay() {
        this.autoPlayInterval = setInterval(() => this.next(), 5000);
    }

    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
        }
    }
}

// ==========================================
// LAZY LOADING DES IMAGES
// ==========================================

/**
 * Lazy loading des images pour améliorer les performances
 */
class LazyLoader {
    constructor() {
        this.images = FIAMCDom.$$('img[data-src]');
        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            this.images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback pour les navigateurs sans IntersectionObserver
            this.loadAllImages();
        }
    }

    loadAllImages() {
        this.images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy');
        });
    }
}

// ==========================================
// INITIALISATION
// ==========================================

/**
 * Initialisation de toutes les fonctionnalités JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le gestionnaire de notifications
    NotificationManager.init();

    // Initialiser les validateurs de formulaires
    FIAMCDom.$$('form').forEach(form => {
        new FormValidator(form);
    });

    // Initialiser les animations au scroll
    new ScrollAnimator();

    // Initialiser le menu mobile
    new MobileMenu();

    // Initialiser le lazy loading
    new LazyLoader();

    // Initialiser les carrousels
    FIAMCDom.$$('.gallery-carousel').forEach(container => {
        new ImageCarousel(container);
    });

    // Initialiser les filtres
    if (FIAMCDom.$('.competitions-grid')) {
        new FilterManager(
            FIAMCDom.$('.competitions-grid'),
            '#competition-filter',
            '#competition-search'
        );
    }

    // Initialiser les notifications temps réel
    new RealTimeNotifications();

    // Messages de succès/erreur depuis PHP
    const successMessage = FIAMCDom.$('.alert-success');
    const errorMessage = FIAMCDom.$('.alert-error');

    if (successMessage) {
        NotificationManager.show(successMessage.textContent, 'success');
    }

    if (errorMessage) {
        NotificationManager.show(errorMessage.textContent, 'error');
    }

    // Animation des cartes au survol
    FIAMCDom.$$('.competition-card, .stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    console.log('🚀 FIAMC JavaScript chargé avec succès !');
});

// ==========================================
// FONCTIONS UTILITAIRES GLOBALES
// ==========================================

/**
 * Système de notifications en temps réel avec WebSocket
 */
class RealTimeNotifications {
    constructor() {
        this.ws = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectInterval = 3000; // 3 secondes
        this.notifications = [];
        this.init();
    }

    init() {
        this.connect();
        this.createNotificationPanel();
        this.bindEvents();
    }

    connect() {
        try {
            // Pour l'instant, simulation avec polling (remplacer par WebSocket réel en production)
            this.updateConnectionStatus('connecting');
            this.startPolling();
            console.log('🔔 Système de notifications temps réel initialisé');
        } catch (error) {
            console.error('Erreur de connexion temps réel:', error);
            this.updateConnectionStatus('disconnected');
            this.scheduleReconnect();
        }
    }

    startPolling() {
        // Simulation de notifications temps réel avec polling
        setInterval(() => {
            this.checkForNewNotifications();
        }, 30000); // Vérifier toutes les 30 secondes

        this.updateConnectionStatus('connected');
    }

    async checkForNewNotifications() {
        try {
            // Simulation d'appel API pour vérifier les nouvelles notifications
            const response = await fetch('api/notifications.php?action=check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        this.showRealTimeNotification(notification);
                    });
                }
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des notifications:', error);
        }
    }

    showRealTimeNotification(notification) {
        const notificationData = {
            message: notification.message,
            type: notification.type || 'info',
            duration: 8000, // Plus long pour les notifications temps réel
            persistent: notification.persistent || false
        };

        // Ajouter un indicateur spécial pour les notifications temps réel
        notificationData.message = `🔔 <strong>Temps réel:</strong> ${notificationData.message}`;

        NotificationManager.show(notificationData.message, notificationData.type, notificationData.duration);

        // Son de notification (optionnel)
        this.playNotificationSound();

        // Ajouter à l'historique
        this.addToHistory(notification);
    }

    playNotificationSound() {
        // Créer un son de notification subtil
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);

            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        } catch (error) {
            // Silencieux en cas d'erreur
        }
    }

    createNotificationPanel() {
        // Indicateur de statut
        const statusIndicator = document.createElement('div');
        statusIndicator.id = 'realtime-status';
        statusIndicator.className = 'realtime-status connecting';
        statusIndicator.textContent = '🔄 Connexion...';
        document.body.appendChild(statusIndicator);

        const panel = document.createElement('div');
        panel.id = 'realtime-notification-panel';
        panel.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 300px;
            max-height: 400px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            flex-direction: column;
        `;

        panel.innerHTML = `
            <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; color: #333; font-size: 1rem;">
                    <i class="fas fa-bell"></i> Notifications Temps Réel
                </h4>
                <button id="close-notification-panel" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #666;">&times;</button>
            </div>
            <div id="notification-history" style="flex: 1; overflow-y: auto; max-height: 300px;">
                <div style="padding: 20px; text-align: center; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Aucune notification récente</p>
                </div>
            </div>
        `;

        document.body.appendChild(panel);

        // Bouton pour ouvrir le panel
        const toggleBtn = document.createElement('button');
        toggleBtn.id = 'notification-toggle';
        toggleBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 80px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e30613, #d4af37);
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        toggleBtn.innerHTML = '<i class="fas fa-bell"></i>';
        toggleBtn.title = 'Notifications temps réel';

        document.body.appendChild(toggleBtn);

        // Événements
        toggleBtn.addEventListener('click', () => this.togglePanel());
        FIAMCDom.$('#close-notification-panel').addEventListener('click', () => this.hidePanel());
    }

    togglePanel() {
        const panel = FIAMCDom.$('#realtime-notification-panel');
        const isVisible = panel.style.display === 'flex';
        panel.style.display = isVisible ? 'none' : 'flex';
    }

    hidePanel() {
        FIAMCDom.$('#realtime-notification-panel').style.display = 'none';
    }

    addToHistory(notification) {
        const history = FIAMCDom.$('#notification-history');
        const notificationItem = document.createElement('div');
        notificationItem.style.cssText = `
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        `;

        const icon = this.getNotificationIcon(notification.type);
        const time = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

        notificationItem.innerHTML = `
            <i class="${icon}" style="color: ${this.getNotificationColor(notification.type)}; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <div style="font-size: 0.9rem; color: #333;">${notification.message}</div>
                <div style="font-size: 0.8rem; color: #666; margin-top: 2px;">${time}</div>
            </div>
        `;

        // Insérer au début
        history.insertBefore(notificationItem, history.firstChild);

        // Limiter à 20 notifications
        const items = history.children;
        if (items.length > 20) {
            history.removeChild(items[items.length - 1]);
        }

        // Mettre à jour le compteur
        this.updateNotificationBadge();
    }

    updateNotificationBadge() {
        const toggleBtn = FIAMCDom.$('#notification-toggle');
        const history = FIAMCDom.$('#notification-history');
        const count = history.children.length;

        if (count > 0) {
            toggleBtn.innerHTML = `<i class="fas fa-bell"></i><span style="
                position: absolute;
                top: -5px;
                right: -5px;
                background: #dc3545;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 0.7rem;
                display: flex;
                align-items: center;
                justify-content: center;
            ">${count > 99 ? '99+' : count}</span>`;
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-bell"></i>';
        }
    }

    getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'fas fa-check-circle';
            case 'error': return 'fas fa-exclamation-circle';
            case 'warning': return 'fas fa-exclamation-triangle';
            case 'info': default: return 'fas fa-info-circle';
        }
    }

    getNotificationColor(type) {
        switch (type) {
            case 'success': return '#28a745';
            case 'error': return '#dc3545';
            case 'warning': return '#ffc107';
            case 'info': default: return '#17a2b8';
        }
    }

    bindEvents() {
        // Écouter les événements personnalisés pour déclencher des notifications
        document.addEventListener('realtime-notification', (e) => {
            this.showRealTimeNotification(e.detail);
        });
    }

    updateConnectionStatus(status) {
        const indicator = FIAMCDom.$('#realtime-status');
        if (!indicator) return;

        indicator.className = `realtime-status ${status}`;

        switch (status) {
            case 'connected':
                indicator.innerHTML = '🟢 Temps réel actif';
                setTimeout(() => indicator.style.display = 'none', 3000); // Masquer après 3 secondes
                break;
            case 'connecting':
                indicator.innerHTML = '🟡 Connexion...';
                indicator.style.display = 'block';
                break;
            case 'disconnected':
                indicator.innerHTML = '🔴 Déconnecté';
                indicator.style.display = 'block';
                break;
        }
    }
}

/**
 * Fonction pour exporter les compétiteurs en CSV
 */
function exportCompetiteursToCSV() {
    const table = FIAMCDom.$('#competiteurs-table');
    if (!table) {
        NotificationManager.show('Table des compétiteurs non trouvée', 'error');
        return;
    }

    const rows = Array.from(table.querySelectorAll('tr'));
    const csvContent = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.map(cell => {
            // Supprimer les boutons et garder seulement le texte
            const text = cell.textContent.trim();
            // Échapper les guillemets et entourer de guillemets si nécessaire
            if (text.includes(',') || text.includes('"') || text.includes('\n')) {
                return '"' + text.replace(/"/g, '""') + '"';
            }
            return text;
        }).join(',');
    }).join('\n');

    // Créer et télécharger le fichier
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `competiteurs_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    NotificationManager.show('Export CSV réussi', 'success');
}

/**
 * Bouton retour en haut de page
 */
class BackToTopButton {
    constructor() {
        this.button = null;
        this.init();
    }

    init() {
        this.createButton();
        this.bindEvents();
    }

    createButton() {
        this.button = document.createElement('button');
        this.button.className = 'back-to-top';
        this.button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        this.button.setAttribute('aria-label', 'Retour en haut de page');
        document.body.appendChild(this.button);
    }

    bindEvents() {
        window.addEventListener('scroll', () => this.toggleVisibility());
        this.button.addEventListener('click', () => this.scrollToTop());
    }

    toggleVisibility() {
        if (window.pageYOffset > 300) {
            FIAMCDom.addClass(this.button, 'visible');
        } else {
            FIAMCDom.removeClass(this.button, 'visible');
        }
    }

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}