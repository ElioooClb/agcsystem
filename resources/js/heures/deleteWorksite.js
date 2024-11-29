import { flashAlert, confirmationAlert } from "../utils/flashAlert";

document.addEventListener("DOMContentLoaded", () => {
    const deleteButtons = document.querySelectorAll(".delete-btn");

    deleteButtons.forEach((button) => {
        button.addEventListener("click", async (e) => {
            const response = await confirmationAlert(
                "Êtes-vous sûr de vouloir supprimer ce chantier ?",
                "Cette action est irréversible.",
                "warning"
            );
            if (response) {
                try {
                    const reqResponse = await fetch(button.getAttribute("data-url"), {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        },
                    })
                    const data = await reqResponse.json();
                    if (reqResponse.status === 200) {
                        flashAlert("success", data.success);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else if (reqResponse.status === 404) {
                        flashAlert("warning", data.error);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else if (reqResponse.status === 400) {
                        flashAlert("warning", data.error);
                        const response = await confirmationAlert("Voulez-vous forcer la suppression des données associées ?", "!! Cette action est irréversible et très dangeureuse !! les heures des techiciens disparaîtront", "warning")
                        if (response) {
                            const reqResponse = await fetch("/supprimer-chantier-planning/force/"+button.getAttribute("data-id"), {
                                method: "DELETE",
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                  },
                            });
                            const data = await reqResponse.json();
                            if (reqResponse.status === 200) {
                                flashAlert("success", data.success);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                flashAlert("warning", data.error);
                            }
                        }
                    }
                }catch(e){
                    flashAlert("warning", "Une erreur est survenue.");
                }
            }
        });
    });
});
