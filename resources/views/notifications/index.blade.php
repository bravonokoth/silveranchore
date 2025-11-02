@extends('layouts.admin')

@section('page-title', 'Inbox')

@section('content')
<style>
    .inbox-wrapper {
        display: flex;
        height: calc(100vh - 150px);
        background: var(--bg-card);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }
    
    .inbox-sidebar {
        width: 380px;
        min-width: 380px;
        border-right: 1px solid var(--border-color);
        background: var(--bg-card);
        display: flex;
        flex-direction: column;
    }
    
    .inbox-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .inbox-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .inbox-title h2 {
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        color: var(--text-primary);
    }
    
    .refresh-btn {
        padding: 0.5rem;
        background: transparent;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        color: var(--text-secondary);
        transition: all 0.2s;
    }
    
    .refresh-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
    }
    
    .inbox-stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    .new-badge {
        margin-left: 0.5rem;
        padding: 0.125rem 0.5rem;
        background: var(--accent-color);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .mark-all-btn {
        color: var(--accent-color);
        background: none;
        border: none;
        font-weight: 500;
        font-size: 0.75rem;
        cursor: pointer;
        padding: 0;
    }
    
    .mark-all-btn:hover {
        text-decoration: underline;
    }
    
    .messages-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .messages-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .messages-list::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }
    
    .message-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .message-item:hover {
        background: var(--hover-bg);
        transform: translateX(4px);
    }
    
    .message-item.unread {
        background: rgba(59, 130, 246, 0.05);
    }
    
    .message-item.selected {
        background: var(--accent-color);
        color: white;
        border-left: 4px solid var(--accent-color);
    }
    
    .message-item.selected * {
        color: white !important;
    }
    
    .message-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .message-icon.read {
        background: var(--hover-bg);
        color: var(--text-secondary);
    }
    
    .message-icon.unread {
        background: linear-gradient(135deg, #3b82f6, #9333ea);
        color: white;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
    }
    
    .unread-dot {
        width: 8px;
        height: 8px;
        background: var(--accent-color);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .message-content-area {
        flex: 1;
        background: var(--bg-card);
        display: flex;
    }
    
    .message-detail {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .message-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card);
    }
    
    .message-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }
    
    .message-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .message-body::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }
    
    .detail-card {
        background: var(--bg-surface);
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .btn-primary {
        padding: 0.5rem 1rem;
        background: var(--accent-color);
        color: white;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    .btn-primary:hover {
        background: var(--active-bg);
    }
    
    .btn-close {
        padding: 0.5rem;
        background: transparent;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        color: var(--text-secondary);
        transition: all 0.2s;
    }
    
    .btn-close:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
    }
    
    .empty-state {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        text-align: center;
    }
    
    .empty-icon {
        width: 8rem;
        height: 8rem;
        margin: 0 auto 1.5rem;
        background: var(--hover-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--accent-color), var(--secondary));
        color: white;
        font-weight: 600;
        border-radius: 0.75rem;
        box-shadow: var(--shadow-md);
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        color: white;
    }
    
    @media (max-width: 1024px) {
        .inbox-sidebar {
            width: 100%;
            min-width: 100%;
        }
        .message-content-area {
            display: none;
        }
    }
</style>

