import Swal from 'sweetalert2';
import { flashAlert, confirmationAlert } from '../utils/flashAlert';
import tippy from 'tippy.js';

/*
|--------------------------------------------------------------------------
| Work site state management [SPECGT21] - [SPECGT15] - [SPECGT16]
|--------------------------------------------------------------------------
|
| This file is used to manage the state of a work site.
| The logic is based on getting fields defined in such a way:
|
| - <field class='example' data-id='exampleId' hidden?></field>
| - document.querySelector('.example[data-id="exampleId"]');
|
| Tips: To rework the logic, you should get the id from the worksite button and either create a cookie or a window object if needed.
| NB: JS file are cleaned and minified on 'build' so do not fear to the comments
|
| The logic is based on the following :
| 1. Listening to the user
| 2. The listener ==> handler function
| 3. The handler function ==> onStateTransition function
| 3. The onStateTransition function ==> request function
| 4. While calling back, each function updates the DOM accordingly
|
| --------------------------------------------------------------------------
| ACCOUNTANT ACTIONS
| --------------------------------------------------------------------------
|
| handleValidationStateTransition is used to handle the transition from the invoice validation button
| state button color :                gray -> green|orange
| invoice (validation|delete) button color :   green -> red
|
| --------------------------------------------------------------------------
| SUPERVISOR ACTIONS
| --------------------------------------------------------------------------
|
| handleStateTransition is used to handle the transition from the state button
| state button color :                green -> gray
| invoice (validation|delete) button color :   red -> green
|
*/

