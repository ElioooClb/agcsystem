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

const deleteButtons = document.querySelectorAll(".delete-button");


// Ajout d'un écouteur d'événement sur le bouton de suppression
deleteButtons.forEach((button) => {
    button.addEventListener("click", () => {
        if(confirm("Voulez-vous vraiment supprimer la déclaration")){
            const modalEl = document.getElementById("modal");
            const date = modalEl.querySelector('h2').getAttribute('data-date');
            const url = '/supprimer-absences/' + userId + '/' + date ;
            fetch(url, {
                method: "GET",
                headers: {
                    "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                },
            });
            location.reload();
        }
    });
});


// Récupération des boutons de declaration d'absence
const declarationButtons = document.querySelectorAll(".declaration-button");

// Ajout d'un écouteur d'événement sur chaque bouton
declarationButtons.forEach((button) => {
    button.addEventListener("click", () => {
        const modalEl = document.getElementById("modal");
        const value = button.value;
        const date = modalEl.querySelector('h2').getAttribute('data-date');
        const hours = modalEl.querySelector('#hours_day');

        const url = '/absences/' + userId + '/' + date + '/' + hours.value + '/' + value;

        // Requête AJAX
        fetch(url, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
            },
        })
        .then((response)=>{
            if(!response.ok){
                throw new Error("Network response was not ok");
            }
        })
        .then(data => {
            // Fermeture de la boîte modale
            location.reload();
        })
        .catch(error => {
            console.error("There was an error!");
        });
    });
});


// Récupération des div info d'une déclaration
const infos = document.querySelectorAll(".info");
const modalEl = document.getElementById("modal");

// Ajout d'un écouteur d'événement au clique sur une div info

infos.forEach((button) => {
    button.addEventListener("click", async () => {
        const idTime = button.getAttribute('data-id');
        const time = await findDeclaration(idTime);
        const modalInfo = document.querySelector('#modalInfo');
        const modalBody = modalInfo.querySelector('.modal-body');
        modalBody.innerHTML = time.note;
        modalEl.classList.add("hidden");
    });
});



// Trouver la déclaration
async function findDeclaration(timeId) {
    try {
        const response = await fetch('/trouver-declaration/' + timeId, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content')
            },
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('There was an error:', error);
    }
}



