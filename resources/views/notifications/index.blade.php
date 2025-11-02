@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section($isAdmin ? 'page-title' : 'header')
    {{ __('Inbox') }}
@endsection

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-bell"></i> {{ __('Inbox') }}
                </h2>
                <span class="text-sm text-gray-500">
                    {{ $notifications->count() }} {{ Str::plural('notification', $notifications->count()) }}
                </span>
            </div>
        </div>

        @if ($notifications->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-lg text-gray-500 dark:text-gray-400">{{ __('Your inbox is empty.') }}</p>
            </div>
        @else
            <div x-data="notificationsApp()" x-init="init()" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($notifications as $notification)
                    @php
                        $data = is_array($notification->data) ? $notification->data : [];
                        $message = $data['message'] ?? 'No message';
                        $sessionId = $data['session_id'] ?? null;
                        $orderId = $data['order_id'] ?? null;
                        $email = $data['email'] ?? null;
                        $url = $data['url'] ?? null;
                    @endphp

                    <div
                        class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer flex items-center gap-4"
                        @click="openModal('{{ $notification->id }}')"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20 font-medium': !$notification->read_at }"
                    >
                        <!-- Status Icon -->
                        <div class="flex-shrink-0">
                            <i :data-feather="$notification->read_at ? 'mail' : 'mail-open'" class="w-5 h-5 text-gray-500"></i>
                        </div>

                        <!-- Message -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-gray-100 truncate">
                                {{ Str::limit($message, 80) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                                @if($sessionId)
                                    • Session: <code class="font-mono">{{ Str::limit($sessionId, 10) }}</code>
                                @endif
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2" @click.stop>
                            @if(!$notification->read_at)
                                <form
                                    action="{{ route('notifications.markAsRead', $notification) }}"
                                    method="POST"
                                    class="inline"
                                    @submit.prevent="markAsRead('{{ $notification->id }}', $event.target)"
                                >
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        {{ __('Mark Read') }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-green-600 font-medium">{{ __('Read') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal (Teleported to body) -->
    <template x-teleport="body">
        <div
            x-show="modalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="closeModal()"
        >
            <!-- Backdrop -->
            <div
                x-show="modalOpen"
                class="fixed inset-0 bg-black bg-opacity-50"
                @click="closeModal()"
            ></div>

            <!-- Modal Panel -->
            <div
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full p-6"
                @click.stop
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Notification Details') }}
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div x-show="selected" class="space-y-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white" x-text="notifications[selected]?.message"></p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 text-gray-600 dark:text-gray-300 text-xs">
                        <div>
                            <strong>{{ __('Session ID') }}:</strong>
                            <code x-text="notifications[selected]?.session_id || '—'" class="ml-1 font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded"></code>
                        </div>
                        <div>
                            <strong>{{ __('Email') }}:</strong>
                            <span x-text="notifications[selected]?.email || '—'"></span>
                        </div>
                        <div>
                            <strong>{{ __('Order') }}:</strong>
                            <a
                                :href="notifications[selected]?.url"
                                x-text="notifications[selected]?.order_id ? '#'+notifications[selected].order_id : '—'"
                                class="text-indigo-600 hover:underline ml-1"
                                target="_blank"
                            ></a>
                        </div>
                        <div>
                            <strong>{{ __('Received') }}:</strong>
                            <span x-text="formatDate(notifications[selected]?.created_at)"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="closeModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 text-sm font-medium"
                    >
                        {{ __('Close') }}
                    </button>
                    <button
                        @click="markAsRead(selected)"
                        x-show="!notifications[selected]?.read_at"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium"
                    >
                        {{ __('Mark as Read') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@section('scripts')
{{-- CSRF Token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Alpine.js v3 + Feather Icons + Pusher --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationsApp', () => ({
        notifications: @json(
            $notifications->mapWithKeys(function($n) {
                $data = is_array($n->data) ? $n->data : [];
                return [$n->id => [
                    'id' => $n->id,
                    'message' => $data['message'] ?? 'No message',
                    'session_id' => $data['session_id'] ?? null,
                    'email' => $data['email'] ?? null,
                    'order_id' => $data['order_id'] ?? null,
                    'url' => $data['url'] ?? null,
                    'created_at' => $n->created_at->toDateTimeString(),
                    'read_at' => $n->read_at,
                ]];
            })->toArray()
        ),
        modalOpen: false,
        selected: null,

        init() {
            // Replace icons
            if (window.feather) feather.replace();

            // Reverb / Pusher
            const pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                wsHost: '{{ env('REVERB_HOST') }}',
                wsPort: {{ env('REVERB_PORT') ?? 8080 }},
                forceTLS: {{ env('REVERB_SCHEME') === 'https' ? 'true' : 'false' }},
                enabledTransports: ['ws', 'wss'],
            });

            const channel = pusher.subscribe(`private-notifications.{{ auth()->id() ?? 'guest' }}`);
            channel.bind('notification.created', (data) => {
                if (data.notification && !this.notifications[data.notification.id]) {
                    this.$nextTick(() => {
                        this.notifications[data.notification.id] = data.notification;
                        feather.replace();
                    });
                }
            });
        },

        openModal(id) {
            this.selected = id;
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
            this.selected = null;
        },

        async markAsRead(id, form = null) {
            if (form) {
                form.submit();
                return;
            }

            try {
                await fetch(`{{ url('/notifications') }}/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                this.notifications[id].read_at = new Date().toISOString();
                feather.replace();
            } catch (e) {
                console.error('Failed to mark as read', e);
            }
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleString() : '—';
        }
    }));
});
</script>
@endsection