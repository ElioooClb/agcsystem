import Swal from "sweetalert2";
import { flashAlert } from "../utils/flashAlert";
import '../../../modernizr.js';

let currentSortIndex = 0;

document.addEventListener("DOMContentLoaded", function () {
    checkDateInputSupport();
});

document.addEventListener("livewire:load", function () {
    initListeners();
});

document.addEventListener("livewire:update", function () {
    initListeners();
});

function initListeners() {
    const hoursCells = document.querySelectorAll(".hoursCell");
    const serviceAmountCells = document.querySelectorAll(".serviceAmountCell");
    const averageHourlyRateCell = document.querySelector("#tdAvgHourlyRate");
    const toggleArchivedCheckbox = document.getElementById("toggleArchived");
    toggleArchivedCheckbox.checked = false;
    const businessCell = document.getElementById("businessID");

    hoursCells.forEach((cell) => {
        cell.removeEventListener("click", handleHoursClick);
        cell.addEventListener("click", handleHoursClick);
    });

    serviceAmountCells.forEach((cell) => {
        cell.removeEventListener("click", handleServiceAmount);
        cell.addEventListener("click", handleServiceAmount);
    });

    if (averageHourlyRateCell) {
        averageHourlyRateCell.removeEventListener("click", handleAvgHourlyRate);
        averageHourlyRateCell.addEventListener("click", handleAvgHourlyRate);
    }

    if (toggleArchivedCheckbox) {
        toggleArchivedCheckbox.removeEventListener(
            "change",
            handleToggleArchived
        );
        toggleArchivedCheckbox.addEventListener("change", handleToggleArchived);
    }

    if (businessCell) {
        businessCell.removeEventListener("click", handleBusinessClick);
        businessCell.addEventListener("click", handleBusinessClick);
    }
}

function handleHoursClick(event) {
    const cell = event.target;
    const worksiteID = cell.getAttribute("id");
    fireDialog(
        cell,
        worksiteID,
        "updateHoursEstimation",
        "hoursEstimationUpdated"
    );
}

function handleServiceAmount(event) {
    const cell = event.target;
    const worksiteID = cell.getAttribute("id");
    fireDialog(cell, worksiteID, "updateServiceAmount", "serviceAmountUpdated");
}

function handleAvgHourlyRate(event) {
    const cell = event.target;
    fireDialog(cell, 0, "updateAvgHourlyRate", "avgHourlyRateUpdated");
}

function handleToggleArchived(event) {
    const showArchived = event.target.checked;
    const rows = document.querySelectorAll("tr[data-status]");

    rows.forEach((row) => {
        if (row.dataset.status === "archived") {
            row.style.display = showArchived ? "none" : "";
        }
    });
}

function handleBusinessClick(event) {
    const table = document.querySelector("#worksitesTable");
    const rows = Array.from(table.querySelectorAll("tr[wire\\:key]"));
    const iconElement = document.getElementById("statusIcon");
    const iconOrders = ["inProgress", "toBill", "archived"];
    const sortOrders = [
        ["inProgress", "toBill", "archived"],
        ["toBill", "archived", "inProgress"],
        ["archived", "inProgress", "toBill"],
    ];

    // Fonction pour trier la table
    function sortTable(index) {
        const currentSortOrder = sortOrders[index];
        const iconStatus = iconOrders[index];

        if (iconStatus === "inProgress") {
            iconElement.className = "text-green-500 fas fa-sync-alt";
        } else if (iconStatus === "toBill") {
            iconElement.className = "text-red-500 fas fa-file-invoice-dollar";
        } else if (iconStatus === "archived") {
            iconElement.className = "text-slate-500 fas fa-archive";
        }

        // Trier les lignes selon le status
        const sortedRows = rows.sort((a, b) => {
            const statusA = a.getAttribute("data-status-group");
            const statusB = b.getAttribute("data-status-group");

            // Retourner l'index des statuts dans l'ordre de tri
            return (
                currentSortOrder.indexOf(statusA) -
                currentSortOrder.indexOf(statusB)
            );
        });

        // Réorganiser les lignes dans la table après le tri
        sortedRows.forEach((row) => {
            table.querySelector("tbody").appendChild(row); // Déplacer chaque ligne au bon endroit dans le DOM
        });
    }

    // Fonction pour changer l'ordre du tri à chaque clic
    function changeSortOrder() {
        currentSortIndex = (currentSortIndex + 1) % 3; // Changer l'index pour passer au prochain ordre
        sortTable(currentSortIndex); // Appeler la fonction de tri après avoir changé l'ordre
    }

    changeSortOrder(); // Appeler la fonction pour trier la table
}

function fireDialog(cell, worksiteID, emitTarget, onTarget) {
    Swal.fire({
        title: "Entrer une nouvelle valeur",
        input: "text",
        inputValue: cell.innerText,
        showCancelButton: true,
        reverseButtons: true,
        cancelButtonText: "Cancel",
        confirmButtonText: "Save",
        customClass: {
            input: "text-center font-bold text-2xl",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const newValue = result.value;
            cell.innerText = newValue;
            Livewire.emit(emitTarget, { id: worksiteID, newValue: newValue });
            Livewire.on(onTarget, (message) => {
                flashAlert("info", message);
            });
            Livewire.on("error", (message) => {
                flashAlert("error", message);
            });
        }
    });
}

function checkDateInputSupport() {
    if (Modernizr.inputtypes.date) {
        document.querySelectorAll(".start-date-input, .end-date-input")
            .forEach((el) => {
                el.classList.remove("hidden");
            });
        document.querySelectorAll(".start-fallback-input, .end-fallback-input")
            .forEach((el) => {
                el.classList.add("hidden");
            });
    } else {
        document.querySelectorAll(".start-date-input, .end-date-input")
            .forEach((el) => {
                el.classList.add("hidden");
            });
        document.querySelectorAll(".start-fallback-input, .end-fallback-input")
            .forEach((el) => {
                el.classList.remove("hidden");
            });
    }
}

