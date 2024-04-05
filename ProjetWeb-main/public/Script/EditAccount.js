document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('Cancel_Btn').addEventListener('click', function() {
        window.location.href = '/Compte';
    });

    //Afficher le mot de passe
    const showPasswordCheckbox = document.getElementById("ShowPassword");
    const passwordInput = document.querySelector('input[name="Password"]');

    showPasswordCheckbox.addEventListener("change", function() {
        if (showPasswordCheckbox.checked) {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    });

    // Initialisation des éléments DOM nécessaires pour l'affichage des messages
    let errorMessageDiv = document.getElementById('errorMessage');
    let successMessageDiv = document.getElementById('successMessage');
    let submitButton = document.getElementById("Submit_Btn");
    let form = document.getElementById("EditAccount_Form");

    // Gestionnaire d'événements pour le contrôle des champs et la soumission du formulaire
    submitButton.addEventListener("click", function(event) {
        event.preventDefault();

        let formData = new FormData(form);
        submitFormData(formData);

    });

    function submitFormData(formData) {
        let actionUrl = form.action;
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    errorMessageDiv.textContent = data.message;
                    errorMessageDiv.style.display = 'block';
                } else {
                    successMessageDiv.textContent = data.message;
                    successMessageDiv.style.display = 'block';
                    setTimeout(function() {
                        window.location.href = '/Compte';
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorMessageDiv.style.display = 'block';
                errorMessageDiv.textContent = 'Une erreur est survenue lors de la communication avec le serveur.';
            });
    }

    const deleteButton = document.getElementById("Delete_Btn");

    deleteButton.addEventListener("click", function(event) {
        event.preventDefault(); // Empêche la soumission par défaut du formulaire

        if (confirm("Êtes-vous sûr de vouloir supprimer ce compte ?")) {
            deleteUser();
        }
    });

    function deleteUser() {
        let actionUrl = form.action; // Récupérez l'URL d'action du formulaire
        let formData = new FormData(form);
        formData.append('DeleteAccount', 'yes'); // Ajoutez les données supplémentaires ici

        fetch(actionUrl, {
            method: 'POST',
            body: formData // Utilisez l'objet FormData modifié
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successMessageDiv.textContent = data.message;
                    successMessageDiv.style.display = 'block';
                    setTimeout(function() {
                        window.location.href = '/Compte';
                    }, 2000);
                } else {
                    errorMessageDiv.textContent = "La suppression du compte a échoué.";
                    errorMessageDiv.style.display = 'block';                        }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorMessageDiv.style.display = 'block';
                errorMessageDiv.textContent = 'Une erreur est survenue lors de la communication avec le serveur.';
            });
    }

});