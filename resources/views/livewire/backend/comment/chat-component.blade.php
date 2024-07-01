@assets
<style>
    html .navbar-floating.footer-static .app-content .content-area-wrapper,
    html .navbar-floating.footer-static .app-content .kanban-wrapper {
        min-height: 550px;
        height: calc(var(--vh, 1vh) * 100 - calc(calc(2rem * 1) + 4.45rem + 18.35rem + 1.3rem + 0rem));
    }

    .user-chats {
        overflow-y: auto;
    }

    .chats {
        height: 100px;
    }

    .error-list {
        list-style: none;
    }

    .inputs {
        padding: 10px;
    }

    .has-error input,
    .has-error select,
    .has-error textarea {
        border-color: red;
    }
</style>
@endassets
<div>
    <div class="chat-application">
        <div class="content-area-wrapper container-xxl p-0">
            <div class="content-wrapper container-xxl p-0">
                <div class="content-body">
                    <div class="body-content-overlay"></div>
                    <!-- Main chat area -->
                    <section class="chat-app-window">
                        <!-- Active Chat -->
                        <div class="active-chat">
                            <!-- Chat Header -->
                            <div class="chat-navbar">
                                <header class="chat-header">
                                    <div class="d-flex align-items-center">
                                        <div class="design-group">
                                            <div class="avatar avatar-border m-0 me-1">
                                                @if ($task?->user?->avatar)
                                                <img src="{{ getUserAvatar($task?->user?->avatar) }}" alt="avatar"
                                                    height="36" width="36" />
                                                <span class="avatar-status-busy"></span>
                                                @else
                                                <div class="avatar-content">{{ $task?->user?->avatarName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <h6 class="mb-0">{{ $task?->user?->fullName }}</h6>
                                    </div>
                                </header>
                            </div>
                            <!--/ Chat Header -->

                            <!-- User Chat messages -->
                            <div class="user-chats">
                                <div class="chats">
                                    @php
                                    $lastDate = null;
                                    @endphp
                                    <div class="text-center">
                                        @if($hideShowMoreButton)
                                        <x-button class="btn-dark btn-sm" wire:click="loadMore" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Show more</span>
                                            <x-button-loader />
                                        </x-button>
                                        @endif
                                    </div>
                                    @foreach ($comments->sortBy('id') as $comment)
                                        @php
                                            $commentDate = $comment->created_at->format('Y-m-d');
                                            $isToday = ($commentDate === now()->format('Y-m-d'));
                                            $isYesterday = ($commentDate === now()->subDay()->format('Y-m-d'));
                                            $isWithinLastWeek = $comment->created_at->greaterThanOrEqualTo(now()->subDays(7));
                                        @endphp

                                        <div
                                            class="chat {{ $comment->fromUser && $comment->fromUser->id !== auth()->user()->id ? 'chat-left' : '' }}">

                                            {{-- Display "Today" divider if it's the first comment of today --}}
                                            @if (($isToday && $lastDate !== 'today') || ($isYesterday && $lastDate !== 'yesterday') || ($isWithinLastWeek && !$isToday && !$isYesterday) || (!$isWithinLastWeek && !$isToday && !$isYesterday))
                                                @php
                                                    if ($isToday){
                                                        $lastDate = 'today';
                                                    }elseif($isYesterday){
                                                        $lastDate = 'yesterday';
                                                    }elseif ($isWithinLastWeek){
                                                        $lastDate =  $comment->created_at->format('l'); // Get full day name
                                                    }elseif (!$isWithinLastWeek && !$isToday && !$isYesterday){
                                                        $lastDate = $comment->created_at->format('F jS, Y');
                                                    }
                                                @endphp

                                                <div class="divider">
                                                    <div class="divider-text">{{ ucfirst($lastDate) }}</div>
                                                </div>
                                            @endif

                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    @if ($comment->fromUser && $comment->fromUser->avatar)
                                                    <img src="{{ getUserAvatar($comment->fromUser->avatar) }}" alt="avatar"
                                                        height="36" width="36" />
                                                    @else
                                                    <div class="avatar-content">{{ $comment->fromUser ?
                                                        $comment->fromUser->avatarName : '' }}</div>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p class="mb-1">{{ $comment->description }}</p>
                                                    <small><b>Created at:</b> {{ formatDate($comment->created_at) }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    {{-- {{ $comments->links() }} <!-- Pagination links --> --}}
                                </div>
                            </div>
                            <!-- User Chat messages -->

                            <!-- Submit Chat form -->
                            <form class="chat-app-form position-relative">
                                <div class="input-group input-group-merge me-1 form-send-message">
                                    @if($isHourModalVisible)
                                    <span class="time input-group-text" wire:click="hideElement"
                                        data-bs-toggle="collapse" href="#collapseShowElement" role="button"
                                        aria-expanded="false" wire:ignore. aria-controls="collapseShowElement">
                                        <i data-feather="arrow-down" class="cursor-pointer"></i>
                                    </span>
                                    @endif
                                    @if(!$isHourModalVisible)
                                    <span class="time input-group-text" data-bs-toggle="collapse"
                                        wire:click="showElement" href="#collapseShowElement" role="button"
                                        aria-expanded="false" wire:ignore. aria-controls="collapseShowElement">
                                        <i data-feather="clock" class="cursor-pointer"></i>
                                    </span>
                                    @endif
                                    <input wire:model="form.description" type="text" class="form-control message"
                                        placeholder="Type your message or use speech to text" />
                                    <span class="input-group-text" wire:ignore.>
                                        <label for="attach-doc" class="attachment-icon form-label mb-0">
                                            <i data-feather="image" class="cursor-pointer text-secondary"></i>
                                            <input type="file" id="attach-doc" hidden />
                                        </label>
                                    </span>
                                </div>
                                <button type="button" class="btn btn-primary send" role="" wire:click="storeChatHours"
                                    wire:submit.prevent>
                                    <i data-feather="send" class="d-lg-none"></i>
                                    <span class="d-none d-lg-block">Send</span>
                                </button>

                                <div class="card collapse position-absolute bottom-50 me-2 no-shadow"
                                    style="margin-right: -1rem !important; margin-left: -1rem; margin-bottom: 28px; box-shadow: none !important; border-radius:0%"
                                    id="collapseShowElement" wire:ignore.self>
                                    <div class="card-body py-0 mt-1 me-2">
                                        <div>
                                            @php
                                            $minutes = \App\Enums\Comment\CommentUnit::MINUTES->value;
                                            $hours = \App\Enums\Comment\CommentUnit::HOURS->value;
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-3 inputs">
                                                    <x-input type="hidden" wire:model="form.task_id" />
                                                    <x-input type="text" name="time" id="time"
                                                        :class="$errors->has('form.time') ? 'error' : ''"
                                                        placeholder="Enter time" wire:model="form.time" />
                                                    @error('form.time')
                                                    <x-input-error :message="$message" />
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 inputs">
                                                    <span wire:ignore.>
                                                        <x-select-input id="unit-select"
                                                            :class="$errors->has('form.unit') ? 'error select2' : 'select2'"
                                                            wire:model="form.unit">
                                                            <option value="">--Select Unit--</option>
                                                            <option value="{{ $minutes }}">{{ $minutes }}</option>
                                                            <option value="{{ $hours }}">{{ $hours }}</option>
                                                        </x-select-input>
                                                    </span>
                                                    @error('form.unit')
                                                    <x-input-error :message="$message" />
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 inputs">
                                                    <x-input type="text" name="dated" id="dated"
                                                        placeholder="2020-09-23"
                                                        :class="$errors->has('form.dated') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                                                        wire:model="form.dated" autocomplete="off" />
                                                    @error('form.dated')
                                                    <x-input-error :message="$message" />
                                                    @enderror
                                                </div>
                                                <div class="col-md-2 inputs" style="margin-top: 9px">
                                                    <x-input-checkbox type="checkbox" id="is-billable"
                                                        name="is_billable" wire:model="form.is_billable"
                                                        statusClass="form-check-success"
                                                        :labelValue="__('Is Billable')" />
                                                    @error('form.is_billable')
                                                    <x-input-error :message="$message" />
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!--/ Submit Chat form -->
                        </div>
                        <!--/ Active Chat -->
                    </section>
                    <!--/ Main chat area -->
                </div>
            </div>
        </div>
    </div>
</div>
@script
<script type="module">
    $(document).ready(function () {
            // Reinitialize select2 on uni dropdown
            Livewire.on('unit-select', (data) => {
                var $select = $('#unit-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formUnit).trigger('change');
            });

            $('#unit-select').on('change', function(event) {
                @this.set('form.unit', $(this).val());
            });

            // Reinitialize flatpickr
            Livewire.dispatch('flatpickr');
            Livewire.on('reinitialize-dispatcher', () => {
                $(document).ready(function () {
                    Livewire.dispatch('flatpickr');
                    Livewire.dispatch('feather-icons');
                })
            })
        });
</script>
@endscript
