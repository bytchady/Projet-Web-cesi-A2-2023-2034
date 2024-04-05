document.addEventListener('DOMContentLoaded', function () {
    //Annuler la crétion
    document.getElementById('Cancel_Btn').addEventListener('click', function() {
        window.location.href = '/Compte';
    });

    //Type d'utilisateur
    document.getElementById('Type').addEventListener('change', function () {
        let hiddenType = document.querySelector('input[name="hidden_Type"]');
        if (this.checked) {
            hiddenType.value = "Pilote";
        } else {
            hiddenType.value = "Etudiant";
        }
    });

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

    // //Afficher l'input type text en décochant "Autre"
    // let Checkboxes = document.querySelectorAll('.checkbox-other');
    //
    // Checkboxes.forEach(function (checkbox) {
    //     checkbox.addEventListener('change', function () {
    //         // Récupérer l'ID de l'élément "Autre" correspondant à cette case à cocher
    //         let targetId = checkbox.getAttribute('data-target');
    //         let Input = document.getElementById(targetId);
    //
    //         // Afficher ou masquer l'élément "Autre" en fonction de l'état de la checkbox
    //         Input.style.display = checkbox.checked ? '' : 'none';
    //     });
    // });

    // //Controle de champs
    // let submitButton = document.getElementById("Submit_Btn");
    //
    // submitButton.addEventListener("click", function() {
    //     let lastName = document.getElementById("Last_Name").value.trim();
    //     let firstName = document.getElementById("First_Name").value.trim();
    //     let promo = document.getElementById("Promotion").value.trim();
    //     let center = document.getElementById("Center").value.trim();
    //     let login = document.getElementById("Login").value.trim();
    //     let password = document.getElementById("Password").value.trim();
    //
    //     if (lastName === '' || firstName === '' || promo === '' || center === '' || login === '' || password === ''){
    //         window.alert("Veuillez remplir tous les champs.");
    //     } else {
    //         document.getElementById("CreateAccount_Form").submit();
    //     }
    // });

    // Entreprise existante
    let loginInput = document.getElementById('Login');
    let errorMessageDiv = document.getElementById('errorMessage');
    let successMessageDiv = document.getElementById('successMessage');
    let submitButton = document.getElementById("Submit_Btn");
    let form = document.getElementById("CreateAccount_Form");

    // Gestionnaire d'événements pour le contrôle des champs et la soumission du formulaire
    submitButton.addEventListener("click", function(event) {
        event.preventDefault();

        let formData = new FormData(form);
        submitFormData(formData);
    });

    function submitFormData(formData) {
        fetch('/Creer-un-compte', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    errorMessageDiv.textContent = data.message;
                    errorMessageDiv.style.display = 'block';
                }else {
                    successMessageDiv.textContent = data.message;
                    successMessageDiv.style.display = 'block';
                    setTimeout(function() {
                        window.location.href = '/Page-d-accueil';
                    }, 2000)
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorMessageDiv.style.display = 'block';
                errorMessageDiv.textContent = 'Une erreur est survenue lors de la communication avec le serveur.';
            });
    }

    //réinitialiser l'affichage des messages d'erreur
    loginInput.addEventListener('input', function() {
        if(errorMessageDiv.style.display === 'block') {
            errorMessageDiv.style.display = 'none';
            errorMessageDiv.textContent = '';
        }
    });

})