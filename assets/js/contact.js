document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nomInput = document.getElementById('nom');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    const submitBtn = document.querySelector('.connexion-btn');
    
    // Éléments pour afficher les erreurs
    let errorElements = {};
    
    // Créer les éléments d'erreur pour chaque champ
    [nomInput, emailInput, messageInput].forEach(input => {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.cssText = 'color: #e74c3c; font-size: 0.85rem; margin-top: 4px; display: none;';
        input.parentNode.appendChild(errorDiv);
        errorElements[input.id] = errorDiv;
    });
    
    // Regex pour validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const nomRegex = /^[a-zA-ZÀ-ÿ\s'-]{2,50}$/;
    
    // Fonctions de validation
    function validateNom(nom) {
        if (!nom.trim()) {
            return 'Le nom est requis';
        }
        if (nom.trim().length < 2) {
            return 'Le nom doit contenir au moins 2 caractères';
        }
        if (nom.trim().length > 50) {
            return 'Le nom ne peut pas dépasser 50 caractères';
        }
        if (!nomRegex.test(nom.trim())) {
            return 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes';
        }
        return '';
    }
    
    function validateEmail(email) {
        if (!email.trim()) {
            return 'L\'email est requis';
        }
        if (!emailRegex.test(email.trim())) {
            return 'Veuillez saisir une adresse email valide';
        }
        return '';
    }
    
    function validateMessage(message) {
        if (!message.trim()) {
            return 'Le message est requis';
        }
        if (message.trim().length < 10) {
            return 'Le message doit contenir au moins 10 caractères';
        }
        if (message.trim().length > 1000) {
            return 'Le message ne peut pas dépasser 1000 caractères';
        }
        return '';
    }
    
    // Afficher/masquer les erreurs
    function showError(fieldId, message) {
        const errorElement = errorElements[fieldId];
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    function hideError(fieldId) {
        const errorElement = errorElements[fieldId];
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }
    
    // Validation en temps réel
    function validateField(input, validator) {
        const value = input.value;
        const error = validator(value);
        
        if (error) {
            showError(input.id, error);
            input.style.borderColor = '#e74c3c';
            return false;
        } else {
            hideError(input.id);
            input.style.borderColor = '#2ecc71';
            return true;
        }
    }
    
    // Événements de validation en temps réel
    nomInput.addEventListener('input', function() {
        if (!nomInput.readOnly) {
            validateField(nomInput, validateNom);
        }
    });
    
    emailInput.addEventListener('input', function() {
        if (!emailInput.readOnly) {
            validateField(emailInput, validateEmail);
        }
    });
    
    messageInput.addEventListener('input', function() {
        validateField(messageInput, validateMessage);
    });
    
    // Validation lors de la perte de focus
    nomInput.addEventListener('blur', function() {
        if (!nomInput.readOnly) {
            validateField(nomInput, validateNom);
        }
    });
    
    emailInput.addEventListener('blur', function() {
        if (!emailInput.readOnly) {
            validateField(emailInput, validateEmail);
        }
    });
    
    messageInput.addEventListener('blur', function() {
        validateField(messageInput, validateMessage);
    });
    
    // Validation complète du formulaire
    function validateForm() {
        const nomValid = nomInput.readOnly || validateField(nomInput, validateNom);
        const emailValid = emailInput.readOnly || validateField(emailInput, validateEmail);
        const messageValid = validateField(messageInput, validateMessage);
        
        return nomValid && emailValid && messageValid;
    }
    
    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Désactiver le bouton pendant l'envoi
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            
            // Soumettre le formulaire
            form.submit();
        } else {
            // Afficher un message d'erreur général
            const generalError = document.createElement('div');
            generalError.className = 'connexion-message error';
            generalError.textContent = 'Veuillez corriger les erreurs dans le formulaire.';
            generalError.style.marginBottom = '18px';
            
            // Supprimer l'ancien message d'erreur général s'il existe
            const existingError = document.querySelector('.connexion-message.error');
            if (existingError) {
                existingError.remove();
            }
            
            // Insérer le nouveau message d'erreur
            const subtitle = document.querySelector('.subtitle');
            subtitle.parentNode.insertBefore(generalError, subtitle.nextSibling);
            
            // Scroll vers le premier champ avec erreur
            const firstError = document.querySelector('.field-error[style*="display: block"]');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Amélioration UX : focus sur le premier champ vide
    window.addEventListener('load', function() {
        if (!nomInput.value && !nomInput.readOnly) {
            nomInput.focus();
        } else if (!emailInput.value && !emailInput.readOnly) {
            emailInput.focus();
        } else if (!messageInput.value) {
            messageInput.focus();
        }
    });
    
    // Compteur de caractères pour le message
    const charCounter = document.createElement('div');
    charCounter.className = 'char-counter';
    charCounter.style.cssText = 'text-align: right; font-size: 0.8rem; color: #7f8c8d; margin-top: 4px;';
    messageInput.parentNode.appendChild(charCounter);
    
    function updateCharCounter() {
        const length = messageInput.value.length;
        const maxLength = 1000;
        charCounter.textContent = `${length}/${maxLength} caractères`;
        
        if (length > maxLength * 0.9) {
            charCounter.style.color = '#e74c3c';
        } else if (length > maxLength * 0.7) {
            charCounter.style.color = '#f39c12';
        } else {
            charCounter.style.color = '#7f8c8d';
        }
    }
    
    messageInput.addEventListener('input', updateCharCounter);
    updateCharCounter(); // Initialisation
}); 