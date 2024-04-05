document.addEventListener('DOMContentLoaded', function () {
    const cancelButton = document.getElementById("Cancel_Btn");

    const submitButton = document.getElementById("Submit_Btn");
    let form = document.getElementById("CreateOffer_Form");
    let errorMessageDiv = document.getElementById('errorMessage');
    let successMessageDiv = document.getElementById('successMessage');

    // const ajouterButton = document.getElementById("ajouterButton");
    // ajouterButton.addEventListener("click", function () {
    //     var competencesInput = document.getElementById("competencesInput").value;
    //     if (competencesInput.trim() !== "") {
    //         var listeCompetenceshtml = document.getElementById("listeCompetences");
    //
    //         var nouvelElement = document.getElementById("template").content.cloneNode(true);
    //         let input = nouvelElement.children[0].children[0];
    //         input.id = "competence_" + Math.random().toString(36).substring(10);
    //         input.name = "competence_" + Math.random().toString(36).substring(10);
    //         input.value = competencesInput.trim();
    //
    //         listeCompetenceshtml.appendChild(nouvelElement);
    //
    //         document.getElementById("competencesInput").value = "";
    //         console.log(input.value);
    //     } else {
    //         alert("Veuillez entrer une compétence.");
    //     }
    // });

    const ajouterButton = document.getElementById("ajouterButton");
    ajouterButton.addEventListener("click", function () {
        var competencesInput = document.getElementById("competencesInput").value;
        if (competencesInput.trim() !== "") {
            var listeCompetenceshtml = document.getElementById("listeCompetences");

            var nouvelElement = document.getElementById("template").content.cloneNode(true);
            let input = nouvelElement.querySelector("input");
            input.name = "skills[]"; // Utilisez un tableau pour recueillir toutes les compétences
            input.value = competencesInput.trim();

            listeCompetenceshtml.appendChild(nouvelElement);

            document.getElementById("competencesInput").value = ""; // Réinitialiser la valeur de l'input
        } else {
            alert("Veuillez entrer une compétence.");
        }
    });


    cancelButton.addEventListener("click", function () {
        window.location.href = "/Offres";
    });

    submitButton.addEventListener("click", function(event) {
        event.preventDefault();

        let formData = new FormData(form);
        submitFormData(formData);
    });
    function submitFormData(formData) {
        fetch('/Creer-une-offre', {
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
                        window.location.href = '/Offres';
                    }, 2000)
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorMessageDiv.style.display = 'block';
                errorMessageDiv.textContent = 'Une erreur est survenue lors de la communication avec le serveur.';
            });
    }

    let companySelect = document.getElementById('Entreprise');
    companySelect.addEventListener('change', function() {
        let companyId = this.value;
        if (companyId) {
            fetch(`/Creer-une-offre/${companyId}`) // Utilisation de backticks ici
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let addressesSelect = document.getElementById('Adresse');
                    addressesSelect.innerHTML = '<option value="">Sélectionnez une adresse</option>';
                    data.addresses.forEach(function(address) {
                        let option = new Option(address.fullAddress, address.idLocation);
                        addressesSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            document.getElementById('Adresse').innerHTML = '<option value="">Sélectionnez d\'abord une entreprise </option>';
        }
    });

});
