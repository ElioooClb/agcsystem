import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    const coefBtn = document.querySelectorAll(".coef");

    coefBtn.forEach((btn) => {
        btn.addEventListener("click", () => {
            Swal.fire({
                title: "Saisir un Coefficient en %",
                input: "number",
                inputPlaceholder: "Entrez un coefficient (0 - 100 %)",
                inputAttributes: {
                    min: 0,
                    max: 100,
                    step: 1,
                },
                showCancelButton: true,
                cancelButtonText: "Fermer",
                confirmButtonText: "Valider",
                preConfirm: (value) => {
                    if (value < 0 || value > 100) {
                        Swal.showValidationMessage(
                            `Le coefficient en % doit être compris entre 0 et 100`
                        );
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    const coef = result.value;
                    const formData = new FormData();
                    formData.append('coef', coef);
                    formData.append('user_id', btn.dataset.id);
                    fetch('/users/coefProd', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur lors de la requête');
                        btn.innerHTML = coef;
                    })
                    .catch(error => {
                        console.error(error);
                    });
                }
            });
        });
    });
});
