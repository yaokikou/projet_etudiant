document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('demandeForm');
    const serviceSelect = document.getElementById('service_id');
    const descriptionTextarea = document.getElementById('description');
    const submitBtn = document.querySelector('.connexion-btn');
    
    // Éléments pour afficher les erreurs
    let errorElements = {};
    
    // Créer les éléments d'erreur pour chaque champ
    [serviceSelect, descriptionTextarea].forEach(input => {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.cssText = 'color: #e74c3c; font-size: 0.85rem; margin-top: 4px; display: none;';
        input.parentNode.appendChild(errorDiv);
        errorElements[input.id] = errorDiv;
    });
    
    // Fonctions de validation
    function validateService(serviceId) {
        if (!serviceId || serviceId === '') {
            return 'Veuillez sélectionner un service';
        }
        return '';
    }
    
    function validateDescription(description) {
        if (!description.trim()) {
            return 'La description est requise';
        }
        if (description.trim().length < 10) {
            return 'La description doit contenir au moins 10 caractères';
        }
        if (description.trim().length > 1000) {
            return 'La description ne peut pas dépasser 1000 caractères';
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
    serviceSelect.addEventListener('change', function() {
        validateField(serviceSelect, validateService);
    });
    
    descriptionTextarea.addEventListener('input', function() {
        validateField(descriptionTextarea, validateDescription);
    });
    
    // Validation lors de la perte de focus
    serviceSelect.addEventListener('blur', function() {
        validateField(serviceSelect, validateService);
    });
    
    descriptionTextarea.addEventListener('blur', function() {
        validateField(descriptionTextarea, validateDescription);
    });
    
    // Validation complète du formulaire
    function validateForm() {
        const serviceValid = validateField(serviceSelect, validateService);
        const descriptionValid = validateField(descriptionTextarea, validateDescription);
        
        return serviceValid && descriptionValid;
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
        if (!serviceSelect.value) {
            serviceSelect.focus();
        } else if (!descriptionTextarea.value) {
            descriptionTextarea.focus();
        }
    });
    
    // Compteur de caractères pour la description
    const charCounter = document.createElement('div');
    charCounter.className = 'char-counter';
    charCounter.style.cssText = 'text-align: right; font-size: 0.8rem; color: #7f8c8d; margin-top: 4px;';
    descriptionTextarea.parentNode.appendChild(charCounter);
    
    function updateCharCounter() {
        const length = descriptionTextarea.value.length;
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
    
    descriptionTextarea.addEventListener('input', updateCharCounter);
    updateCharCounter(); // Initialisation
    
    // Amélioration UX : prévisualisation du service sélectionné
    serviceSelect.addEventListener('change', function() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            // Ajouter une classe pour indiquer qu'un service est sélectionné
            serviceSelect.style.borderColor = '#2ecc71';
        }
    });
    
    // Initialisation de la couleur de bordure si un service est déjà sélectionné
    if (serviceSelect.value) {
        serviceSelect.style.borderColor = '#2ecc71';
    }
}); 