@assets
    <style>
        html .navbar-floating.footer-static .app-content .content-area-wrapper,
        html .navbar-floating.footer-static .app-content .kanban-wrapper {
            min-height: 550px;
            height: calc(var(--vh, 1vh) * 100 - calc(calc(2rem * 1) + 4.45rem + 18.35rem + 1.3rem + 0rem));
        }
    </style>
@endassets
<div>
    <div class="chat-application">
        <div class="content-area-wrapper container-xxl p-0">
            {{-- <div class="content-right"> --}}
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
                                    @foreach ($comments as $comment)
                                        <div class="chat {{ $comment?->fromUser?->id !== auth()->user()->id ? 'chat-left' : '' }}">
                                            <div class="chat-avatar">
                                                <span class="avatar box-shadow-1 cursor-pointer">
                                                    @if ($comment?->fromUser?->avatar)
                                                    <img src="{{ getUserAvatar($comment?->fromUser?->avatar) }}"
                                                        alt="avatar" height="36" width="36" />
                                                    @else
                                                        <div class="avatar-content">{{ $comment?->fromUser?->avatarName }}</div>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="chat-body">
                                                <div class="chat-content">
                                                    <p class="mb-1">{{ $comment?->description }}</p>
                                                    <small><b>Created at:</b> {{ formatDate($comment?->created_at) }}</small>
                                                </div>
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="divider">
                                        <div class="divider-text">Yesterday</div>
                                    </div>
                                </div>
                            </div>
                            <!-- User Chat messages -->

                            <!-- Submit Chat form -->
                            <form class="chat-app-form" action="javascript:void(0);" onsubmit="enterChat();">
                                <div class="input-group input-group-merge me-1 form-send-message">
                                    <span class="time input-group-text" wire:click="openModal">
                                        <i data-feather="clock" class="cursor-pointer"></i>
                                    </span>
                                    <input type="text" class="form-control message"
                                        placeholder="Type your message or use speech to text" />
                                    <span class="input-group-text">
                                        <label for="attach-doc" class="attachment-icon form-label mb-0">
                                            <i data-feather="image" class="cursor-pointer text-secondary"></i>
                                            <input type="file" id="attach-doc" hidden />
                                        </label>
                                    </span>
                                </div>
                                <button type="button" class="btn btn-primary send" onclick="enterChat();">
                                    <i data-feather="send" class="d-lg-none"></i>
                                    <span class="d-none d-lg-block">Send</span>
                                </button>
                            </form>
                            <!--/ Submit Chat form -->
                        </div>
                        <!--/ Active Chat -->
                    </section>
                    <!--/ Main chat area -->
                </div>
            </div>
            {{-- </div> --}}
        </div>
    </div>
</div>
