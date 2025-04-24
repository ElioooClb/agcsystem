import { flashAlert } from "../utils/flashAlert";
// Script pour le planning des chantiers
var modal;
var modalInfo;
var chantierId;
var assignedUsers;
var selectUsers;

var dropEvents = document.querySelectorAll(".dropEvent");
// Côté administrateurs pour la gestions des chantiers à gauche du calendar
dropEvents.forEach(function (dropEvent) {
    // Ajouter un évènement sur chaque élément dropEvent au clique
    dropEvent.addEventListener("click", function () {
        // Récupérer l'id du chantier
        chantierId = this.getAttribute("data-id-chantier");
        // Sélectionner la boîte modale correspondante
        modal = document.getElementById("chantierModal_" + chantierId);
        modalInfo = document.getElementById("chantierModalInfo_" + chantierId);

        // Afficher la boîte modale

        modal.style.display = "block";

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
        // Get the <span> element that closes the modal
        var span = modal.getElementsByClassName("close")[0];
        // When the user clicks on <span> (x), close the modal
        span.onclick = function () {
            modal.style.display = "none";
        };
        selectUsers = modal.querySelector(".selected-users");
        assignedUsers = modal.querySelectorAll(".assignedUser");
        // Récupérer les utilisateurs assignés au chantier
    });
});


var btnTitles = document.querySelectorAll(".btn-title");

btnTitles.forEach(function (btnTitle) {
    btnTitle.addEventListener("click", function () {
        var dropEvent = document.querySelector(".dropEvent[data-id-chantier='" + chantierId + "']");
        var title = modal.querySelector(".title").value;
        var titleModal = modal.querySelector(".title-modal");
        titleModal.innerHTML = "Informations sur le chantier " + title;
        dropEvent.innerHTML = title;
        var url =
            "/modifier-titre-chantier/" + chantierId + "?title=" + title;
        // Effectuer la requête pour modifier le titre
        fetch(url, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok.");
                } else {
                    // Afficher un message de succès
                    modalInfo.querySelector(".title_info").innerHTML = "Informations sur le chantier " + title;
                    document.querySelectorAll(".idChantierEvent" + chantierId).forEach((event) => {
                        event.innerHTML = title;
                        event.style.color = "white";
                    });
                    flashAlert("success", "Le titre a été modifiée avec succès");

                }
            })
            .catch((error) => {
                console.error(error);
                console.error(error.stack);
            });
    });
});

// Ajouter un évènement sur le bouton de changement d'heure
var btnUpdateHours = document.querySelectorAll(".btn-hour");
btnUpdateHours.forEach(function (btnUpdateHour) {
    btnUpdateHour.addEventListener("click", function () {
        var hour = modal.querySelector(".hour").value;
        var url =
            "/modifier-devis-chantier/" + chantierId + "?hour=" + hour;
        // Effectuer la requête PUT sur les heures
        fetch(url, {
            method: "PUT",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok.");
                } else {
                    // Afficher un message de succès
                    modalInfo.querySelector(".hourInfo").innerHTML = "Heure Prévue : " + hour + "h";
                    flashAlert("success", "L'heure a été modifiée avec succès");
                }
            })

            .catch((error) => {
                console.error(error);
                console.error(error.stack);
            });
    });
});


