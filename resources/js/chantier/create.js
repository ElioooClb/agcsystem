const btnAssign = document.querySelector('.btn-assign');
const ulListeTeck = document.querySelector('.ulListeTeck');
const assignedUsersInput = document.getElementById('assignedUsersInput');

btnAssign.addEventListener('click', function () {
    const selectedUsers = document.querySelectorAll('.selected-users option:checked');

    selectedUsers.forEach(user => {
        const li = document.createElement('li');
        li.className = 'd-flex assignedUser';
        li.innerHTML = `
            <p>${user.textContent}</p>
            <button class="deleteAssignedUser" data-id-user="${user.value}" data-name-user="${user.textContent}" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-trash">
                    <path
                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                    <path
                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                </svg>
            </button>
        `;
        ulListeTeck.appendChild(li);

        // Ajouter un gestionnaire d'événements de clic au bouton de suppression nouvellement créé
        const deleteButton = li.querySelector('.deleteAssignedUser');
        deleteButton.addEventListener('click', function (event) {
            const userId = deleteButton.getAttribute('data-id-user');
            const userName = deleteButton.getAttribute('data-name-user');

            const option = document.createElement('option');
            option.className = 'text-2xl';
            option.value = userId;
            option.text = userName;
            document.querySelector('.selected-users').add(option);

            li.remove();
            updateAssignedUsersInput();
        });

        // Retirer l'option sélectionnée des techniciens disponibles
        user.remove();
        updateAssignedUsersInput();
    });
});

function updateAssignedUsersInput() {
    const assignedUserIds = Array.from(ulListeTeck.querySelectorAll('.assignedUser')).map(li => li.querySelector('.deleteAssignedUser').getAttribute('data-id-user'));
    assignedUsersInput.value = JSON.stringify(assignedUserIds);
}
