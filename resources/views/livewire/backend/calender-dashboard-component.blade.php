@push('styles')
    <style>
        .calendar-avatar {
            width: 28px;
            height: 28px;
            display: -webkit-box;
            display: flex;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: center;
            justify-content: center;
            color: #ffffff;
            font-weight: bold;
            background: #7e93a3;
            border-radius: 50%;
        }

        .flex-space-between {
            display: -webkit-box;
            display: flex;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            justify-content: space-between;
        }

        .fc-popover {
            max-height: 350px;
            overflow-y: auto;
            z-index: 1000 !important;
        }

        .fc-popover .event-title {
            width: 250px !important;
        }

        .fc-popover .fc-event-title,
        .fc-popover .fc-event-description {
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .fc-event-main {
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
    <style>
        .fc .fc-toolbar {
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-box-orient: horizontal !important;
            -webkit-box-direction: normal !important;
            -webkit-flex-direction: row !important;
            -ms-flex-direction: row !important;
            flex-direction: row !important
        }

        .fc .fc-toolbar .fc-next-button,
        .fc .fc-toolbar .fc-prev-button {
            display: inline-block;
            background-color: transparent;
            border-color: transparent
        }

        .fc .fc-toolbar .fc-next-button .fc-icon,
        .fc .fc-toolbar .fc-prev-button .fc-icon {
            color: #6E6B7B
        }

        .fc .fc-toolbar .fc-next-button:active,
        .fc .fc-toolbar .fc-next-button:focus,
        .fc .fc-toolbar .fc-next-button:hover,
        .fc .fc-toolbar .fc-prev-button:active,
        .fc .fc-toolbar .fc-prev-button:focus,
        .fc .fc-toolbar .fc-prev-button:hover {
            background-color: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important
        }

        .fc .fc-toolbar .fc-button-group .fc-button:focus,
        .fc .fc-toolbar .fc-button:active,
        .fc .fc-toolbar .fc-button:focus {
            box-shadow: none
        }

        .fc .fc-toolbar .fc-prev-button {
            padding-left: 0 !important
        }

        .fc .fc-toolbar .fc-toolbar-chunk:first-child {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap
        }

        .app-calendar .event-sidebar .card .todo-item-action .dropdown-toggle::after,
        .fc .fc-timegrid .fc-timegrid-divider,
        .fc .fc-toolbar .fc--button:empty,
        .fc .fc-toolbar .fc-toolbar-chunk:empty {
            display: none
        }

        .fc .fc-toolbar .fc-button {
            padding: .438rem .5rem
        }

        .fc .fc-toolbar .fc-button-group .fc-button {
            text-transform: capitalize
        }

        .fc .fc-toolbar .fc-button-group .fc-button-primary:not(.fc-prev-button):not(.fc-next-button) {
            background-color: transparent;
            border-color: #7367F0;
            color: #7367F0
        }

        .fc .fc-toolbar .fc-button-group .fc-button-primary:not(.fc-prev-button):not(.fc-next-button).fc-button-active,
        .fc .fc-toolbar .fc-button-group .fc-button-primary:not(.fc-prev-button):not(.fc-next-button):hover {
            background-color: rgba(115, 103, 240, .2) !important;
            border-color: #7367F0 !important;
            color: #7367F0
        }

        .fc .fc-toolbar .fc-button-group .fc-button-primary.fc-sidebarToggle-button {
            border: 0
        }

        .fc .fc-toolbar .fc-button-group .fc-button-primary.fc-sidebarToggle-button i,
        .fc .fc-toolbar .fc-button-group .fc-button-primary.fc-sidebarToggle-button svg {
            height: 21px;
            width: 21px;
            font-size: 21px
        }

        .fc .fc-toolbar .fc-button-group .fc-sidebarToggle-button {
            padding-left: 0;
            background-color: transparent !important;
            color: #6E6B7B !important
        }

        .fc .fc-toolbar .fc-button-group .fc-sidebarToggle-button:not(.fc-prev-button):not(.fc-next-button):hover {
            background-color: transparent !important
        }

        .fc .fc-toolbar .fc-button-group .fc-sidebarToggle-button+div {
            margin-left: 0
        }

        .fc .fc-toolbar .fc-button-group .fc-dayGridMonth-button,
        .fc .fc-toolbar .fc-button-group .fc-listMonth-button,
        .fc .fc-toolbar .fc-button-group .fc-timeGridDay-button,
        .fc .fc-toolbar .fc-button-group .fc-timeGridWeek-button {
            padding: .55rem 1.5rem
        }

        .fc .fc-toolbar .fc-button-group .fc-dayGridMonth-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-dayGridMonth-button:last-child,
        .fc .fc-toolbar .fc-button-group .fc-listMonth-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-listMonth-button:last-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridDay-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridDay-button:last-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridWeek-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridWeek-button:last-child {
            border-radius: .358rem
        }

        .fc .fc-toolbar .fc-button-group .fc-dayGridMonth-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-listMonth-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridDay-button:first-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridWeek-button:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0
        }

        .fc .fc-toolbar .fc-button-group .fc-dayGridMonth-button:last-child,
        .fc .fc-toolbar .fc-button-group .fc-listMonth-button:last-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridDay-button:last-child,
        .fc .fc-toolbar .fc-button-group .fc-timeGridWeek-button:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0
        }

        .fc .fc-toolbar>*>:not(:first-child) {
            margin-left: 0
        }

        .fc .fc-toolbar .fc-toolbar-title {
            margin-left: .25rem
        }

        .fc tbody td,
        .fc thead th {
            border-color: #EBE9F1
        }

        .fc tbody td.fc-col-header-cell,
        .fc thead th.fc-col-header-cell {
            border-right: 0;
            border-left: 0
        }

        .fc .fc-view-harness {
            min-height: 650px
        }

        .fc .fc-scrollgrid-section-liquid>td {
            border-bottom: 0
        }

        .fc .fc-daygrid-event-harness .fc-event {
            font-size: .8rem;
            font-weight: 600;
            padding: .25rem .5rem
        }

        .fc .fc-daygrid-day-bottom,
        .fc .fc-daygrid-event-harness+.fc-daygrid-event-harness {
            margin-top: .3rem !important
        }

        .fc .fc-daygrid-day {
            padding: 5px
        }

        .fc .fc-daygrid-day .fc-daygrid-day-top {
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -webkit-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row
        }

        .fc .fc-daygrid-day-number,
        .fc .fc-list-event-time,
        .fc .fc-timegrid-slot-label-cushion {
            color: #6E6B7B
        }

        .fc .fc-day-today {
            background: #F8F8F8 !important
        }

        .fc .fc-timegrid .fc-scrollgrid-section .fc-col-header-cell,
        .fc .fc-timegrid .fc-scrollgrid-section .fc-timegrid-axis {
            border-color: #EBE9F1;
            border-left: 0;
            border-right: 0
        }

        .app-calendar .fc-scrollgrid,
        .fc .fc-list,
        .fc .fc-list .fc-list-event td,
        .fc .fc-timegrid .fc-scrollgrid-section .fc-timegrid-axis {
            border-color: #EBE9F1
        }

        .fc .fc-timegrid .fc-timegrid-axis.fc-scrollgrid-shrink .fc-timegrid-axis-cushion {
            text-transform: capitalize;
            color: #B9B9C3
        }

        .fc .fc-timegrid .fc-timegrid-slots .fc-timegrid-slot {
            height: 3rem
        }

        .fc .fc-timegrid .fc-timegrid-slots .fc-timegrid-slot .fc-timegrid-slot-label-frame {
            text-align: center
        }

        .fc .fc-timegrid .fc-timegrid-slots .fc-timegrid-slot .fc-timegrid-slot-label-frame .fc-timegrid-slot-label-cushion {
            text-transform: uppercase
        }

        .fc .fc-list .fc-list-day-cushion {
            background: #F8F8F8
        }

        .fc .fc-list .fc-list-event:hover td {
            background-color: #F8F8F8
        }

        .app-calendar {
            position: relative;
            border-radius: .428rem;
            margin-bottom: 2rem
        }

        .app-calendar .app-calendar-sidebar {
            position: absolute;
            left: calc(-18rem - 1.2rem);
            width: 18rem;
            height: 100%;
            z-index: 5;
            background-color: #FFF;
            border-right: 1px solid #EBE9F1;
            -webkit-flex-basis: 18rem;
            -ms-flex-preferred-size: 18rem;
            flex-basis: 18rem;
            -webkit-transition: all .2s, background 0s, border 0s;
            transition: all .2s, background 0s, border 0s
        }

        .app-calendar .app-calendar-sidebar.show {
            left: 0
        }

        .app-calendar .app-calendar-sidebar .sidebar-content-title {
            font-size: .85rem;
            color: #B9B9C3;
            text-transform: uppercase;
            letter-spacing: .6px
        }

        .app-calendar .app-calendar-sidebar .input-filter~label,
        .app-calendar .app-calendar-sidebar .select-all~label {
            color: #5E5873;
            font-weight: 500;
            letter-spacing: .4px
        }

        .app-calendar .event-sidebar {
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            z-index: 15;
            -webkit-transform: translateX(120%);
            -ms-transform: translateX(120%);
            transform: translateX(120%);
            -webkit-transition: all .3s ease-in-out;
            transition: all .3s ease-in-out
        }

        .app-calendar .event-sidebar .card {
            height: calc(100vh - 12.96rem);
            height: calc(var(--vh, 1vh) * 100 - 12.96rem);
            border-radius: 0 .25rem .25rem 0
        }

        .app-calendar .event-sidebar .card .close-bar {
            cursor: pointer
        }

        .app-calendar .event-sidebar .card .todo-item-action {
            width: 6rem
        }

        .app-calendar .event-sidebar .card .todo-item-action .dropdown,
        .app-calendar .event-sidebar .card .todo-item-action .todo-item-favorite,
        .app-calendar .event-sidebar .card .todo-item-action .todo-item-info {
            cursor: pointer;
            line-height: 1.5
        }

        .app-calendar .event-sidebar .card .todo-item-action .dropdown .dropdown-menu .dropdown-item {
            padding: .14rem 1.428rem
        }

        .app-calendar .event-sidebar.show {
            -webkit-transform: translateX(0);
            -ms-transform: translateX(0);
            transform: translateX(0)
        }

        .app-calendar .fc-toolbar h2 {
            font-size: 1.45rem
        }

        .app-calendar .fc-header-toolbar {
            margin-bottom: 1.75rem !important
        }

        .app-calendar .fc-view-harness {
            margin: 0 -1.6rem
        }

        .app-calendar .fc-day-future .fc-daygrid-day-number,
        .app-calendar .fc-day-past .fc-daygrid-day-number {
            color: #B9B9C3
        }

        .app-calendar .fc-popover {
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, .1)
        }

        .app-calendar .fc-popover .fc-popover-header {
            background: 0 0;
            padding: .5rem
        }

        .app-calendar .fc-popover .fc-popover-header .fc-popover-close,
        .app-calendar .fc-popover .fc-popover-header .fc-popover-title {
            color: #5E5873
        }

        .app-calendar .fc-popover .fc-popover-body :not(:last-of-type) {
            margin-bottom: .3rem
        }

        .app-calendar .fc .fc-event .fc-event-main {
            color: inherit
        }

        .app-calendar .fc-list-event {
            background: 0 0 !important
        }

        .event-sidebar .select2-selection__choice__remove:before {
            top: 40% !important
        }

        .horizontal-layout .app-calendar {
            margin-bottom: 1rem
        }

        @media (max-width:992px) {
            .fc .fc-sidebarToggle-button {
                font-size: 0
            }
        }

        @media (min-width:992px) {
            .app-calendar .app-calendar-sidebar {
                position: static;
                height: auto;
                box-shadow: none !important
            }

            .app-calendar .app-calendar-sidebar .flatpickr-days {
                background-color: transparent
            }

            .fc .fc-sidebarToggle-button {
                display: none
            }
        }

        @media (max-width:700px) {
            .app-calendar .fc .fc-header-toolbar .fc-toolbar-chunk:last-of-type {
                margin-top: 1rem
            }
        }
    </style>
