import './bootstrap';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Configure toastr defaults
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    preventDuplicates: false,
    onclick: null,
    showDuration: '300',
    hideDuration: '1000',
    timeOut: '5000',
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};

// Make toastr globally available
window.toastr = toastr;

// Listen for Livewire toast events
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (event) => {
        const data = event[0] || event;
        const type = data.type || 'info';
        const message = data.message || '';
        const title = data.title || '';

        toastr[type](message, title);
    });

    // Individual event listeners for each type
    Livewire.on('toast:success', (event) => {
        const data = event[0] || event;
        toastr.success(data.message || data, data.title || 'Success');
    });

    Livewire.on('toast:error', (event) => {
        const data = event[0] || event;
        toastr.error(data.message || data, data.title || 'Error');
    });

    Livewire.on('toast:warning', (event) => {
        const data = event[0] || event;
        toastr.warning(data.message || data, data.title || 'Warning');
    });

    Livewire.on('toast:info', (event) => {
        const data = event[0] || event;
        toastr.info(data.message || data, data.title || 'Info');
    });
});
