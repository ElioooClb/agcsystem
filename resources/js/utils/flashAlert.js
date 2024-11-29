import Swal from 'sweetalert2';
import 'animate.css';

/**
 * Display a flash alert message
 * @param {string} type - default: 'info' - success, error, warning, info, question
 * @param {string} message - the message to display
 * @param {int} duration - the duration of the alert
 */
export function flashAlert(icon = 'info', message, duration = 3000) {
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: duration,
        timerProgressBar: true,
    });
    toast.fire({
        icon: icon,
        text: message,
        showClass: {
            popup: `
              animate__animated
              animate__fadeInRight
              animate__faster
            `
        },
        hideClass: {
            popup: `
              animate__animated
              animate__fadeOutRight
              animate__faster
            `
        }
    });
}

/**
 * Display a confirmation alert
 * @param {string} title - the title of the alert
 * @param {string} message - default: '' - the message to display
 * @param {string} icon - default: warning - the icon to display
 * @returns
 */
export function confirmationAlert(title = 'Êtes-vous sûr de vouloir procéder?', message = '', icon = 'warning') {
    return new Promise((resolve) => {
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Valider',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Annuler',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                cancelButton: 'order-1 bg-gray-500 text-dark font-bold py-2 px-2 rounded opacity-50',
                confirmButton: 'order-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg mx-3',
            }
        })
            .then((result) => {
                resolve(result.isConfirmed);
            });
    })
};
