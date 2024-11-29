import Swal from "sweetalert2";
import { flashAlert } from "../utils/flashAlert";

document.addEventListener("livewire:load", function () {
    const hoursCells = document.querySelectorAll(".hoursCell");
    const serviceAmountCells = document.querySelectorAll(".serviceAmountCell");

    hoursCells.forEach((cell) => {
        cell.removeEventListener("click", handleHoursClick);
        cell.addEventListener("click", handleHoursClick);
    });

    serviceAmountCells.forEach((cell) => {
        cell.removeEventListener("click", handleServiceAmount);
        cell.addEventListener("click", handleServiceAmount);
    });
});

document.addEventListener("livewire:update", function () {
    const hoursCells = document.querySelectorAll(".hoursCell");
    const serviceAmountCells = document.querySelectorAll(".serviceAmountCell");

    hoursCells.forEach((cell) => {
        cell.removeEventListener("click", handleHoursClick);
        cell.addEventListener("click", handleHoursClick);
    });

    serviceAmountCells.forEach((cell) => {
        cell.removeEventListener("click", handleServiceAmount);
        cell.addEventListener("click", handleServiceAmount);
    });
});

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
