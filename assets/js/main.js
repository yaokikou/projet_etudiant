// Confirmation pour les liens de suppression

window.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[onclick]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                e.preventDefault();
            }
        });
    });
});

// Validation des formulaires

document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function() {
       
    });
}); 