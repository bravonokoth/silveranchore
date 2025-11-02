@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section($isAdmin ? 'page-title' : 'header')
    {{ __('Inbox') }}
@endsection

@section('content')
<style>
    .message-item {
        transition: all 0.2s ease;
    }
    .message-item:hover {
        transform: translateX(4px);
    }
    .unread-indicator {
        width: 8px;
        height: 8px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .message-selected {
        background: linear-gradient(to right, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.05));
        border-left: 4px solid #3b82f6;
    }
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="flex h-[calc(100vh-4rem)] bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar Message List -->
    <div class="w-full lg:w-96 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10">
            <div class="flex items-center justify-between mb-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Inbox
                </h1>
                <button onclick="window.location.reload()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $notifications->count() }}</span> messages
                    @if($notifications->where('read_at', null)->count() > 0)
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
                            {{ $notifications->where('read_at', null)->count() }} new
                        </span>
                    @endif
                </span>
                
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-xs">
                            Mark all read
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Messages List -->
        <div class="flex-1 overflow-y-auto scrollbar-thin" x-data="inboxApp()" x-init="init()">
            @if ($notifications->isEmpty())
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No messages yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">When you receive notifications, they'll appear here</p>
                </div>
            @else
                @foreach ($notifications as $notification)
                    @php
                        $data = is_array($notification->data) ? $notification->data : [];
                        $message = $data['message'] ?? 'No message';
                        $orderId = $data['order_id'] ?? null;
                        $email = $data['email'] ?? null;
                        $status = $data['status'] ?? null;
                    @endphp

                    <div
                        @click="selectMessage('{{ $notification->id }}')"
                        class="message-item p-4 border-b border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        :class="{ 'message-selected': selectedId === '{{ $notification->id }}', 'bg-blue-50/50 dark:bg-blue-900/10': {{ $notification->read_at ? 'false' : 'true' }} && selectedId !== '{{ $notification->id }}' }"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Avatar/Icon -->
                            <div class="flex-shrink-0 mt-0.5">
                                @if($notification->read_at)
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate {{ $notification->read_at ? '' : 'font-bold' }}">
                                        Order Notification
                                        @if($orderId)
                                            <span class="text-blue-600 dark:text-blue-400">#{{ $orderId }}</span>
                                        @endif
                                    </h3>
                                    @if(!$notification->read_at)
                                        <span class="unread-indicator"></span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-2">
                                    {{ Str::limit($message, 80) }}
                                </p>

                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if($status)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($status === 'completed' || $status === 'delivered') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                            @elseif($status === 'pending' || $status === 'processing') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                            @elseif($status === 'shipped') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400
                                            @endif">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Message Content Area -->
    <div class="hidden lg:flex lg:flex-1 bg-white dark:bg-gray-800" x-data="inboxApp()" x-init="init()">
        <!-- Selected Message View -->
        <div x-show="selectedId" x-transition class="flex-1 flex flex-col">
            <!-- Message Header -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                Order Notification
                                <span x-text="messages[selectedId]?.order_id ? ' #' + messages[selectedId].order_id : ''" class="text-blue-600 dark:text-blue-400"></span>
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-text="formatDate(messages[selectedId]?.created_at)"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button
                            @click="markAsRead(selectedId)"
                            x-show="!messages[selectedId]?.read_at"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2 shadow-md"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as read
                        </button>
                        <button @click="selectedId = null" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Status Badge -->
                <div x-show="messages[selectedId]?.status">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium"
                         :class="{
                             'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': messages[selectedId]?.status === 'completed' || messages[selectedId]?.status === 'delivered',
                             'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': messages[selectedId]?.status === 'pending' || messages[selectedId]?.status === 'processing',
                             'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': messages[selectedId]?.status === 'shipped',
                             'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': messages[selectedId]?.status === 'cancelled'
                         }">
                        <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"></circle>
                        </svg>
                        <span x-text="messages[selectedId]?.status?.toUpperCase()"></span>
                    </span>
                </div>
            </div>

            <!-- Message Body -->
            <div class="flex-1 overflow-y-auto scrollbar-thin p-6">
                <div class="max-w-3xl">
                    <!-- Main Message -->
                    <div class="mb-8">
                        <p class="text-lg text-gray-900 dark:text-white leading-relaxed" x-text="messages[selectedId]?.message"></p>
                    </div>

                    <!-- Details Card -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Order Details
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white break-all" x-text="messages[selectedId]?.email || '—'"></p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Order ID</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="messages[selectedId]?.order_id ? '#' + messages[selectedId].order_id : '—'"></p>
                                </div>
                            </div>

                            <div x-show="messages[selectedId]?.session_id" class="md:col-span-2 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Session ID</p>
                                    <code class="text-xs font-mono bg-gray-200 dark:bg-gray-800 px-3 py-1.5 rounded text-gray-900 dark:text-white break-all inline-block" x-text="messages[selectedId]?.session_id"></code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div x-show="messages[selectedId]?.url" class="text-center">
                        <a
                            :href="messages[selectedId]?.url"
                            target="_blank"
                            class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Order Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!selectedId" class="flex-1 flex items-center justify-center p-12">
            <div class="text-center max-w-md">
                <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Select a message</h3>
                <p class="text-gray-500 dark:text-gray-400">Choose a message from your inbox to view its contents</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Load Pusher first -->
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>

<!-- Prepare data for Alpine -->
<script>
@php
$notificationsData = $notifications->mapWithKeys(function($n) {
    $data = is_array($n->data) ? $n->data : [];
    return [$n->id => [
        'id' => $n->id,
        'message' => $data['message'] ?? 'No message',
        'session_id' => $data['session_id'] ?? null,
        'email' => $data['email'] ?? null,
        'order_id' => $data['order_id'] ?? null,
        'status' => $data['status'] ?? null,
        'url' => $data['url'] ?? null,
        'created_at' => $n->created_at->toDateTimeString(),
        'read_at' => $n->read_at,
    ]];
})->toArray();
@endphp

// Define Alpine component before Alpine loads
window.inboxData = @json($notificationsData);
window.inboxAppComponent = function() {
    return {
        messages: window.inboxData,
        selectedId: null,

        init() {
            console.log('Inbox app initialized');
            
            // Initialize Pusher
            try {
                const pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                    wsHost: '{{ env('REVERB_HOST') }}',
                    wsPort: {{ env('REVERB_PORT') ?? 8080 }},
                    forceTLS: {{ env('REVERB_SCHEME') === 'https' ? 'true' : 'false' }},
                    enabledTransports: ['ws', 'wss'],
                });

                const channel = pusher.subscribe(`private-notifications.{{ auth()->id() ?? 'guest' }}`);
                channel.bind('notification.created', (data) => {
                    if (data.notification && !this.messages[data.notification.id]) {
                        this.messages[data.notification.id] = data.notification;
                        console.log('New notification received:', data.notification);
                    }
                });
            } catch (e) {
                console.error('Pusher initialization failed:', e);
            }
        },

        selectMessage(id) {
            this.selectedId = id;
            if (!this.messages[id].read_at) {
                this.markAsRead(id);
            }
        },

        async markAsRead(id) {
            if (this.messages[id]?.read_at) return;

            try {
                const response = await fetch(`{{ url('/notifications') }}/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    this.messages[id].read_at = new Date().toISOString();
                }
            } catch (e) {
                console.error('Failed to mark as read', e);
            }
        },

        formatDate(date) {
            if (!date) return '—';
            const d = new Date(date);
            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
};
</script>

<!-- Load Alpine.js last -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Register Alpine component after Alpine loads -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('inboxApp', window.inboxAppComponent);
});
</script>
@endpush