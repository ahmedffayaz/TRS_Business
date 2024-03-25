<div>
    @section('breadcrumbs', Breadcrumbs::render('task_details', $task))
    <div class="row mb-2">
        <div class="col-md-6">
            <div class="card card-apply-job">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="mb-0">{{ $task?->name }} : Detail</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Project</th>
                                    <td>{{ $task?->project?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Priority</th>
                                    <td>{{ $task?->priority }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>{{ $task?->user?->fullName }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ formatDate($task?->start_date) }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ formatDate($task?->end_date) }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ formatDate($task?->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>Completed</th>
                                    <td>{{ $task?->completed_at ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Billed At</th>
                                    <td>{{ formatDate($task?->billed_at) }}</td>
                                </tr>
                                <tr>
                                    <th>Time Spent</th>
                                    <td>{{ formatTime($task?->comments->sum('time')) }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{!! $task?->description !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @livewire('backend.comment.chat-component', ['taskId' => $task->id])
        </div>
    </div>
    @livewire('backend.comment.comment-component', ['taskId' => $task->id])
</div>

@script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });
        });
    </script>
@endscript
