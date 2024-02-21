import '@popperjs/core'

import Swal from 'sweetalert2'
import './select2.full.js'
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

window.$ = window.jQuery = $;
window.Swal = Swal;
