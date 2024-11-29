import { flashAlert } from '../utils/flashAlert';

document.addEventListener('alpine:init', function() {
    window.Alpine.data('formHandler', () => ({
        submitForm(e) {
            e.preventDefault();
            fetch(this.$el.getAttribute('action'), {
                method: this.$el.getAttribute('method'),
                body: new FormData(this.$el),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                let icon = data.errors ? 'error' : 'success';
                flashAlert(icon, data.message);
            })
            .catch(error => {
                flashAlert('error', 'An error occurred. Please try again later. If the problem persists, contact support.');
            });

        }
    }));
});
