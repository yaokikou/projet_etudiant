// Récupération des éléments par les ID des champs 
const nom_utilisateur = document.getElementById("nom_utilisateur");
const email = document.getElementById("email");
const motdepasse = document.getElementById("motdepasse");
const motdepasse2 = document.getElementById("motdepasse2");
const message = document.getElementById("message");
const registerContainer = document.getElementById("register-container");

// Fonction de validation d'email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Fonction de validation de mot de passe
function isValidPassword(password) {
    return password.length >= 6;
}

// Fonction de validation de nom d'utilisateur
function isValidUsername(username) {
    return username.length >= 3 && /^[a-zA-Z0-9_]+$/.test(username);
}

// Validation en temps réel
nom_utilisateur.addEventListener('input', function() {
    if (this.value.trim() !== '' && !isValidUsername(this.value)) {
        this.style.borderColor = 'red';
        message.textContent = 'Le nom d\'utilisateur doit contenir au moins 3 caractères (lettres, chiffres, underscore)';
    } else {
        this.style.borderColor = '#ddd';
        message.textContent = '';
    }
});

email.addEventListener('input', function() {
    if (this.value.trim() !== '' && !isValidEmail(this.value)) {
        this.style.borderColor = 'red';
        message.textContent = 'Veuillez entrer une adresse email valide';
    } else {
        this.style.borderColor = '#ddd';
        message.textContent = '';
    }
});

motdepasse.addEventListener('input', function() {
    if (this.value.trim() !== '' && !isValidPassword(this.value)) {
        this.style.borderColor = 'red';
        message.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
    } else {
        this.style.borderColor = '#ddd';
        message.textContent = '';
    }
});

motdepasse2.addEventListener('input', function() {
    if (this.value.trim() !== '' && this.value !== motdepasse.value) {
        this.style.borderColor = 'red';
        message.textContent = 'Les mots de passe ne correspondent pas';
    } else {
        this.style.borderColor = '#ddd';
        message.textContent = '';
    }
});

// Validation lors de la soumission du formulaire
registerContainer.addEventListener("submit", function (e) {
    let hasErrors = false;
    let errorMessage = '';

    // Vérifier si les champs sont vides
    if (nom_utilisateur.value.trim() === "") {
        nom_utilisateur.style.borderColor = 'red';
        errorMessage = "Veuillez remplir tous les champs !";
        hasErrors = true;
    } else if (!isValidUsername(nom_utilisateur.value)) {
        nom_utilisateur.style.borderColor = 'red';
        errorMessage = "Nom d'utilisateur invalide";
        hasErrors = true;
    }

    if (email.value.trim() === "") {
        email.style.borderColor = 'red';
        errorMessage = "Veuillez remplir tous les champs !";
        hasErrors = true;
    } else if (!isValidEmail(email.value)) {
        email.style.borderColor = 'red';
        errorMessage = "Email invalide";
        hasErrors = true;
    }

    if (motdepasse.value.trim() === "") {
        motdepasse.style.borderColor = 'red';
        errorMessage = "Veuillez remplir tous les champs !";
        hasErrors = true;
    } else if (!isValidPassword(motdepasse.value)) {
        motdepasse.style.borderColor = 'red';
        errorMessage = "Le mot de passe doit contenir au moins 6 caractères";
        hasErrors = true;
    }

    if (motdepasse2.value.trim() === "") {
        motdepasse2.style.borderColor = 'red';
        errorMessage = "Veuillez remplir tous les champs !";
        hasErrors = true;
    } else if (motdepasse2.value !== motdepasse.value) {
        motdepasse2.style.borderColor = 'red';
        errorMessage = "Les mots de passe ne correspondent pas";
        hasErrors = true;
    }

    // Empêche le formulaire de se soumettre si des erreurs sont détectées
    if (hasErrors) {
        e.preventDefault();
        message.textContent = errorMessage;
        message.style.color = 'red';
        return false;
    }

    // Si tout est OK, réinitialiser les styles
    nom_utilisateur.style.borderColor = '#ddd';
    email.style.borderColor = '#ddd';
    motdepasse.style.borderColor = '#ddd';
    motdepasse2.style.borderColor = '#ddd';
    message.textContent = '';
});