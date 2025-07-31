// Récupération des éléments par les ID des champs 

const nom_utilisateur = document.getElementById("nom_utilisateur");
const motdepasse = document.getElementById("motdepasse");
const connexionForm = document.getElementById("connexion-form");

// Fonction de validation de nom d'utilisateur

function isValidUsername(username) {
    return username.length >= 3 && /^[a-zA-Z0-9_]+$/.test(username);
}

// Fonction de validation de mot de passe

function isValidPassword(password) {
    return password.length >= 1; // Pour la connexion, juste vérifier qu'il n'est pas vide
}

// Fonction pour afficher les erreurs

function showError(element, message) {
    element.style.borderColor = 'red';

    // Créer ou mettre à jour le message d'erreur

    let errorDiv = element.parentNode.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
        element.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

// Fonction pour effacer les erreurs

function clearError(element) {
    element.style.borderColor = '#ddd';
    const errorDiv = element.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Validation en temps réel pour le nom d'utilisateur

nom_utilisateur.addEventListener('input', function() {
    if (this.value.trim() !== '') {
        if (!isValidUsername(this.value)) {
            showError(this, 'Le nom d\'utilisateur doit contenir au moins 3 caractères (lettres, chiffres, underscore)');
        } else {
            clearError(this);
        }
    } else {
        clearError(this);
    }
});

// Validation en temps réel pour le mot de passe

motdepasse.addEventListener('input', function() {
    if (this.value.trim() !== '') {
        if (!isValidPassword(this.value)) {
            showError(this, 'Le mot de passe ne peut pas être vide');
        } else {
            clearError(this);
        }
    } else {
        clearError(this);
    }
});

// Validation lors de la soumission du formulaire

connexionForm.addEventListener("submit", function (e) {
    let hasErrors = false;
    let errorMessage = '';

    // Vérifier si les champs sont vides

    if (nom_utilisateur.value.trim() === "") {
        nom_utilisateur.style.borderColor = 'red';
        showError(nom_utilisateur, 'Veuillez saisir votre nom d\'utilisateur');
        hasErrors = true;
    } else if (!isValidUsername(nom_utilisateur.value)) {
        nom_utilisateur.style.borderColor = 'red';
        showError(nom_utilisateur, 'Nom d\'utilisateur invalide');
        hasErrors = true;
    }

    if (motdepasse.value.trim() === "") {
        motdepasse.style.borderColor = 'red';
        showError(motdepasse, 'Veuillez saisir votre mot de passe');
        hasErrors = true;
    }

    // Empêcher le formulaire de se soumettre si des erreurs sont détectées

    if (hasErrors) {
        e.preventDefault();
        return false;
    }

    // Si tout est OK, réinitialiser les styles

    clearError(nom_utilisateur);
    clearError(motdepasse);
    
    // // Afficher un message de chargement
    // const submitBtn = document.querySelector('.connexion-btn');
    // const originalText = submitBtn.textContent;
    // submitBtn.textContent = 'Connexion en cours...';
    // submitBtn.disabled = true;
    
    // Réactiver le bouton après 3 secondes au cas où il y aurait un problème

    setTimeout(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Effacer les erreurs quand l'utilisateur clique sur un champ

nom_utilisateur.addEventListener('focus', function() {
    if (this.style.borderColor === 'red') {
        clearError(this);
    }
});

motdepasse.addEventListener('focus', function() {
    if (this.style.borderColor === 'red') {
        clearError(this);
    }
});

// Validation lors de la perte de focus

nom_utilisateur.addEventListener('blur', function() {
    if (this.value.trim() !== '' && !isValidUsername(this.value)) {
        showError(this, 'Le nom d\'utilisateur doit contenir au moins 3 caractères (lettres, chiffres, underscore)');
    }
});

motdepasse.addEventListener('blur', function() {
    if (this.value.trim() === '') {
        showError(this, 'Le mot de passe est requis');
    }
}); 