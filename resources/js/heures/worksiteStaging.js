import Swal from 'sweetalert2';
import { flashAlert, confirmationAlert } from '../utils/flashAlert';
import tippy from 'tippy.js';

document.addEventListener('DOMContentLoaded', () => {
    const stagingBtns = document.querySelectorAll('.staging-btn');

    stagingBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            confirmationAlert('Voulez-vous vraiment passer à l\'étape suivante ?')
                .then((result) => {
                    if (result) {
                        const chantierId = btn.getAttribute('data-id');
                        const nextStage = btn.getAttribute('data-next-stage');
                        const currentStage = btn.getAttribute('data-current-stage');

                        const url = `/worksite/${chantierId}/staging/${nextStage}`;
                        
                        
                    }
                })
        })
    })
})

        