assignedUsers = document.querySelectorAll(".assignedUser");
// Ajouter évènement sur le bouton d'assignation d'un utilisateur ou plusieurs utilisateurs à un chantier
var btnAssigns = document.querySelectorAll(".btn-assign");
// Ajouter un évènement sur le bouton d'assignation d'un utilisateur ou plusieurs utilisateurs à un chantier
btnAssigns.forEach(function (btnAssign) {
    btnAssign.addEventListener("click", function () {
        var selectedUsers = modal.querySelector(".selected-users");
        var listeTeck = modal.querySelector(".ulListeTeck");
        var listeTeckInfo = modalInfo.querySelector(".ulListeTeck");
        selectedUsers = selectedUsers.selectedOptions;
        var users = [];
        var assignedUsersList = modal.querySelector(".assigned-users");
        // Met dans un tableau les valeurs des utilisateurs sélectionnés dans le select
        for (var i = 0; i < selectedUsers.length; i++) {
            users.push(selectedUsers[i].value);
        }

        // Requête PUT pour ajouter les utilisateurs au chantier
        users.forEach((user) => {
            var url = "/ajouter-user-chantier/" + chantierId + "/" + user;
            fetch(url, {
                method: "PUT",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok.");
                    } else {
                        // Afficher un message de succès
                        var option = modal.querySelector(
                            "option[value='" + user + "']"
                        );
                        var userId = option.value;
                        var userName = option.text;
                        // Créer le nouvel élément à ajouter à la liste des teckos assignés
                        var newAssignedUser = document.createElement("li");
                        newAssignedUser.classList.add(
                            "d-flex",
                            "assignedUser"
                        );
                        // Nouvel élément de la page
                        newAssignedUser.innerHTML =
                            "<p>" +
                            userName +
                            "</p>" +
                            '<button class="deleteAssignedUser" data-id-user="' +
                            userId +
                            '" data-name-user="' +
                            userName +
                            '">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">' +
                            '<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>' +
                            '<path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>' +
                            "</svg>" +
                            "</button>";
                        // Ajouter le nouvel élément à la liste des utilisateurs assignés
                        listeTeck.appendChild(newAssignedUser);
                        // Pour enlever le teckos assignées de la liste des teckos disponibles
                        option.remove();
                        // Ajouter le nouvel élément à la liste des utilisateurs assignés
                        var newAssignedUserInfo = document.createElement("li");
                        newAssignedUserInfo.classList.add(
                            "d-flex",
                            "assignedUser"
                        );
                        // Nouvel élément de la page
                        newAssignedUserInfo.innerHTML = userName;
                        // Ajouter le nouvel élément à la liste des utilisateurs assignés
                        listeTeckInfo.appendChild(newAssignedUserInfo);
                        assignedUsers = modal.querySelectorAll(".assignedUser");
                        assignedUsers.forEach((assignedUser) => {
                            // Récupérer l'id de l'utilisateur , du bouton delete et le nom de l'utilisateur pour pouvoir l'enlever de la liste des teckos assignés
                            var deleteButons = assignedUser.querySelectorAll(".deleteAssignedUser");


                            // Requête DELETE pour supprimer l'utilisateur du chantier
                            deleteButons.forEach((deleteButon) => {
                                deleteButon.addEventListener("click", function () {
                                    var listeTeckInfo = modalInfo.querySelector(".ulListeTeck");
                                    var user_id = deleteButon.getAttribute("data-id-user");
                                    var user_name = deleteButon.getAttribute("data-name-user");
                                    deleteButon.disabled = true;
                                    var url =
                                        "/supprimer-user-chantier/" + chantierId + "/" + user_id;
                                    fetch(url, {
                                        method: "DELETE",
                                        headers: {
                                            "X-CSRF-TOKEN": document
                                                .querySelector('meta[name="csrf-token"]')
                                                .getAttribute("content"),
                                        },
                                    })
                                        .then((response) => {
                                            if (!response.ok) {
                                                throw new Error("Network response was not ok.");
                                            } else {
                                                // En cas de succès enlevé de la liste des teckos assignés et ajouté à la liste des teckos disponibles
                                                assignedUser.remove();
                                                var option = document.createElement("option");
                                                option.value = user_id;
                                                option.text = user_name;
                                                option.classList.add("text-2xl");
                                                selectUsers.add(option);
                                                deleteButon.disabled = false;
                                                var assignedUserInfo = findLiByText(listeTeckInfo, user_name);
                                                assignedUserInfo.remove();
                                            }
                                        })

                                        .catch((error) => {
                                            console.error(error);
                                            console.error(error.stack);
                                        });
                                });
                            });
                        });
                    }
                })
                .catch((error) => {
                    console.error(error);
                    console.error(error.stack);
                });
        });
    });
});


