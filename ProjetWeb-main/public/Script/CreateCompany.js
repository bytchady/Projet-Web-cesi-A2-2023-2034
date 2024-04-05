document.addEventListener('DOMContentLoaded', function () {
    //Annuler la crétion
    document.getElementById('Cancel_Btn').addEventListener('click', function() {
        window.location.href = '/Page-d-accueil';
    });

    //Adresses supplémentaires
    let addressIndex = 0; // Ajoutez un compteur pour suivre les adresses supplémentaires

    document.getElementById("custom").addEventListener("click", function() {
        let container = document.getElementById("additionalAddressContainer");
        let card = createCard(addressIndex); // Passez l'index à la fonction createCard
        container.appendChild(card);
        addressIndex++; // Incrémentez l'index à chaque ajout d'une nouvelle adresse
    });

    function createCard(index) { // Ajoutez un paramètre pour l'index
        let card = document.createElement("div");
        card.classList.add("container");
        card.innerHTML = `
            <div class="card mt-2 mb-4 border-0 shadow ps-3" style="background-color:#c8d9e1">
                 <div  style=" height: 10px; background-color: #c8d9e1" class="card border-0"></div>
                 <div class="position-absolute top-0 end-0 m-3">
                     <button type="button" class="btn-close closeButton" aria-label="Close" data-bs-dismiss="offcanvas"></button>
                 </div>
                 <div class="row align-items-center">
                     <div class="col-10 col-lg-11">
                         <h6 class="text-start text-decoration-underline mt-2 mb-2 ms-1"> Adresse supplementaire :</h6>
                     </div>
                 </div>
  
                <!-- ... Autres éléments de la carte ... -->
                <div class="row align-items-center">
                    <div class="col-11">
                        <input type="text" name="additionalAddresses[${index}][StreetNumber]" class="form-control rounded-pill mt-2 mb-2 ms-1 border-dark" placeholder="Numéro de rue" aria-label="Numéro de rue">
                        <input type="hidden" name="additionalAddresses[${index}][DelLocation]" value="0">
                    </div>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-11">
                        <input type="text" name="additionalAddresses[${index}][StreetName]" class="form-control rounded-pill mt-2 mb-2 ms-1 border-dark" placeholder="Nom de rue" aria-label="Nom de rue">
                    </div>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-11">
                        <input type="text" name="additionalAddresses[${index}][City]" class="form-control rounded-pill mt-2 mb-2 ms-1 border-dark" placeholder="Ville" aria-label="Ville">
                    </div>
                </div>
                
                 <div class="row align-items-center">
                    <div class="col-11">
                        <input type="text" name="additionalAddresses[${index}][Zipcode]" class="form-control rounded-pill mt-2 mb-2 ms-1 border-dark" placeholder="Code postal" aria-label="Code postal">
                    </div>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-11">
                        <input type="text" name="additionalAddresses[${index}][Add_Adr]" class="form-control rounded-pill mt-2 mb-2 ms-1 border-dark" placeholder="Complément d'adresse (facultatif)" aria-label="Complément d'adresse (facultatif)">
                    </div>
                </div>
                
            </div>
        `;
        card.querySelector(".closeButton").addEventListener("click", function() {
            card.remove();
        });
        return card;
    }

    // Entreprise existante
    let companyInput = document.getElementById('Name_Company');
    let logoInput = document.getElementById('customFile');
    let errorMessageDiv = document.getElementById('errorMessage');
    let successMessageDiv = document.getElementById('successMessage');
    let submitButton = document.getElementById("Submit_Btn");
    let form = document.getElementById("CreateCompany_Form");

    // Gestionnaire d'événements pour le contrôle des champs et la soumission du formulaire
    submitButton.addEventListener("click", function(event) {
        event.preventDefault(); // Empêche la soumission par défaut du formulaire

        let formData = new FormData(form);
        submitFormData(formData);
    });

    function submitFormData(formData) {
        fetch('/Creer-une-entreprise', {
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
    companyInput.addEventListener('input', function() {
        if(errorMessageDiv.style.display === 'block') {
            errorMessageDiv.style.display = 'none';
            errorMessageDiv.textContent = '';
        }
    });

    logoInput.addEventListener('change', function() {
        if(errorMessageDiv.style.display === 'block') {
            errorMessageDiv.style.display = 'none';
            errorMessageDiv.textContent = '';
        }
    });

})