<div class="inbox-wrapper">
    <!-- Sidebar -->
    <div class="inbox-sidebar">
        <div class="inbox-header">
            <div class="inbox-title">
                <h2>
                    <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Inbox
                </h2>
                <button onclick="window.location.reload()" class="refresh-btn">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
            
            <div class="inbox-stats">
                <span>
                    <strong>{{ $notifications->count() }}</strong> messages
                    @if($notifications->where('read_at', null)->count() > 0)
                        <span class="new-badge">{{ $notifications->where('read_at', null)->count() }} new</span>
                    @endif
                </span>
                
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" style="display:inline;margin:0;">
                        @csrf @method('PATCH')
                        <button type="submit" class="mark-all-btn">Mark all read</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="messages-list">
            @forelse ($notifications as $notification)
                @php
                    $data = is_array($notification->data) ? $notification->data : [];
                    $message = $data['message'] ?? 'No message';
                    $orderId = $data['order_id'] ?? null;
                    $status = $data['status'] ?? null;
                @endphp

                <div data-id="{{ $notification->id }}" class="message-item {{ !$notification->read_at ? 'unread' : '' }}" onclick="selectMessage('{{ $notification->id }}')">
                    <div style="display:flex;gap:0.75rem;align-items:start;">
                        <div class="message-icon {{ $notification->read_at ? 'read' : 'unread' }}">
                            <svg style="width:1.25rem;height:1.25rem;" fill="{{ $notification->read_at ? 'none' : 'currentColor' }}" stroke="{{ $notification->read_at ? 'currentColor' : 'none' }}" viewBox="0 0 24 24">
                                @if($notification->read_at)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                                @else
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                @endif
                            </svg>
                        </div>

                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;margin-bottom:0.25rem;">
                                <h3 style="font-size:0.875rem;font-weight:{{ $notification->read_at ? '600' : '700' }};margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    Order Notification @if($orderId)<span style="color:var(--accent-color);">#{{ $orderId }}</span>@endif
                                </h3>
                                @if(!$notification->read_at)<span class="unread-dot"></span>@endif
                            </div>
                            
                            <p style="font-size:0.875rem;color:var(--text-secondary);margin:0 0 0.5rem 0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                {{ Str::limit($message, 80) }}
                            </p>

                            <div style="display:flex;align-items:center;gap:0.75rem;font-size:0.75rem;color:var(--text-secondary);">
                                <span style="display:flex;align-items:center;gap:0.25rem;">
                                    <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                @if($status)
                                    <span class="badge {{ $status }}">{{ ucfirst($status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:3rem;text-align:center;">
                    <div style="width:5rem;height:5rem;background:var(--hover-bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:var(--text-secondary);">
                        <svg style="width:2.5rem;height:2.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <h3 style="font-size:1.125rem;font-weight:600;margin-bottom:0.25rem;color:var(--text-primary);">No messages yet</h3>
                    <p style="font-size:0.875rem;color:var(--text-secondary);margin:0;">Notifications will appear here</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Content Area -->
    <div class="message-content-area">
        <div id="messageDetail" class="message-detail" style="display:none;">
            <div class="message-header">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:1rem;">
                    <div style="display:flex;gap:1rem;">
                        <div style="width:3rem;height:3rem;background:linear-gradient(135deg,#3b82f6,#9333ea);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-md);flex-shrink:0;">
                            <svg style="width:1.5rem;height:1.5rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 style="font-size:1.5rem;font-weight:bold;margin:0 0 0.25rem 0;color:var(--text-primary);">
                                Order Notification <span id="orderId" style="color:var(--accent-color);"></span>
                            </h2>
                            <p id="timestamp" style="font-size:0.875rem;color:var(--text-secondary);margin:0;"></p>
                        </div>
                    </div>
                    
                    <div style="display:flex;gap:0.5rem;">
                        <button id="markReadBtn" onclick="markAsRead()" class="btn-primary" style="display:none;">
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as read
                        </button>
                        <button onclick="closeMessage()" class="btn-close">
                            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="statusContainer" style="display:none;">
                    <span id="statusBadge" class="status-badge"></span>
                </div>
            </div>

            <div class="message-body">
                <div style="max-width:48rem;">
                    <div style="margin-bottom:2rem;">
                        <p id="messageText" style="font-size:1.125rem;line-height:1.75;color:var(--text-primary);margin:0;"></p>
                    </div>

                    <div class="detail-card">
                        <h3 style="font-size:0.875rem;font-weight:600;margin:0 0 1rem 0;color:var(--text-primary);">Order Details</h3>
                        
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
                            <div>
                                <p style="font-size:0.75rem;color:var(--text-secondary);margin:0 0 0.25rem 0;">Email</p>
                                <p id="detailEmail" style="font-size:0.875rem;font-weight:500;word-break:break-all;margin:0;color:var(--text-primary);"></p>
                            </div>

                            <div>
                                <p style="font-size:0.75rem;color:var(--text-secondary);margin:0 0 0.25rem 0;">Order ID</p>
                                <p id="detailOrderId" style="font-size:0.875rem;font-weight:500;margin:0;color:var(--text-primary);"></p>
                            </div>

                            <div id="sessionContainer" style="display:none;grid-column:1/-1;">
                                <p style="font-size:0.75rem;color:var(--text-secondary);margin:0 0 0.25rem 0;">Session ID</p>
                                <code id="detailSession" style="font-size:0.75rem;font-family:monospace;background:var(--hover-bg);padding:0.375rem 0.75rem;border-radius:0.25rem;word-break:break-all;display:inline-block;color:var(--text-primary);"></code>
                            </div>
                        </div>
                    </div>

                    <div id="urlContainer" style="display:none;text-align:center;">
                        <a id="urlLink" href="#" target="_blank" class="action-btn">
                            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Order Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="emptyState" class="empty-state">
            <div>
                <div class="empty-icon">
                    <svg style="width:4rem;height:4rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                    </svg>
                </div>
                <h3 style="font-size:1.25rem;font-weight:600;margin-bottom:0.5rem;color:var(--text-primary);">Select a message</h3>
                <p style="color:var(--text-secondary);margin:0;">Choose a message from your inbox to view details</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
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

const messages = @json($notificationsData);
let currentId = null;

function selectMessage(id) {
    currentId = id;
    const msg = messages[id];
    if (!msg) return;
    
    document.querySelectorAll('.message-item').forEach(el => el.classList.remove('selected'));
    document.querySelector(`[data-id="${id}"]`).classList.add('selected');
    
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('messageDetail').style.display = 'flex';
    
    document.getElementById('orderId').textContent = msg.order_id ? '#' + msg.order_id : '';
    document.getElementById('timestamp').textContent = new Date(msg.created_at).toLocaleString();
    document.getElementById('messageText').textContent = msg.message;
    document.getElementById('detailEmail').textContent = msg.email || '—';
    document.getElementById('detailOrderId').textContent = msg.order_id ? '#' + msg.order_id : '—';
    
    document.getElementById('markReadBtn').style.display = msg.read_at ? 'none' : 'flex';
    
    if (msg.status) {
        document.getElementById('statusContainer').style.display = 'block';
        document.getElementById('statusBadge').className = 'status-badge badge ' + msg.status;
        document.getElementById('statusBadge').textContent = msg.status.toUpperCase();
    } else {
        document.getElementById('statusContainer').style.display = 'none';
    }
    
    if (msg.session_id) {
        document.getElementById('sessionContainer').style.display = 'block';
        document.getElementById('detailSession').textContent = msg.session_id;
    } else {
        document.getElementById('sessionContainer').style.display = 'none';
    }
    
    if (msg.url) {
        document.getElementById('urlContainer').style.display = 'block';
        document.getElementById('urlLink').href = msg.url;
    } else {
        document.getElementById('urlContainer').style.display = 'none';
    }
    
    if (!msg.read_at) markAsRead();
}

function closeMessage() {
    currentId = null;
    document.getElementById('messageDetail').style.display = 'none';
    document.getElementById('emptyState').style.display = 'flex';
    document.querySelectorAll('.message-item').forEach(el => el.classList.remove('selected'));
}

async function markAsRead() {
    if (!currentId || !messages[currentId] || messages[currentId].read_at) return;
    
    try {
        const res = await fetch(`{{ url('/notifications') }}/${currentId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        if (res.ok) {
            messages[currentId].read_at = new Date().toISOString();
            document.getElementById('markReadBtn').style.display = 'none';
            const item = document.querySelector(`[data-id="${currentId}"]`);
            if (item) {
                item.classList.remove('unread');
                const dot = item.querySelector('.unread-dot');
                if (dot) dot.remove();
            }
        }
    } catch (e) {
        console.error('Failed:', e);
    }
}
</script>
@endpush