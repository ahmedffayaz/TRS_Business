    @assets
<style>
    .avatar-group .avatar .avatar-content {
        background-color: unset !important;
    }
</style>
@endassets

<div>
    @section('breadcrumbs', Breadcrumbs::render('project_details', $project))
    <section class="row">
        <div class="col-md-6">
            <div class="result-toggler">
                <h1 class="fs-3 text-capitalize">{{ $project?->name }} (
                    <x-anchor-tag href="#" class="fw-bold text-capitalize" :value="$project?->client?->name" /> )
                </h1>
            </div>
        </div>
        <div class="col-md-6 float-end">
            <div class="view-options float-end d-flex">

                @can('allow_invitation')
                <x-button class="btn btn-primary me-1" type="button" wire:click="openInviteClientModal">
                    Invite Client
                </x-button>
                @endcan
                @can('view_statistics')
                <x-button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" id="btn-project-statistics"
                    data-bs-target="#project-statistics" aria-expanded="false" aria-controls="project-statistics">
                    Project Statistics
                </x-button>
                @endcan
                @can('view_revenue')
                    <x-anchor-tag class="btn btn-primary me-1" href="javascript:void(0);" :value="__('Revenue')"
                    tabindex="0" aria-controls="table-hover" type="button" wire:click="showRevenueModal" wire:ignore. />
                @endcan

                @if ($project->tasks_count == 0 && $project->status->value !== 'delivered' &&
                is_null($project->deleted_at))
                <x-anchor-tag class="btn btn-primary me-1" href="javascript:void(0);" :value="__('Deliver Project')"
                    tabindex="0" aria-controls="table-hover" type="button" wire:ignore. />
                @endif

                <x-anchor-tag href="{{ route('dashboard.projects.index') }}" class="btn btn-primary" value="Back" />
            </div>
        </div>
    </section>

    @can('view_statistics')
    <section class="collapse mt-2" id="project-statistics">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-app-design h-100">
                    <div class="card-body">
                        <h4 class="card-title mt-1 mb-75">{{ $project?->name }}</h4>
                        <p class="card-text font-small-2 mb-2">{!! $project?->detail !!}</p>
                        <div class="design-group row">
                            <div class="col-md-9">
                                <div class="mb-1">
                                    <h6 class="section-label">Business</h6>
                                    <span class="badge badge-light-primary me-1">{{ $project?->client?->business?->name
                                        }}</span>
                                </div>
                                <div>
                                    <h6 class="section-label">Client</h6>
                                    <span class="badge badge-light-primary me-1">{{ $project?->client?->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="avatar bg-light-primary rounded p-1">
                                    <div class="avatar-conent">{{ $project?->tasks_count }}
                                        <div class="fs-5 text-primary">Tasks</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                {!! $project?->description !!}
                            </div>
                        </div>
                        <div class="mb-2">
                            <h6 class="section-label">Members</h6>
                            <div class="avatar-group">
                                @foreach ($project?->members?->take(10) as $member)
                                <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom"
                                    title="{{ $member?->fullName }}"
                                    class="avatar bg-light-{{ randomColors() }} pull-up">
                                    @if ($member?->avatar)
                                    <img src="{{ getUserAvatar($member) }}" alt="Avatar" width="33" height="33" />
                                    @else
                                    <div class="avatar-content">{{ $member?->avatarName }}</div>
                                    @endif
                                </div>
                                @endforeach
                                @if ($project?->members?->count() > 10)
                                <h6 class="align-self-center cursor-pointer ms-50 mb-0">+{{ $project?->members?->count()
                                    - 5 }}</h6>
                                @endif
                            </div>
                        </div>
                        <div class="design-planning-wrapper">
                            <div class="design-planning">
                                <p class="card-text mb-25">Start Date</p>
                                <h6 class="mb-0">{{ formatDate($project?->start_date) }}</h6>
                            </div>
                            <div class="design-planning">
                                <p class="card-text mb-25">Due Date</p>
                                <h6 class="mb-0">{{ formatDate($project?->end_date) }}</h6>
                            </div>
                            <div class="design-planning">
                                <p class="card-text mb-25">Billing Type</p>
                                <h6 class="mb-0">{{ $project?->type?->value }}</h6>
                            </div>
                            @can('view_budget')
                            @if ($project?->type?->value === 'hourly')
                            <div class="design-planning">
                                <p class="card-text mb-25">Hourly Rate</p>
                                <h6 class="mb-0">{{ formatCurrency($project->hourly_rate, $project->currency) }}</h6>
                            </div>
                            @endif
                            @endcan
                            @if (count($project?->invoices) > 0)
                            <div class="design_planning">
                                <div><b>Invoices</b></div>
                                @foreach ($project?->invoices as $invoice)
                                <p>{{ $invoice?->invoice_number . ' - ' . formatCurrency($invoice?->total,
                                    $invoice?->currency) . ' - ' . $invoice?->status->value }}</p>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-transaction h-100">
                    <div class="card-body">
                        <div id="line-chart"></div>
                        <div style="max-height: 200px; overflow-y: auto; margin-bottom: 1rem;">
                            @foreach ($project?->members as $member)
                            <div class="transaction-item">
                                <div class="d-flex">
                                    <div class="avatar bg-light-primary rounded float-start">
                                        <div class="avatar-content">{{ $member?->avatarName }}</div>
                                    </div>
                                    <div class="transaction-percentage">
                                        <h6 class="transaction-title">{{ $member?->fullName }}</h6>
                                        <small>{{ implode(', ', $member->roles->pluck('title')->toArray()) }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-statistics h-100">
                    <div class="card-header">
                        <h4 class="card-title">Status</h4>
                    </div>
                    <div class="card-body statistics-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar bg-light-primary me-2">
                                        <div class="avatar-content">
                                            <i data-feather='meh' class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="fw-bolder mb-0">{{ ucfirst(str_replace('-', ' ',
                                            $project?->status?->value)) }}</h4>
                                        <p class="card-text font-small-3 mb-0">Status</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar bg-light-success me-2">
                                        <div class="avatar-content">
                                            <i data-feather="dollar-sign" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="fw-bolder mb-0">{{ formatCurrency($project?->budget) }}</h4>
                                        <p class="card-text font-small-3 mb-0">Budget</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar bg-light-info me-2">
                                        <div class="avatar-content">
                                            <i data-feather="users" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="fw-bolder mb-0">{{ $project?->members_count }}</h4>
                                        <p class="card-text font-small-3 mb-0">Members</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar bg-light-danger me-2">
                                        <div class="avatar-content">
                                            <i data-feather="box" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="fw-bolder mb-0">{{ $project?->tasks_count }}</h4>
                                        <p class="card-text font-small-3 mb-0">Tasks</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar bg-light-danger me-2">
                                        <div class="avatar-content">
                                            <i data-feather="align-justify" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="fw-bolder mb-0">{{ $project?->completed_tasks_count }}</h4>
                                        <p class="card-text font-small-3 mb-0">Completed Tasks</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar bg-light-warning me-2">
                                        <div class="avatar-content">
                                            <i data-feather="align-center" class="avatar-icon"></i>
                                        </div>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="fw-bolder mb-0">{{ $project?->pending_tasks }}</h4>
                                        <p class="card-text font-small-3 mb-0">Pending Tasks</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endcan

    <section class="mt-3">
        @livewire('backend.task.task-data-component', ['project' => $project?->id, 'projectSlug' => $project?->slug])
    </section>
    <div wire:loading wire:target=""></div>
</div>
@script
<script type="module">
    $(document).ready(function () {
    var dataset = [];
        $('#btn-project-statistics').click(() => {
            if(dataset.length == 0) {
                chartData();
            }
        });
            function chartData() {
                console.log(dataset)
                let projectSlug = "{{ $project->slug }}";
                let path = "{{ route('dashboard.project.chart-data', ':projectSlug') }}"
                // replace :projectSlug with projectSlug
                path = path.replace(':projectSlug', projectSlug);
                $.ajax({
                    url: path,
                    type : 'GET',
                    success: function(response) {
                        const data = response.data.chartData;
                        const dates = response.data.dates
                        dataset = data.map(item => {
                            return item[1];
                        });
                        if (data.length > 0) {
                            var flatPicker = $('.flat-picker'),
                            isRtl = $('html').attr('data-textdirection') === 'rtl',
                            chartColors = {
                                column: { series1: '#826af9', series2: '#d2b0ff', bg: '#f8d3ff' },
                                success: { shade_100: '#7eefc7', shade_200: '#06774f' },
                                donut: { series1: '#ffe700', series2: '#00d4bd', series3: '#826bf8', series4: '#2b9bf4', series5: '#FFA1A1' },
                                area: { series3: '#a4f8cd', series2: '#60f2ca', series1: '#2bdac7' }
                            };
                            // Line Chart
                            // --------------------------------------------------------------------
                            var lineChartEl = document.querySelector('#line-chart');
                            var lineChartConfig = {
                                    chart: {
                                        height: 200,
                                        type: 'line',
                                        zoom: { enabled: false },
                                        parentHeightOffset: 0,
                                        toolbar: { show: false }
                                    },
                                    series: [
                                        { data: dataset }
                                    ],
                                    markers: {
                                        strokeWidth: 7,
                                        strokeOpacity: 1,
                                        strokeColors: [window.colors.solid.white],
                                        colors: [window.colors.solid.warning]
                                    },
                                    dataLabels: { enabled: false },
                                    stroke: { curve: 'straight' },
                                    colors: [window.colors.solid.warning],
                                    grid: {
                                        xaxis: { lines: { show: true } },
                                        padding: { top: -20 }
                                    },
                                    tooltip: {
                                        custom: function (newData) {
                                            return (
                                                '<div class="px-1 py-50">' +
                                                '<span><b>Time:</b> ' + newData.series[newData.seriesIndex][newData.dataPointIndex] + ' hrs</span>' +
                                                '</div>'
                                            );
                                        }
                                    },
                                    xaxis: {
                                        categories: dates
                                    },
                                    yaxis: { opposite: isRtl }
                                };
                            // Ensure lineChartEl exists before initializing ApexCharts
                            if (typeof lineChartEl !== 'undefined' && lineChartEl !== null) {
                                var lineChart = new ApexCharts(lineChartEl, lineChartConfig);
                                lineChart.render(); // Render the chart
                            }
                        }
                    },
                    error: function(response) {
                        console.error(response);
                    }
                })
            }
    });

</script>
@endscript