@endpush
<div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">

        </div>
        <div class="content-body">
            @if (session()->has('status'))
                <div class="alert alert-success p-1" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif
            <!-- Full calendar start -->
            <section>
                <div class="app-calendar overflow-hidden border">
                    <div class="row g-0">
                        <!-- Sidebar -->
                        <div class="col app-calendar-sidebar flex-grow-0 overflow-hidden d-flex flex-column"
                            id="app-calendar-sidebar">
                            <div class="sidebar-wrapper">
                                <div class="card-body pb-0">
                                    <h5 class="section-label mb-1">
                                        <span class="align-middle">Filter</span>
                                    </h5>
                                    <div class="form-check form-check-info mb-1">
                                        <input type="checkbox" class="form-check-input select-all" id="select-all"
                                            checked />
                                        <label class="form-check-label" for="select-all">View All</label>
                                    </div>
                                    <div class="calendar-events-filter">
                                        <div class="form-check form-check-danger mb-1">
                                            <input type="checkbox" class="form-check-input input-filter" id="attendance"
                                                data-value="attendance" checked />
                                            <label class="form-check-label" for="attendance">Attendance</label>
                                        </div>
                                        <div class="form-check form-check-success mb-1">
                                            <input type="checkbox" class="form-check-input input-filter" id="tasks"
                                                data-value="tasks" checked />
                                            <label class="form-check-label" for="tasks">Tasks</label>
                                        </div>
                                        <div class="form-check form-check-primary mb-1">
                                            <input type="checkbox" class="form-check-input input-filter" id="projects"
                                                data-value="projects" checked />
                                            <label class="form-check-label" for="projects">Projects</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <img src="{{ asset('assets/images/calendar-illustration.png') }}"
                                    alt="Calendar illustration" class="img-fluid" />
                            </div>
                        </div>
                        <!-- /Sidebar -->
                        <!-- Calendar -->
                        <div class="col position-relative">
                            <div class="card shadow-none border-0 mb-0 rounded-0">
                                <div class="card-body pb-0" wire:ignore>
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                        <!-- /Calendar -->
                        <div class="body-content-overlay"></div>
                    </div>
                </div>
            </section>
            <!-- Full calendar end -->
        </div>
    </div>

    <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeLeaveModal" modalTitle="Request Leaves" formSubmit="submitLeaveRequest">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <x-input-label for="start_date" class="required" value="Start Date" />

                    <x-input type="text" id="start_date" class="form-control flatpickr-basic"
                        wire:model="form.start_date" />

                    @error('form.start_date')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="col-md-6 mb-1">
                    <x-input-label for="end_date" class="required" value="End Date" />

                    <x-input type="text" id="end_date" class="form-control flatpickr-basic"
                        wire:model="form.end_date" />

                    @error('form.end_date')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="col-md-12 mb-1">
                    <x-input-label for="is_working" class="required" value="Is Working" />
                    <x-select-input id="is_working" wire:model="form.is_working" :class="$errors->has('form.is_working') ? 'error' : ''">
                        <option>Select leave type</option>
                        <option value="0">Leave</option>
                        <option value="1">Work From Home</option>
                        <option value="2">Half Leave</option>
                    </x-select-input>
                    @error('form.is_working')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="col-md-12 mb-1">
                    <x-input-label for="reason" value="Detail" />
                    <x-textarea id="reason" rows="4" wire:model="form.reason" :class="$errors->has('form.reason') ? 'error' : ''" />
                    @error('form.reason')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="col-md-12 text-center d-flex justify-content-end">
                    <x-button class="btn btn-primary" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save</span>
                        <span wire:loading>
                            <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                        </span>
                    </x-button>
                </div>
            </div>
    </x-main-modal>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script type="module">
        $(document).ready(function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
                headerToolbar: {
                    left: 'prev,next title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                initialView: 'dayGridMonth',
                views: {
                    dayGridMonth: {
                        dayMaxEventRows: 3,
                    }
                },
                nowIndicator: true,
                editable: false,
                nextDayThreshold: '00:00:00',
                fixedWeekCount: true,
                showNonCurrentDates: true,
                displayEventTime: false,
                events: function(info, successCallback, failureCallback) {
                    const selectedTypes = [];
                    $('.input-filter:checked').each(function() {
                        selectedTypes.push($(this).data('value'));
                    });

                    $.ajax({
                        url: '/dashboard/events',
                        method: 'GET',
                        data: {
                            start: info.startStr,
                            end: info.endStr,
                            types: selectedTypes.join(',')
                        },
                        success: function(data) {
                            successCallback(data);
                            setTimeout(function() {
                                var tooltipTriggerList = [].slice.call(document
                                    .querySelectorAll(
                                        '[data-bs-toggle="tooltip"]'));
                                var tooltipList = tooltipTriggerList.map(function(
                                    tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(
                                        tooltipTriggerEl, {
                                            container: 'body'
                                        });
                                });
                            }, 500);
                        },
                        error: function() {
                            failureCallback();
                        }
                    });
                },
                eventContent: function(info) {
                    return {
                        html: info.event.title
                    };
                },
                eventDidMount: function(event, element) {

                },
                eventOverlap: false,
                slotEventOverlap: false,
                unselectAuto: true,
                dateClick: function(info) {
                    var start = moment(info.dateStr, "YYYY-MM-DD").format("YYYY-MM-DD");
                    var current = new Date();
                    var currentFormatted = current.getFullYear() + '-' + (current.getUTCMonth() + 1)
                        .toString().padStart(2, '0') + '-' + current.getDate().toString().padStart(2,
                            '0');
                    var days = (Math.floor((new Date(start) - new Date(currentFormatted)) / 86400000));

                    if (days >= 0) {
                        flatpickr('.flatpickr-basic', {
                            defaultDate: start,
                            setDate: start,
                        });

                        @this.set('form.end_date', start);
                        @this.set('form.start_date', start);

                        Livewire.dispatch('open-main-modal');
                    }
                },
                eventClick: function(info) {

                    var calEvent = info.event;
                    var can_edit = calEvent.extendedProps.can_edit;
                    var user_id = calEvent.extendedProps.user_id;
                    var can_delete = calEvent.extendedProps.can_delete;

                    if (!calEvent.extendedProps.user_id) {
                        return;
                    }

                    // Check if  editable
                    if (can_edit) {
                        Swal.fire({
                            text: "Select your action",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Edit',
                            cancelButtonText: 'Delete',
                            focusConfirm: true
                        }).then((result) => {
                            if (result.isConfirmed) {

                                $('#leave_id').val(calEvent.extendedProps.evt.id);
                                $('#is_working').val(calEvent.extendedProps.evt.is_working)
                                    .change();
                                $('#reason').val(calEvent.extendedProps.evt.reason);

                                flatpickr('#start_date', {
                                    defaultDate: calEvent.extendedProps.evt.start_date,
                                });
                                flatpickr('#end_date', {
                                    defaultDate: calEvent.extendedProps.evt.end_date,
                                });

                                @this.set('form.end_date', calEvent.extendedProps.evt
                                    .start_date);
                                @this.set('form.start_date', calEvent.extendedProps.evt
                                    .end_date);
                                @this.set('form.id', calEvent.extendedProps.evt.id);

                                Livewire.dispatch('open-main-modal');

                            } else if (result.dismiss === Swal.DismissReason.cancel) {

                                if (can_delete) {
                                    Livewire.dispatch('destroy-leave', [calEvent.extendedProps
                                        .evt.id
                                    ]);
                                }
                            }
                        });
                    }
                },
                eventDragStop: function eventDragStop(info) {
                    calendar.refetchEvents();
                },
            });

            calendar.render();

            // Re-fetch events when any checkbox changes
            $(document).on('change', '.input-filter', function() {
                calendar.refetchEvents();
            });

            // Handle 'View All' checkbox
            $('#select-all').change(function() {
                if ($(this).is(':checked')) {
                    $('.input-filter').prop('checked', true);
                } else {
                    $('.input-filter').prop('checked', false);
                }
                calendar.refetchEvents();
            });

            // Handle individual checkbox changes
            $('.input-filter').change(function() {
                // Check if all checkboxes are checked
                var allChecked = $('.input-filter').length === $('.input-filter:checked').length;
                $('#select-all').prop('checked', allChecked);
                calendar.refetchEvents();
            });
            // Handle event clickv show hide description
            $(document).on('click', '.event-title', function() {
                $(this).find('.fc-description').toggleClass('d-none');
            });

            Livewire.on('reinitialize-calendar', () => {
                calendar.refetchEvents();
                calendar.render();
            });

        });
    </script>
@endpush
