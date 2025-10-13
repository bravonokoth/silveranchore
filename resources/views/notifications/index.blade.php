@php
    $user = Auth::user();
    $isAdmin = $user && $user->hasRole(['admin', 'super-admin']);
@endphp

@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section($isAdmin ? 'page-title' : 'header')
    {{ __('Inbox') }}
@endsection

@section('content')
    <div class="notifications-container">
        <div class="notifications-card">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Inbox') }}
            </h2>
            @if ($notifications->isEmpty())
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Your inbox is empty.') }}
                </p>
            @else
                <div class="inbox-table" x-data="notifications()" x-init="init">
                    <table class="table-auto w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-600 dark:text-gray-400">
                                <th class="px-4 py-2">{{ __('Status') }}</th>
                                <th class="px-4 py-2">{{ __('Message') }}</th>
                                <th class="px-4 py-2">{{ __('Date') }}</th>
                                <th class="px-4 py-2">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notifications as $notification)
                                <tr class="notification-item cursor-pointer" 
                                    :class="{ 'unread': !notifications['{{ $notification->id }}']?.read_at, 'read': notifications['{{ $notification->id }}']?.read_at }" 
                                    @click="openModal('{{ $notification->id }}')">
                                    <td class="px-4 py-2">
                                        <i data-feather="{{ $notification->read_at ? 'mail' : 'mail' }}" 
                                           :class="{ 'text-gray-400': notifications['{{ $notification->id }}']?.read_at, 'text-accent': !notifications['{{ $notification->id }}']?.read_at }"></i>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="notification-message">
                                            {{ Str::limit($notification->data['message'] ?? $notification->id, 50) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <form x-show="!notifications['{{ $notification->id }}']?.read_at" 
                                              action="{{ route('notifications.read', $notification) }}" 
                                              method="POST" 
                                              class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="mark-read-btn" title="Mark as Read">
                                                <i data-feather="check-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Modal for Full Message -->
                    <template x-if="selectedNotification">
                        <div class="modal" x-show="showModal" x-transition>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Notification Details') }}
                                    </h3>
                                    <button @click="closeModal" class="modal-close">
                                        <i data-feather="x"></i>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p x-text="notifications[selectedNotification]?.data?.message || 'No message'"></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        <strong>{{ __('From') }}:</strong> 
                                        <span x-text="notifications[selectedNotification]?.data?.user_name || 'System'"></span>
                                        (<span x-text="notifications[selectedNotification]?.data?.email || 'N/A'"></span>)
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>{{ __('Date') }}:</strong> 
                                        <span x-text="notifications[selectedNotification]?.created_at"></span>
                                    </p>
                                    <a :href="'{{ url('admin/users') }}/' + notifications[selectedNotification]?.data?.user_id" 
                                       class="modal-link">
                                        {{ __('View User') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notifications', () => ({
                notifications: @json($notifications->mapWithKeys(fn($notification) => [$notification->id => $notification->toArray()])),
                showModal: false,
                selectedNotification: null,
                init() {
                    console.log('Initializing Pusher for user: {{ auth()->id() }}');
                    Pusher.logToConsole = true;
                    const pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                        wsHost: '{{ env('REVERB_HOST', 'localhost') }}',
                        wsPort: {{ env('REVERB_PORT', 8080) }},
                        wssPort: {{ env('REVERB_PORT', 8080) }},
                        forceTLS: {{ env('REVERB_SCHEME', 'http') === 'https' ? 'true' : 'false' }},
                        enabledTransports: ['ws', 'wss'],
                    });

                    const channel = pusher.subscribe('notifications.{{ auth()->id() }}');
                    channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (data) => {
                        console.log('Broadcast received:', data);
                        if (data.id && !this.notifications[data.id]) {
                            this.notifications[data.id] = {
                                id: data.id,
                                data: data.data || data,
                                created_at: data.created_at,
                                read_at: null,
                            };
                        } else if (data.id && data.read_at) {
                            this.notifications[data.id].read_at = data.read_at;
                        }
                    });
                },
                openModal(id) {
                    this.selectedNotification = id;
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.selectedNotification = null;
                }
            }));
        });
    </script>
@endsection