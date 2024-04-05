document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.applyForm').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêche la soumission standard du formulaire
            let formData = new FormData(this);
            let offerId = this.querySelector('.apply-btn').dataset.offerId;
            let actionUrl = `/postuler/${offerId}`; // URL d'action dynamique basée sur l'ID de l'offre

            // Élément pour afficher les messages de réussite ou d'erreur
            let successMessageDiv = this.querySelector('#successMessage' + offerId);
            let errorMessageDiv = this.querySelector('#errorMessage' + offerId);

            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                if(data.success) {
                    successMessageDiv.textContent = data.message;
                    successMessageDiv.style.display = 'block';
                    errorMessageDiv.style.display = 'none';
                } else {
                    errorMessageDiv.textContent = data.message;
                    errorMessageDiv.style.display = 'block';
                    successMessageDiv.style.display = 'none';
                }
            }).catch(error => {
                console.error('Erreur:', error);
                errorMessageDiv.style.display = 'block';
                errorMessageDiv.textContent = 'Une erreur est survenue lors de la communication avec le serveur.';
            });
        });
    });
});
