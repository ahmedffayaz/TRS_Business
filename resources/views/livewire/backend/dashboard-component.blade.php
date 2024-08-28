@section('breadcrumbs', Breadcrumbs::render('dashboard'))
<div>
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section id="dashboard-ecommerce">
            @if (session()->has('status'))
            <div class="alert alert-success p-1" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('status') }}
            </div>
        @endif
        @if (session()->has('error'))
        <div class="alert alert-error alert-danger p-1" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 3000)">
            {{ session('error') }}
        </div>
    @endif
            <div class="row match-height">
                <!-- Statistics Card -->
                <div class="col-12">
                    <div class="row">
                        <div class="col-xl-4 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-primary p-50 mb-1">
                                        <div class="avatar-content">
                                            <i data-feather="users" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="fw-bolder">36.9k</h2>
                                    <p class="card-text">Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-info p-50 mb-1">
                                        <div class="avatar-content">
                                            <i data-feather="package" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="fw-bolder">36.9k</h2>
                                    <p class="card-text">Companies</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-info p-50 mb-1">
                                        <div class="avatar-content">
                                            <i data-feather="user-check" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="fw-bolder">36.9k</h2>
                                    <p class="card-text">Clients</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-info p-50 mb-1">
                                        <div class="avatar-content">
                                            <i data-feather="file-text" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="fw-bolder">36.9k</h2>
                                    <p class="card-text">Projects</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-body">
                                    <div class="avatar bg-light-info p-50 mb-1">
                                        <div class="avatar-content">
                                            <i data-feather="check-circle" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="fw-bolder">36.9k</h2>
                                    <p class="card-text">Tasks</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <div id='calendar'></div>

</div>
@if($show_swl)
<script type="module">
  window.Swal.fire({
    title: 'Confirmation Required',
    text: 'Please confirm the invitation.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Accept',
    cancelButtonText: 'Reject',
  }).then((result) => {
    if (result.isConfirmed) {
        Livewire.dispatch('accept_invitation_dashboard')
    } else {
        Livewire.dispatch('reject_invitation_dashboard')
    }
  });
</script>
@endif
@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function(){
        const calendarEl = document.getElementById('calendar')
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                 right: 'dayGridMonth,dayGridWeek,dayGridDay'
            },
            initialView: 'dayGridWeek',
            allDaySlot: false,
            nowIndicator: true,
            editable: false,
            resizable: false,
            nextDayThreshold: '00:00:00',
            fixedWeekCount: false,
            showNonCurrentDates: false,
            displayEventTime: false,
            // events: {
            //         url: 'https://cms.therightsw.com/dashboard/events?type=' + 'task',
            //         method: 'GET',
            //         success: function success() {
            //             setTimeout(function () {
            //             $(document).find('[data-toggle="tooltip"]').tooltip();
            //             }, 500);
            //         },
            //         failure: function failure() {
            //             alert('there was an error while fetching events!');
            //         }
            //     },
            events: [
            {
                "id": 1,
                "title": "First Line<br>Second Line",
                "start": "2024-08-28",
                "end": null,
                "allDay": true,
                "editable": true,
                "className": "badge-soft-warning",

            }
        ],

      eventContent: function( info ) {
          return {html: info.event.title};
      }
        })
        calendar.render()
    });
</script>
@endpush
