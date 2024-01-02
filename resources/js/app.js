import './bootstrap'

import $ from 'jquery'
import '@popperjs/core'

import select2 from 'select2'
select2();

import Swal from 'sweetalert2'
import '../assets/js/app-menu'
import '../assets/js/app-core'
import 'toastr'
import '../assets/js/script'

// initialize select2 input
var select = $('.select-two');
select.each(function () {
    var $this = $(this);
    $this.wrap('<div class="position-relative"></div>');
    $this.select2({
        // the following code is used to disable x-scrollbar when click in select input and
        // take 100% width in responsive also
        placeholder: 'Select Value',
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $this.parent()
    });
});

$(window).on('load', function () {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }
});

window.$ = window.jQuery = $;
window.Swal = Swal;
