import Swal from "sweetalert2";
import { flashAlert } from "../utils/flashAlert";

document.addEventListener("livewire:load", function () {
    initListeners(); // Initialise les listeners sur les cellules et le checkbox
});

document.addEventListener("livewire:update", function () {
    initListeners(); // Réinitialise les listeners après une mise à jour Livewire
});

function initListeners() {
    const hoursCells = document.querySelectorAll(".hoursCell");
    const serviceAmountCells = document.querySelectorAll(".serviceAmountCell");
    const averageHourlyRateCell = document.querySelector("#tdAvgHourlyRate");
    const toggleArchivedCheckbox = document.getElementById("toggleArchived");

    // Ajouter les listeners aux cellules
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

    // Ajouter le listener pour le checkbox
    if (toggleArchivedCheckbox) {
        toggleArchivedCheckbox.removeEventListener("change", handleToggleArchived);
        toggleArchivedCheckbox.addEventListener("change", handleToggleArchived);
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
