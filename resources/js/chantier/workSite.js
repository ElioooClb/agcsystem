// DEBUT - [SPECGT9] Ajout de la logique des checkbox pour l'état des chantiers
import { flashAlert } from '../utils/flashAlert';
const workSites = document.querySelectorAll('tbody tr');

document.addEventListener('DOMContentLoaded', function () {
    const btnArchived = document.getElementById('checkArchived');
    const btnInProgress = document.getElementById('checkInProgress');
    const btnToBill = document.getElementById('checkToBill');
    const dataTable = $('#example').DataTable();

    let firstLoad = true;

    function handleChange() {
        updateWorkSites(btnInProgress, btnToBill, btnArchived)
    }
    function handleState(e) {
        onState(e);
    }

    function addEventListeners() {
        const btnToHandleStates = document.querySelectorAll('.onState');
        btnToHandleStates.forEach(item => {
            item.removeEventListener('click', handleState, false);
            item.addEventListener('click', handleState, false);
        });
    }

    if (firstLoad) {
        btnArchived.addEventListener('change', handleChange);
        btnInProgress.addEventListener('change', handleChange);
        btnToBill.addEventListener('change', handleChange);
        addEventListeners();
        firstLoad = false;
    }

    dataTable.on('draw.dt', function () {
        addEventListeners();
    });
});

/**
 * Handle the archive and unarchive action of a work site
 * @param {Object} event
 * @returns {void}
 */
function onState(event) {
    event.preventDefault();
    const url = "/handle-state";
    const id = event.target.getAttribute('data-id');
    const state = event.target.getAttribute('data-target-state');
    const action = event.target.getAttribute('data-action');
    const data = {
        id: id,
        state: state,
        action: action
    }
    const formData = new FormData();
    formData.append('_method', "PUT");
    formData.append('id', id);
    formData.append('state', state);
    formData.append('action', action);
    fetch(url, {
        method: 'PUT',
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify(data),
    })
        .then(response => {
            if (response.status === 200) {
                flashAlert("success", "Le chantier a été mis à jour avec succès");
                // Update the state of the button
                const linkElement = document.querySelector(`a[data-id="${id}"]`);
                let trElement = null;
                for (let i = 0; i < workSites.length; i++) {
                    if (workSites[i].id === event.target.dataset.id) {
                        trElement = workSites[i];
                        break;
                    }
                }
                if (action === "update") {
                    linkElement.dataset.targetState = "pendingArchiving";
                    linkElement.dataset.action = "downgrade";
                    linkElement.innerHTML = "Désarchiver";
                    const totalHours = trElement.dataset.totalhours ? parseInt(trElement.dataset.totalhours) : 0;
                    const hours = trElement.dataset.hours ? parseInt(trElement.dataset.hours) : 0;
                    if (totalHours > hours) {
                        cleanTr(trElement);
                        trElement.classList.add('bg-danger', 'text-white');
                    } else if (totalHours == hours) {
                        cleanTr(trElement);
                        trElement.classList.add('bg-warning', 'text-black');
                    } else {
                        cleanTr(trElement);
                        trElement.classList.add('bg-success', 'text-white');
                    }
                    trElement.setAttribute('data-status', 'archived');
                }

                if (action === "downgrade") {
                    linkElement.dataset.targetState = "archived";
                    linkElement.dataset.action = "update";
                    linkElement.innerHTML = "Archiver";
                    cleanTr(trElement);
                    trElement.classList.add('text-black');
                    trElement.setAttribute('data-status', 'toBill');
                }
                updateWorkSites(document.getElementById('checkInProgress'), document.getElementById('checkToBill'), document.getElementById('checkArchived'));
            } else {
                flashAlert("Une erreur est survenue lors de la mise à jour du chantier", "error");
            }
        })
        .catch(error => {
            flashAlert("Une erreur est survenue lors de la mise à jour du chantier", "error");
        });
}

/**
 * Update the work sites data table based on the checkbox checked
 * @param {DOMElement} btnInProgress
 * @param {DOMElement} btnToBill
 * @param {DOMElement} btnArchived
 * @returns {void}
 */
function updateWorkSites(btnInProgress, btnToBill, btnArchived) {
    const isCheckedInProgress = btnInProgress.checked;
    const isCheckedToBill = btnToBill.checked;
    const isCheckedArchived = btnArchived.checked;

    // Filter the work sites
    let filteredWorkSites = Array.from(workSites).filter(item => {
        const status = item.getAttribute('data-status');
        // Handling the visibliity of all the work sites to display or hide archived work sites
        if (isCheckedArchived && status === 'archived') item.classList.remove('hidden');
        if (!isCheckedArchived && status === 'archived') item.classList.add('hidden');
        return (isCheckedInProgress && status === 'inProgress') || (isCheckedToBill && status === 'toBill') || (isCheckedArchived && status === 'archived');
    });

    // Handling the case where no checkbox is checked (default case)
    if (!isCheckedArchived && !isCheckedInProgress && !isCheckedToBill) {
        filteredWorkSites = Array.from(workSites).filter(item => {
            if (item.getAttribute('data-status') === 'archived') item.classList.remove('hidden');
            return true;
        });
    }
    // How to handle the DataTable
    // 1. Get the current DataTable instance (jquery method DataTable())
    const TABLE = $('#example').DataTable();
    // 2. Clear the table content
    TABLE.rows().remove();
    // 3. Add the filtered data
    filteredWorkSites.forEach(item => {
        TABLE.row.add(item);
    });
    // 4. Redraw the table
    TABLE.draw();
}

/**
 * Clean the classes of a tr element
 * @param {DOMElement} element
 * @returns {void}
 */
function cleanTr(element) {
    element.classList.remove('bg-danger', 'bg-warning', 'bg-success', 'text-white', 'text-black');
}
// FIN - [SPECGT9] Ajout de la logique des checkbox pour l'état des chantiers


