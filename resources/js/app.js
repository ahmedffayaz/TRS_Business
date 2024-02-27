import '@popperjs/core'

import $ from 'jquery'
import select2 from 'select2/dist/js/select2.full'
select2()
import Swal from 'sweetalert2'
import Quill from 'quill';
window.jQuery = window.$ = $;

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

    $('.select2').each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
            dropdownAutoWidth: true,
            dropdownParent: $this.parent(),
            width: '100%',
            containerCssClass: 'select-md'
        });
    });
});

document.addEventListener('livewire:init', () => {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }

    $('.select2').each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
            dropdownAutoWidth: true,
            dropdownParent: $this.parent(),
            width: '100%',
            containerCssClass: 'select-md'
        });
    });
});

document.addEventListener('livewire:navigated', () => {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }

    $('.select2').each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
            dropdownAutoWidth: true,
            dropdownParent: $this.parent(),
            width: '100%',
            containerCssClass: 'select-md'
        });
    });
});

window.Swal = Swal;
window.Quill = Quill;
