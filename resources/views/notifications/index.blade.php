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
                            <th class="px-4 py-2">{{ __('Session ID') }}</th>
                            <th class="px-4 py-2">{{ __('Date') }}</th>
                            <th class="px-4 py-2">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr class="notification-item cursor-pointer" 
                                :class="{ 'unread': !notifications['{{ $notification->id }}']?.read_at }" 
                                @click="openModal('{{ $notification->id }}')">
                                <td class="px-4 py-2">
                                    <i data-feather="{{ $notification->read_at ? 'mail' : 'mail-open' }}"></i>
                                </td>
                                <td class="px-4 py-2">
                                    {{ Str::limit($notification->message, 50) }}
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-500">
                                    {{ $notification->session_id ? Str::limit($notification->session_id, 12) : '—' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-2">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-blue-600">Mark Read</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Modal -->
                <template x-if="selectedNotification">
                    <div class="modal" x-show="showModal" x-transition>
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3>Notification</h3>
                                <button @click="closeModal">×</button>
                            </div>
                            <div class="modal-body">
                                <p x-text="notifications[selectedNotification]?.message"></p>
                                <p><strong>Session ID:</strong> <code x-text="notifications[selectedNotification]?.session_id || 'N/A'"></code></p>
                                <p><strong>Email:</strong> <span x-text="notifications[selectedNotification]?.email || 'N/A'"></span></p>
                                <p><strong>Order ID:</strong> <span x-text="notifications[selectedNotification]?.order_id || 'N/A'"></span></p>
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
        notifications: @json($notifications->mapWithKeys(fn($n) => [$n->id => $n->toArray()])),
        showModal: false,
        selectedNotification: null,
        init() {
            const pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                wsHost: '{{ env('REVERB_HOST') }}',
                wsPort: {{ env('REVERB_PORT') }},
                forceTLS: {{ env('REVERB_SCHEME') === 'https' ? 'true' : 'false' }},
                enabledTransports: ['ws', 'wss'],
            });

            const channel = pusher.subscribe('notifications.{{ auth()->id() ?? "guest" }}');
            channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (data) => {
                if (data.id && !this.notifications[data.id]) {
                    this.notifications[data.id] = data;
                }
            });
        },
        openModal(id) { this.selectedNotification = id; this.showModal = true; },
        closeModal() { this.showModal = false; this.selectedNotification = null; }
    }));
});
</script>
@endsection