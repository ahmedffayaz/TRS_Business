import '@popperjs/core'

import $ from 'jquery'
import select2 from 'select2/dist/js/select2.full'
select2()
import Swal from 'sweetalert2'
import Quill from 'quill';
import Showdown from 'showdown'
window.jQuery = window.$ = $;

import ApexCharts from 'apexcharts'
import '../assets/js/app-menu'
import '../assets/js/app-core'
import 'toastr'
import '../assets/vendors/editors/markdown-text-editor-master/js/editor.min'
import MTE from '../assets/vendors/editors/markdown-text-editor-master/js/mte'
import flatpickr from 'flatpickr'
import '../assets/js/app-chat'
import '../assets/js/script'
import '../assets/js/custom'
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
window.Showdown = Showdown;
window.MTE = MTE;
window.ApexCharts = ApexCharts;

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.timeGridPlugin = timeGridPlugin;
window.listPlugin = listPlugin;
window.interactionPlugin = interactionPlugin;

