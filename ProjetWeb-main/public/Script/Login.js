document.addEventListener('DOMContentLoaded', function () {
    //Afficher le mot de passe
    const showPasswordCheckbox = document.getElementById("ShowPassword");
    const passwordInput = document.querySelector('input[name="Password"]');

    showPasswordCheckbox.addEventListener("change", function() {
        if (showPasswordCheckbox.checked) {
            passwordInput.type = "text"; // Afficher le mot de passe
        } else {
            passwordInput.type = "password"; // Masquer le mot de passe
        }
    });

    document.getElementById('Login_Form').addEventListener('submit', function(e) {
        // Empêcher la soumission classique du formulaire
        e.preventDefault();

        // Récupérer les valeurs du formulaire
        let login = document.getElementById('Login').value;
        let password = document.getElementById('Password').value;

        // Préparer les données à envoyer
        let formData = new FormData();
        formData.append('Login', login);
        formData.append('Password', password);

        // Faire la requête fetch à l'API
        fetch('/Connection', { // Assurez-vous que l'URL est correcte
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Gestion en cas de succès, par exemple redirection ou affichage d'un message
                    window.location.href = '/Page-d-accueil';
                } else {
                    // Affichage du message d'erreur
                    let errorMessageDiv = document.getElementById('errorMessage');
                    errorMessageDiv.style.display = 'block'; // Change le display de none à block pour le rendre visible
                    errorMessageDiv.textContent = data.message; // Insère le message d'erreur dans l'élément
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('errorMessage').style.display = 'block';
                document.getElementById('errorMessage').textContent = 'Une erreur est survenue lors de la connexion.';
            });
    });

})