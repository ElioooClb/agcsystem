// Fermer la boîte modale
const closeButton = document.querySelector("#close");
const modalBg = document.querySelector("#modal-bg");

// Fermeture de la boîte modale en cliquant sur la croix
closeButton.addEventListener("click", (event) => {
    event.preventDefault();
    const modalEl = document.getElementById("modal");
    modalEl.classList.add("hidden");
});

// Fermeture de la boîte modale en cliquant sur le fond
modalBg.addEventListener("click", () => {
    const modalEl = document.getElementById("modal");
    modalEl.classList.add("hidden");
});

// Fermeture de la boîte modale en appuyant sur la touche Echap
document.addEventListener("keydown", (e) => {
    const modalEl = document.getElementById("modal");
    if (e.key === "Escape" && !modalEl.classList.contains("hidden")) {
        modalEl.classList.add("hidden");
    }
});

const deleteButtons = document.querySelectorAll(".delete-hours");

// Ajout d'un écouteur d'événement sur le bouton de suppression
deleteButtons.forEach((button) => {
    button.addEventListener("click", () => {
        const chantier_id = document.getElementById("chantier_id").value;
        const dateD = document.querySelector('#date');
        const date = dateD.value;

        let url = '/supprimer-heure-chantier/' + userId + '/' + date + '/' + chantier_id;
        if (chantier_id == "" || chantier_id == null) {
            url = '/supprimer-heure/' + userId + '/' + date;
        }
        fetch(url, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        location.reload();
    });
});
