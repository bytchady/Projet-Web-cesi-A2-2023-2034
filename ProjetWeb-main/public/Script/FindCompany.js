function validerFormulaire() {

    var nom = document.getElementById("exampleFormControlInput1").value;
    var prenom = document.getElementById("exampleFormControlInput2").value;
    var email = document.getElementById("exampleFormControlInput3").value;
    var cv = document.getElementById("formFile").value;
    var lettreMotivation = document.getElementById("formFile50").value;

    // Vérifier si tous les champs sont remplis
    if (nom === "" || prenom === "" || email === "" || cv === "" || lettreMotivation === "") {
        // Afficher un message d'erreur
        alert("Veuillez remplir tous les champs du formulaire.");
        return false;
    }
    return true;
}