// Ajouter un événement sur le bouton de suppression de chaque utilisateur pour déssassigner l'utilisateur du chantier
assignedUsers.forEach((assignedUser) => {
    assignedUser.addEventListener("click", function () {
        var deleteButons = assignedUser.querySelectorAll(".deleteAssignedUser");
        // Requête DELETE pour supprimer l'utilisateur du chantier
        deleteButons.forEach((deleteButon) => {
            deleteButon.addEventListener("click", function () {

                var listeTeckInfo = modalInfo.querySelector(".ulListeTeck");
                var user_id = deleteButon.getAttribute("data-id-user");
                var user_name = deleteButon.getAttribute("data-name-user");
                deleteButon.disabled = true;
                var url =
                    "/supprimer-user-chantier/" + chantierId + "/" + user_id;
                fetch(url, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error("Network response was not ok.");
                        } else {
                            // En cas de succès enlevé de la liste des teckos assignés et ajouté à la liste des teckos disponibles
                            assignedUser.remove();
                            var option = document.createElement("option");
                            option.value = user_id;
                            option.text = user_name;
                            option.classList.add("text-2xl");
                            selectUsers.add(option);
                            deleteButon.disabled = false;
                            var assignedUserInfo = findLiByText(listeTeckInfo, user_name);
                            assignedUserInfo.remove();
                        }
                    })

                    .catch((error) => {
                        console.error(error);
                        console.error(error.stack);
                    });
            });
        });
    });
});

// Ajouter une observation au chantier

var btnObservations = document.querySelectorAll(".btn-observation");
btnObservations.forEach(function (btnObservation) {
    btnObservation.addEventListener("click",
        function handleObservationClick() {
            var observation = modal.querySelector(".observations").value;
            var url =
                "/ajouter-observation-chantier/" +
                chantierId +
                "?observation=" +
                observation;
            // Effectuer la requête PUT pour une observation au chantier
            fetch(url, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({
                    observation: observation
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok.");
                    } else {
                        if (observation != "") {
                            modalInfo.querySelector(".obs").innerHTML =
                                observation;
                        } else {
                            modalInfo.querySelector(".obs").innerHTML =
                                "Aucune observation";
                        }

                        // Afficher un message de succès
                        flashAlert("success", "Observation modifiée !");
                        // btnObservation.removeEventListener("click", handleObservationClick);
                    }
                })
                .catch((error) => {
                    console.error(error);
                    console.error(error.stack);
                });
        });
});


// Archiver le chantier
var btnArchives = document.querySelectorAll(".btn-archive");
btnArchives.forEach(function (btnArchive) {
    btnArchive.addEventListener("click", function () {
        if (confirm("Voulez-vous vraiment archiver ce chantier ?")) {
            var url = "/archiver-chantier/" + chantierId;
            // Effectuer la requête PUT pour archiver le chantier
            fetch(url, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok.");
                    } else {
                        window.location.reload();
                    }
                })

                .catch((error) => {
                    console.error(error);
                    console.error(error.stack);
                });
        }
    });
});

// Sélectionner tous les éléments input de type select dans la div "amount"

const btnAmounts = document.querySelectorAll(".btn-amount");
btnAmounts.forEach(function (btnAmount) {
    btnAmount.addEventListener("click", () => {
        const amounts = modal.querySelector(".amounts");
        const amountSelected = Array.from(amounts.querySelectorAll(".amount"));
        // Récupérer les valeurs des éléments amountSelected
        const values = amountSelected.map((element) => element.value);

        // Construire l'objet de données à envoyer dans la requête
        const data = {
            amounts: values,
        };

        // URL de la requête PUT pour modifier les montants
        const url =
            "/modifier-montant/" + chantierId + "?amounts=" + data.amounts;

        // Effectuer la requête GET avec les données
        fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok.");
                } else {
                    modalInfo.querySelector(".amountMaterial").innerHTML = "Montant : " + data.amounts[0] + "€";
                    modalInfo.querySelector(".amountService").innerHTML = "Montant : " + data.amounts[1] + "€";
                    flashAlert("success", "Montant modifié avec succès !");
                }
            })
            .catch((error) => {
                console.error(error);
                console.error(error.stack);
            });
    });
});


