document.querySelectorAll('[data-target="#editModal"]').forEach(item => {
    item.addEventListener('click', async event => {
        var button = event.target;
        var loadoutId = button.getAttribute('data-loadout-id');

        try {
            const response = await fetch('/loadouts/' + loadoutId, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            });
            const loadout = await response.json();

            // Remplissez le formulaire de modification avec les données du loadout
            document.querySelector('#editModal #title').value = loadout.title;

            // Mappez le tableau des paramètres du loadout à un tableau d'ID de paramètres
            const loadoutParameterIds = loadout.parameters.map(parameter => parameter.id);

            document.querySelectorAll('#editModal input[name="parameters[]"]').forEach(input => {
                input.checked = loadoutParameterIds.includes(parseInt(input.value));
            });
        } catch (error) {
            console.error('There was an error:', error);
        }
    });
});

document.querySelectorAll('.add-parameter-button').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelector('#mainModalLabel').textContent = 'Ajouter un paramètre';
        document.querySelector('#mainModalBody').innerHTML = `
            <form method="POST" action="/parameters" class="w-full max-w-lg">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <div class="flex flex-wrap mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="label">
                            Label
                        </label>
                        <input id="label" type="text" name="label" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border rounded appearance-none focus:outline-none focus:bg-white" required>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Créer
                </button>
            </form>
        `;
        const labelInput = document.querySelector('#mainModalBody #label');
        const submitButton = document.querySelector('#mainModalBody button[type="submit"]');
        labelInput.addEventListener('input', () => {
            if (labelInput.value.length > 15) {
                submitButton.disabled = true;
                submitButton.title = 'Le label ne peut pas contenir plus de 15 caractères.';
            } else {
                submitButton.disabled = false;
                submitButton.title = '';
            }
        });
        $('#mainModal').modal('show');
    });
});

document.querySelectorAll('.add-loadout-button').forEach(button => {
    button.addEventListener('click', async () => {
        document.querySelector('#mainModalLabel').textContent = 'Ajouter un modèle';

        // Récupérez les paramètres de votre serveur
        const response = await fetch('/parameters');
        const parameters = await response.json();

        // Générez le HTML pour chaque paramètre
        const parametersHtml = parameters.map(parameter => `
            <div class="flex items-center mt-2">
                <input id="parameter${parameter.id}" type="checkbox" name="parameters[]" value="${parameter.id}" class="w-5 h-5 text-blue-600 form-checkbox">
                <label for="parameter${parameter.id}" class="ml-2 text-sm text-gray-600">${parameter.label}</label>
            </div>
        `).join('');

        document.querySelector('#mainModalBody').innerHTML = `
            <form method="POST" action="/loadouts" class="w-full max-w-lg">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <div class="mb-6 -mx-3">
                    <div class="items-center mt-2">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="title">
                            Titre
                        </label>
                        <input id="title" type="text" name="title" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border rounded appearance-none focus:outline-none focus:bg-white" required>
                    </div>
                </div>
                <div class="flex flex-wrap mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">
                            Paramètres
                        </label>
                        ${parametersHtml}
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Créer
                </button>
            </form>
        `;
        $('#mainModal').modal('show');

        validateParameters();
    });
});

document.querySelectorAll('.edit-button').forEach(button => {
    button.addEventListener('click', async () => {
        var loadoutId = button.getAttribute('data-loadout-id');

        // Récupérez les informations du loadout de votre serveur
        const response = await fetch('/loadouts/' + loadoutId);
        const loadout = await response.json();

        document.querySelector('#mainModalLabel').textContent = 'Modifier le modèle';

        // Récupérez les paramètres de votre serveur
        const responseParameters = await fetch('/parameters');
        const parameters = await responseParameters.json();

        // Générez le HTML pour chaque paramètre
        const parametersHtml = parameters.map(parameter => `
            <div class="flex items-center mt-2">
                <input id="parameter${parameter.id}" type="checkbox" name="parameters[]" value="${parameter.id}" ${loadout.parameters.find(p => p.id === parameter.id) ? 'checked' : ''} class="w-5 h-5 text-blue-600 form-checkbox">
                <label for="parameter${parameter.id}" class="ml-2 text-sm text-gray-600">${parameter.label}</label>
            </div>
        `).join('');

        document.querySelector('#mainModalBody').innerHTML = `
            <form method="POST" action="/loadouts/${loadout.id}" class="w-full max-w-lg">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="PUT">
                <div class="mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="title">
                            Titre
                        </label>
                        <input id="title" type="text" name="title" value="${loadout.title}" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border rounded appearance-none focus:outline-none focus:bg-white">
                    </div>
                </div>
                <div class="mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">
                            Paramètres
                        </label>
                        ${parametersHtml}
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Modifier
                </button>
            </form>
        `;
        $('#mainModal').modal('show');

        validateParameters();
    });
});

function validateParameters() {
    const submitButton = document.querySelector('#mainModalBody button[type="submit"]');
    const selectedParameters = document.querySelectorAll('#mainModalBody input[name="parameters[]"]:checked').length;
    if (selectedParameters < 1 || selectedParameters > 8) {
        submitButton.disabled = true;
        submitButton.title = 'Vous devez sélectionner au moins un paramètre et au maximum 8.';
    } else {
        submitButton.disabled = false;
        submitButton.title = '';
    }

    document.querySelectorAll('#mainModalBody input[name="parameters[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedParameters = document.querySelectorAll('#mainModalBody input[name="parameters[]"]:checked').length;
            if (selectedParameters < 1 || selectedParameters > 8) {
                submitButton.disabled = true;
                submitButton.title = 'Vous devez sélectionner au moins un paramètre et au maximum 8.';
            } else {
                submitButton.disabled = false;
                submitButton.title = '';
            }
        });
    });
}


// Add event listeners to the delete buttons for parameters and loadouts
document.addEventListener('DOMContentLoaded', function () {
    const deleteParams = document.querySelectorAll("#delete-param");
    deleteParams.forEach(deleteParam => {
        deleteParam.addEventListener("click", function (event) {
            event.preventDefault();

            // Définir le formulaire à soumettre
            const form = event.target.closest('form');

            // Show the confirmation dialog
            window.confirmationAlert('Voulez-vous vraiment supprimer ce paramètre ?').then((result) => {
                if (result) {
                    // If the user confirmed, submit the form
                    form.submit();
                }
            });
        });
    });

    const deleteLoads = document.querySelectorAll("#delete-load");
    deleteLoads.forEach(deleteLoad => {
        deleteLoad.addEventListener("click", function (event) {
            event.preventDefault();

            // Show the confirmation dialog
            window.confirmationAlert('Voulez-vous vraiment supprimer ce modèle ?').then((result) => {
                if (result) {
                    // If the user confirmed, submit the form
                    const form = event.target.closest('form');
                    form.submit();
                }
            });
        });
    });
});
