import '@popperjs/core'

import Swal from 'sweetalert2'
import '../assets/js/app-menu'
import '../assets/js/app-core'
import 'toastr'
import '../assets/js/script'

document.addEventListener('DOMContentLoaded', () => {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }
});

document.addEventListener('livewire:init', () => {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }
});

document.addEventListener('livewire:navigated', () => {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }
});

window.$ = window.jQuery = $;
window.Swal = Swal;