// Début [SPECGT6] - Modification du comportement des checkbox
// Sélectionner tous les éléments input de type checkbox dans la div "options"
const checkboxes = document.querySelectorAll('#options input[type="checkbox"]');

// Ajouter un gestionnaire d'événements onChange à chaque case à cocher
checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
        // Récupérer la valeur de la case à cocher
        const checked = checkbox.checked;
        const id_chantier = checkbox.closest('[id^="options"]').getAttribute("data-id");
        const id_parameter = checkbox.getAttribute("data-id"); // Nouveau

        // URL de la requête GET pour modifier les options en fonction de la value de la checkbox
        const url = "/modifier-parameter/" + id_chantier + "/" + id_parameter + "/" + checked; // Modifié

        // Effectuer la requête GET avec les données
        fetch(url, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok.");
                } else {
                    let labelEl = checkbox.parentNode;
                    if (checked) {
                        labelEl.classList.add('green');
                    } else {
                        labelEl.classList.remove('green');
                    }
                    flashAlert("success", "Option modifiée avec succès !");
                }
            })

            .catch((error) => {
                console.error(error);
                console.error(error.stack);
            });

        let allChecked = true;
        const currentModal = document.getElementById("chantierModal_" + id_chantier);
        const currentCheckboxes = currentModal.querySelectorAll("input[type='checkbox']");
        currentCheckboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                allChecked = false;
            }
        });

        if (allChecked) {
            fetch('/modifier-date-realisation/' + id_chantier, {
                method: "PUT",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
        }
    });
});
// Fin [SPECGT6] - Modification du comportement des checkbox

// Sélectionner tous les éléments input de type select dans la div "colors"
const colorsSelected = document.querySelectorAll(".colorSelect");
// Ajouter un gestionnaire d'événements onChange à chaque case à cocher
colorsSelected.forEach((colorSelected) => {
    colorSelected.addEventListener("change", () => {
        // Récupérer la valeur de la couleur sélectionner
        const color = colorSelected.value;
        const id_chantier = colorSelected.getAttribute("data-id");
        const url = "/modifier-couleur/" + id_chantier + "/" + color;

        // Effectuer la requête PUT pour modifier la couleur du chantier en fonction de la value de la couleur sélectionner
        fetch(url, {
            method: "PUT",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok.");
                } else {
                    const currentEvent = document.querySelector(
                        '.dropEvent[data-id-chantier="' + id_chantier + '"]'
                    );
                    const events = document.querySelectorAll(
                        ".idChantierEvent" + id_chantier
                    );
                    events.forEach((event) => {

                        event.classList.remove(
                            "bg-blue-500",
                            "bg-green-500",
                            "bg-red-500",
                            "bg-yellow-500",
                            "bg-purple-500",
                            "bg-gray-500",
                        );

                        event.classList.add("bg-" + color + "-500");
                    });

                    // Mettre à jour la classe de l'élément currentEvent avec la classe tailwinds et la couleurs récupérer dans la value de la couleur sélectionner
                    currentEvent.classList.remove(
                        "bg-blue-500",
                        "bg-green-500",
                        "bg-red-500",
                        "bg-yellow-500",
                        "bg-purple-500",
                        "bg-gray-500",
                    );
                    currentEvent.classList.add("bg-" + color + "-500");
                }
            })

            .catch((error) => {
                console.error(error);
                console.error(error.stack);
            });
    });
});

function findLiByText(parentElement, text) {
    var lis = parentElement.querySelectorAll('li');
    for (var i = 0; i < lis.length; i++) {
        if (lis[i].textContent.includes(text)) {
            return lis[i];
        }
    }
    return null;
}
