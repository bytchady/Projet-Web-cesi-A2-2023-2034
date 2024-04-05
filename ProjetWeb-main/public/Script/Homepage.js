document.addEventListener('DOMContentLoaded', function() {
    let successMessage = sessionStorage.getItem('successMessage');
    if(successMessage) {
        // Mettre à jour le texte de la pop-up avec le message
        document.getElementById('successMessageText').textContent = successMessage;
        // Afficher la pop-up
        let successMessageContainer = document.getElementById('successMessageContainer');
        successMessageContainer.style.display = 'block';

        // La cacher après 3 secondes
        setTimeout(function() {
            $(successMessageContainer).fadeOut('slow');
        }, 3000); // 3000 ms = 3 secondes

        // Nettoyez le message pour qu'il ne s'affiche pas à nouveau lors d'une visite ultérieure
        sessionStorage.removeItem('successMessage');
    }
});