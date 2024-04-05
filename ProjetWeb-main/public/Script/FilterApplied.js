document.addEventListener('DOMContentLoaded', function () {
    // Récupérer tous les éléments nécessaires
    let otherCheckboxes = document.querySelectorAll('.other-checkbox');

    otherCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            // Récupérer l'élément "Autre" correspondant à cette dropdown
            let otherInput = checkbox.closest('li').querySelector('.other-input');

            // Afficher ou masquer l'élément "Autre" en fonction de l'état de la checkbox
            otherInput.style.display = checkbox.checked ? '' : 'none';
        });
    });
});