document.addEventListener('DOMContentLoaded', function () {
    const invoiceStateBtns = document.querySelectorAll('.invoiceStateBtn');
    const invoiceReturnStateBtns = document.querySelectorAll('.invoiceReturnStateBtn');
    const invoiceValidateBtns = document.querySelectorAll('.invoiceValidateBtn');
    const invoiceDeleteBtns = document.querySelectorAll('.invoiceDeleteBtn');
    const invoiceInputNumbers = document.querySelectorAll('.invoiceInputNumber');

    // FORMATTING DATE
    // Will need rework deprecated
    const rawDate = document.querySelectorAll('.formattedDate');
    moment.updateLocale('fr', {
        months: 'janvier_février_mars_avril_mai_juin_juillet_août_septembre_octobre_novembre_décembre'.split('_'),
        monthsShort: 'janv._févr._mars_avr._mai_juin_juil._août_sept._oct._nov._déc.'.split('_'),
        weekdays: 'dimanche_lundi_mardi_mercredi_jeudi_vendredi_samedi'.split('_'),
        weekdaysShort: 'dim._lun._mar._mer._jeu._ven._sam.'.split('_'),
        weekdaysMin: 'Di_Lu_Ma_Me_Je_Ve_Sa'.split('_'),
        longDateFormat: {
            LT: 'HH:mm',
            L: 'DD/MM/YYYY',
            LL: 'D MMMM YYYY',
            LLL: 'D MMMM YYYY LT',
            LLLL: 'dddd D MMMM YYYY LT'
        },
    });
    rawDate.forEach(element => {
        const formattedDate = window.moment(element.innerHTML).format('DD MMMM YYYY');
        element.innerText = formattedDate;
    });

    // Worksite update state management by the accountant actions (Click event)
    // Loop through all invoiceValidateBtns and add an event listener to each
    invoiceValidateBtns.forEach(invoiceValidateBtn => {
        invoiceValidateBtn.addEventListener('click', function (e) {
            e.preventDefault();
            // Get the value of the input field and check if it's not empty
            const value = findField(invoiceValidateBtn.dataset.id, 'invoiceInputNumber').value;
            if (value) {
                // Asking for confirmation
                confirmationAlert('Voulez-vous vraiment valider ce numéro de facture ?')
                    .then((result) => {
                        // Check if the user confirmed the action
                        if (result) {
                            const id = invoiceValidateBtn.dataset.id;
                            const currentState = invoiceValidateBtn.dataset.status;

                            // Request to update the invoice number
                            updateInvoiceRequest(id, value)
                                .then(() => {
                                    // PARTIALLY BILLED TO PENDING APPROVAL
                                    if (currentState === 'partiallyBilled') {
                                        handleValidationStateTransition(id, 'pendingApproval', 'green');
                                    }

                                    // BILLABLE TO PENDING ARCHIVING
                                    if (currentState === 'billable') {
                                        handleValidationStateTransition(id, 'pendingArchiving', 'orange');
                                    }
                                });
                            flashAlert('success', 'Le numéro de facture a été validé avec succès');
                        } else {
                            flashAlert('info', 'Opération annulée');
                        }
                    });
            } else {
                flashAlert('error', 'Veuillez saisir un numéro de facture');
            }
        });
    });

    // Worksite update state management by the accountant actions (Enter key)
    // Loop through all invoiceInputNumbers and add an event listener to each
    invoiceInputNumbers.forEach(invoiceInputNumber => {
        invoiceInputNumber.addEventListener('keydown', function (e) {
            if (e.key === 'Entrer' || e.keyCode === 13) {
                const id = invoiceInputNumber.dataset.id;
                const currentState = invoiceInputNumber.dataset.status;
                const value = invoiceInputNumber.value;

                if (value && value !== '') {
                    // Request to update the invoice number
                    updateInvoiceRequest(id, value)
                        .then(() => {
                            // PARTIALLY BILLED TO PENDING APPROVAL
                            if (currentState === 'partiallyBilled') {
                                handleValidationStateTransition(id, 'pendingApproval', 'green');
                            }

                            // BILLABLE TO PENDING ARCHIVING
                            if (currentState === 'billable') {
                                handleValidationStateTransition(id, 'pendingArchiving', 'orange');
                            }
                        });
                    flashAlert('success', 'Le numéro de facture a été validé avec succès');
                } else {
                    flashAlert('error', 'Veuillez saisir un numéro de facture');
                }
            }
        });
    });

    // Worksite downgrade state management (trashbin)
    invoiceDeleteBtns.forEach(invoiceDeleteBtn => {
        invoiceDeleteBtn.addEventListener('click', function (e) {
            e.preventDefault();

            // Building the html alert message
            const html = document.createElement('section');
            const p = document.createElement('p');
            const img = document.createElement('img');

            // Container
            html.classList.add('flex', 'flex-row', 'items-center', 'justify-center');

            // Paragraph text
            p.textContent = 'Que souhaitez-vous faire ?';
            p.classList.add('m-0', 'p-0');

            // Image tooltip
            img.src = '/front/images/tooltip.svg';
            img.alt = 'Icone de l\'infobulle';
            img.width = 16;
            img.height = 16;
            img.classList.add('ml-2');

            // Tooltip
            tippy(img, {
                content: 'La touche \'Entrer\' valide la suppression',
            });

            html.appendChild(p);
            html.appendChild(img);

            Swal.fire({
                title: 'Actions',
                html: html,
                icon: 'question',
                showDenyButton: true,
                denyButtonText: 'Supprimer le numéro de facture',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Récupération du numéro de facture',
                customClass: {
                    actions: 'vertical-buttons',
                    cancelButton: 'order-1 bg-gray-500 text-dark font-bold py-2 px-2 rounded opacity-50',
                    confirmButton: 'order-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg mx-3',
                    denyButton: 'order-3 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-2 rounded-lg',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    handleInvoiceNumber('recover', invoiceDeleteBtn.dataset.id);
                    flashAlert('success', 'Le numéro de facturation à été récupéré avec succès');
                } else if (result.isDenied) {
                    handleInvoiceNumber('delete', invoiceDeleteBtn.dataset.id);
                    flashAlert('success', 'Le numéro de facturation à été supprimé avec succès');
                } else {
                    flashAlert('info', 'Opération annulée');
                }
            });
        });
    });

    // Worksite update state management by the supervisor actions
    // Loop through all invoiceStateBtns and add an event listener to each
    invoiceStateBtns.forEach(invoiceStateBtn => {
        invoiceStateBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const status = invoiceStateBtn.dataset.status;

            // INITIAL => BILL (PARTIAL OR FINAL)
            if (status === 'initial') {
                showBillingTypeModal('Facturation finale', 'Facturation partielle').then((result) => {
                    switch (result) {
                        case 'confirm':
                            handleStateTransition(invoiceStateBtn, 'partiallyBilled', 'gray');
                            enableReturn(invoiceStateBtn.dataset.id);
                            flashAlert('success', 'La facture partielle a été demandée');
                            break;
                        case 'deny':
                            handleStateTransition(invoiceStateBtn, 'billable', 'gray');
                            enableReturn(invoiceStateBtn.dataset.id);
                            flashAlert('success', 'La facture finale a été demandée');
                            break;
                        default:
                            flashAlert('info', 'Opération annulée');
                            break;
                    }
                });
            }

            // PENDING APPROVAL => BILLABLE
            if (status === 'pendingApproval') {
                showBillingTypeModal('Facturation finale', 'Facturation partielle').then((result) => {
                    switch (result) {
                        case 'confirm':
                            handleStateTransition(invoiceStateBtn, 'partiallyBilled', 'gray', 'downgrade');
                            flashAlert('success', 'La facture partielle a été demandée');
                            break;
                        case 'deny':
                            handleStateTransition(invoiceStateBtn, 'billable', 'gray');
                            flashAlert('success', 'La facture finale a été demandée');
                            break;
                        default:
                            flashAlert('info', 'Opération annulée');
                            break;
                    }
                });
            }

            // PENDING ARCHIVING STATE => TO ARCHIVED
            if (status === 'pendingArchiving') {
                confirmationAlert('Voulez-vous vraiment archiver ce chantier ?', 'ATTENTION : Cette action est irréversible, le chantier sera archivé et ne pourra plus être modifié.')
                    .then((result) => {
                        if (result) {
                            handleStateTransition(invoiceStateBtn, 'archived', 'gray');

                            // Hide the worksite container
                            const liEl = document.querySelector(`.menu-item[data-id-chantier="${invoiceStateBtn.dataset.id}"]`);
                            liEl.classList.add('hidden');

                            // Hide the modal
                            const id = 'chantierModal_' + invoiceStateBtn.dataset.id;
                            const modal = document.getElementById(id);
                            modal.style.display = 'none';

                            flashAlert('success', 'Le chantier a été archivé avec succès');
                        } else {
                            flashAlert('info', 'Opération annulée');
                        }
                    });
            }
        });
    });

    invoiceReturnStateBtns.forEach(invoiceReturnStateBtn => {
        invoiceReturnStateBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const status = invoiceReturnStateBtn.dataset.status;

            if (status === 'pendingArchiving') {
                confirmationAlert('Voulez-vous vraiment Retourner à la facturation finale ?')
                    .then((result) => {
                        if (result) {
                            handleStateTransition(findField(invoiceReturnStateBtn.dataset.id, 'invoiceStateBtn'), 'billable', 'gray', 'downgrade');
                            flashAlert('success', 'Retour à l\'état de facturation final effectué avec succès');
                        } else {
                            flashAlert('info', 'Opération annulée');
                        }
                    });
            }

            if (status === 'billable') {
                showBillingTypeModal('État initial', 'Facturation partielle').then((result) => {
                    if (result) {
                        switch (result) {
                            case 'confirm':
                                handleStateTransition(findField(invoiceReturnStateBtn.dataset.id, 'invoiceStateBtn'), 'partiallyBilled', 'gray', 'downgrade');
                                flashAlert('success', 'Retour à l\'état de facturation partielle effectué avec succès');
                                break;
                            case 'deny':
                                handleStateTransition(findField(invoiceReturnStateBtn.dataset.id, 'invoiceStateBtn'), 'initial', 'green', 'downgrade');
                                enableReturn(invoiceReturnStateBtn.dataset.id, false);
                                flashAlert('success', 'Retour à l\état initial effectué avec succès');
                                break;
                            default:
                                flashAlert('info', 'Opération annulée');
                                break;
                        }
                    }
                });
            }

            if (status === 'pendingApproval') {
                confirmationAlert('Voulez-vous vraiment retourner à la facturation partielle ?')
                    .then((result) => {
                        if (result) {
                            handleStateTransition(findField(invoiceReturnStateBtn.dataset.id, 'invoiceStateBtn'), 'partiallyBilled', 'gray', 'downgrade');
                            flashAlert('success', 'Retour à l\'état initial effectué avec succès');
                        } else {
                            flashAlert('info', 'Opération annulée');
                        }
                    });
            }

            if (status === 'partiallyBilled') {
                confirmationAlert('Voulez-vous vraiment retourner à l\'état initial ?')
                    .then((result) => {
                        if (result) {
                            handleStateTransition(findField(invoiceReturnStateBtn.dataset.id, 'invoiceStateBtn'), 'initial', 'green', 'downgrade');
                            enableReturn(invoiceReturnStateBtn.dataset.id, false);
                            flashAlert('success', 'Retour à l\'état initial effectué avec succès');
                        } else {
                            flashAlert('info', 'Opération annulée');
                        }
                    });
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Commons alerts
    |--------------------------------------------------------------------------
    |
    | functions for multiple uses
    |
    */

    /**
     * Show a modal to choose the billing type
     * @param {string} confirmButtonText - the text for the confirm button
     * @param {string} denyButtonText - the text for the deny button
     * @returns {Promise}
     */
    function showBillingTypeModal(confirmButtonText, denyButtonText) {
        return new Promise((resolve, reject) => {
            Swal.fire({
                title: 'Choisir le type de facturation',
                text: 'Appuyer sur entrer pour valider une facturation partielle',
                icon: 'question',
                showDenyButton: true,
                denyButtonText: confirmButtonText,
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: denyButtonText,
                customClass: {
                    cancelButton: 'order-1 bg-gray-500 text-dark font-bold py-2 px-2 rounded opacity-50',
                    confirmButton: 'order-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg mx-3',
                    denyButton: 'order-3 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    resolve('confirm'); // Résoudre la promesse avec le type de facturation partiel
                } else if (result.isDenied) {
                    resolve('deny'); // Résoudre la promesse avec le type de facturation final
                } else {
                    reject('cancelled'); // Rejeter la promesse si l'utilisateur annule
                }
            });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Handle states transitions
    |--------------------------------------------------------------------------
    |
    | functions waiting for the worksite supervisor actions
    |
    */

    /**
     * Handle the transition from initial to toBill group_state
     * @param {DOMElement<button>} invoiceStateBtn the invoice button
     * @param {string} newState the new state to update to
     * @param {string} color default: gray - the color for the button
     * @param {string} action default: update - the action to perform (update|downgrade)
     * @param {boolean} disableInvoice default: false - whether to disable the invoice button or not
     * @returns {void}
     */
    function handleStateTransition(invoiceStateBtn, newState, color = 'gray', action = 'update', disableInvoice = false) {
        onStateTransition(invoiceStateBtn.dataset.id, newState, action, disableInvoice)
            .then(worksite => {
                switch (action) {
                    case 'update':
                        // Update the invoice button color
                        updateColors(invoiceStateBtn, color);

                        // Disable the invoice button
                        invoiceStateBtn.disabled = true;

                        // Show the container for the state field
                        showField(findField(worksite.id, 'stateContainer'))

                        // Update the requestedAtDate span field
                        updateFieldFromInvoice('date', findField(worksite.id, 'requestedAtDate'), worksite, 'requested_at');
                        break;
                    case 'downgrade':
                        // Update the invoice button color
                        updateColors(invoiceStateBtn, color);

                        if (newState === 'initial') {
                            findField(worksite.id, 'stateContainer').setAttribute('hidden', true);
                        }

                        if (newState === 'billable' || newState === 'partiallyBilled') {
                            invoiceStateBtn.disabled = true;
                        } else {
                            invoiceStateBtn.disabled = false;
                        }
                        break;
                }
            });
    }

    /**
     * Handle the transition to a new state from the validation button
     * @param {int} id - the worksite id
     * @param {string} newState - the new state
     * @param {string} color - the color and hover:color for the button
     * @returns {void}
     */
    function handleValidationStateTransition(id, newState, color) {
        // Handle the transition to a new state
        onStateTransition(id, newState, 'update', true);

        const stateBtn = findField(id, 'invoiceStateBtn');

        // Update the color of the button
        updateColors(stateBtn, color);

        // Remove the disabled attribute from the invoice state button
        stateBtn.removeAttribute('disabled');
    }

    /**
     * Handle the invoice number
     * @param {string} action - the action to perform (delete|recover)
     * @param {int} id - the worksite id
     * @returns {void}
     */
    async function handleInvoiceNumber(action, id) {
        switch (action) {
            case 'delete':
                deleteInvoiceNumberRequest(id);
                // Clear input
                const inputEl = findField(id, 'invoiceInputNumber');
                inputEl.value = '';
                break;
            case 'recover':
                const invoice = await recoverInvoiceNumberRequest(id);
                if (invoice) {
                    // Update input
                    findField(id, 'invoiceInputNumber').value = invoice.number;
                }
                break;
        }
    }

    /**
     * Handle the transition to a new state
     * @param {int} id - the worksite id
     * @param {string} newState - the new state
     * @param {string} action - default: update - the action to perform (update|downgrade)
     * @param {boolean} disableInvoice - whether to disable the invoice button or not
     * @returns {Promise}
     */
    async function onStateTransition(id, newState, action = 'update', disableInvoice) {
        const worksite = await updateStateRequest(id, newState, action);
        // Update the state of the button
        updateStateContent(worksite);

        // Update the validation button and input field
        updateValidationDOM(worksite, disableInvoice ? 'disable' : 'enable');
        return worksite;
    }

    /*
    |--------------------------------------------------------------------------
    | Request functions
    |--------------------------------------------------------------------------
    |
    | All the functions to handle the request to change states
    |
    */

    /**
    * Request to update the state of a worksite
    * @param {string} id worksite id
    * @param {string} state initial|partiallyBilled|pendingApproval|billable|pendingArchiving|archived
    * @param {string} action update|downgrade
    * @return {Promise}
    */
    async function updateStateRequest(id, state, action) {
        const requestData = {
            id: id,
            state: state,
            action: action,
            return: true,
        };
        try {
            const response = await fetch('/handle-state', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData),
            });
            const data = await response.json();
            return data.additionalData;
        } catch (error) {
            flashAlert('error', 'Erreur lors de la mise à jour de l\'état du chantier : ' + error);
        }
    }

    /**
     * Request to update the invoice number of a worksite
     * @param {int} id
     * @param {string} invoiceNumber
     * @returns {Promise}
     */
    async function updateInvoiceRequest(id, invoiceNumber) {
        const requestData = {
            id: id,
            number: invoiceNumber,
        };
        try {
            const response = await fetch('/handle-invoice', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData),
            });
            const data = await response.json();
            return data.additionalData;
        } catch (error) {
            flashAlert('error', 'Erreur lors de la mise à jour de la facture : ' + error);
        }
    }

    /**
     * Request to delete an invoice number
     * @param {int} id - the invoice id to delete
     * @returns {Promise}
     */
    async function deleteInvoiceNumberRequest(workSiteId) {
        const requestData = {
            id: workSiteId,
        };
        try {
            const response = await fetch('/delete-invoice', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData),
            });
            const data = await response.json();
            return data.additionalData;
        } catch (error) {
            flashAlert('error', 'Erreur lors de la suppression de la facture : ' + error);
        }
    }

    async function recoverInvoiceNumberRequest(workSiteId) {
        const requestData = {
            id: workSiteId,
        };
        try {
            const response = await fetch('/recovery-invoice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData),
            });
            const data = await response.json();
            if (data.errors) {
                flashAlert('error', data.errors);
                return null;
            }
            return data.additionalData;
        } catch (error) {
            flashAlert('error', 'Erreur lors de la récupération de la facture : ' + error);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DOM manipulation functions
    |--------------------------------------------------------------------------
    |
    | All the functions to handle the DOM between states
    |
    */

    /**
     * Update the color and hover color of a DOM element
     * @param {DOMElement} invoiceStateBtn the invoice button
     * @param {string} tailwindcss tailwindcss color name ex: green
     * @returns {void}
     */
    function updateColors(DOMElement, color) {
        DOMElement.classList.remove("bg-green-500", "hover:bg-green-700", "bg-orange-500", "hover:bg-orange-700", "bg-gray-500", "hover:bg-gray-700");
        const newColor = 'bg-' + color + '-500';
        const newHover = 'hover:bg-' + color + '-700';
        DOMElement.classList.add(newColor, newHover);
    }

    /**
     * Find the field element with the given class name and worksite id
     * @param {int} worksiteId - the worksite id
     * @param {string} className - the class name of the field
     * @returns {DOMElement} the field element
     */
    function findField(worksiteId, className) {
        return document.querySelector(`.${className}[data-id="${worksiteId}"]`);
    }

    /**
     * remove the hidden attribute from the field
     * @param {DOMElement} field
     * @returns {void}
     */
    function showField(field) {
        field.removeAttribute('hidden');
    }

    /**
     * Activate/Desactivate the return button
     * @param {int} id - the worksite id
     * @param {boolean} isEnable - default: true - whether to enable the return button or not
     * @returns {void}
     */
    function enableReturn(id, isEnable = true) {
        const invoiceReturnStateBtns = findField(id, 'invoiceReturnStateBtn');
        const imgEl = invoiceReturnStateBtns.querySelector('img');
        isEnable ? invoiceReturnStateBtns.removeAttribute('disabled') : invoiceReturnStateBtns.setAttribute('disabled', true);
        isEnable ? imgEl.src = "/front/images/return-icon.svg" : imgEl.src = "/front/images/return-icon-disabled.svg";
    }

    /**
     * Update the state status and textContent of every element with the same data-id
     * @param {Object} worksite - the object containing the worksite id and the new state
     * @returns {void}
     */
    function updateStateContent(worksite) {
        findField(worksite.id, 'invoiceStateBtn').textContent = worksite.states.label;
        findField(worksite.id, 'invoiceValidateBtn').dataset.status = worksite.states.status;
        findField(worksite.id, 'invoiceStateBtn').dataset.status = worksite.states.status;
        findField(worksite.id, 'invoiceDeleteBtn').dataset.status = worksite.states.status;
        findField(worksite.id, 'invoiceReturnStateBtn').dataset.status = worksite.states.status;
    }

    /**
     * Update the validation button and input field
     * @param {Object} worksite - the object containing the worksite id and the new state
     * @param {string} action - the action to perform (enable|disable)
     * @returns {void}
     */
    function updateValidationDOM(worksite, action) {
        const validBtnEL = findField(worksite.id, 'invoiceValidateBtn');
        const trashBtnEl = findField(worksite.id, 'invoiceDeleteBtn');
        const imgValidateEl = validBtnEL.querySelector('img');
        const imgTrashbinEl = trashBtnEl.querySelector('img');
        const inputEl = findField(worksite.id, 'invoiceInputNumber');
        switch (action) {
            case 'enable':
                validBtnEL.disabled = false;
                trashBtnEl.disabled = false;
                imgValidateEl.src = "/front/images/validate-icon.svg";
                imgTrashbinEl.src = "/front/images/trashbin-icon.svg";
                inputEl.disabled = false;
                break;
            case 'disable':
                validBtnEL.disabled = true;
                trashBtnEl.disabled = true;
                imgValidateEl.src = "/front/images/validate-icon-disabled.svg";
                imgTrashbinEl.src = "/front/images/trashbin-icon-disabled.svg";
                inputEl.disabled = true;
                break;
        }
    }

    /**
 * Update the field textContent from the invoice object
 * @param {string} action - the action to perform (date|number)
 * @param {DOMElement} field - the field to update
 * @param {Object} worksite - default: null - the worksite object
 * @param {string} value - default: null - the object key to get the value from
 * @param {boolean} show - default: false - whether to show the field or not
 * @returns {void}
 */
    function updateFieldFromInvoice(action, field, worksite = null, value = null, show = false) {
        show ?? showField(field);
        switch (action) {
            case 'date':
                field.textContent = window.moment(worksite.invoices[value]).format('DD MMMM YYYY');
                break;
            case 'number':
                field.textContent = worksite.invoices[value];
                break;
        }
    }
});
