@props([
    'title' => 'Items',
    'createRoute' => null,
    'createLabel' => 'Create Item',
    'items' => [],
    'columns' => [],
    'actions' => [],
    'searchRoute' => null,
    'searchPlaceholder' => 'Search...',
    'pagination' => null
])

<link href="{{ asset('css/index-table.css') }}" rel="stylesheet">

<div class="index-table-page">
    <!-- Header -->
    <div class="sticky-header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
                <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                {{ $title }}
            </h2>
            <div class="flex items-center gap-3">
                @if ($searchRoute)
                    <form action="{{ $searchRoute }}" method="GET" class="search-form">
                        <div class="search-container">
                            <i data-lucide="search" class="w-5 h-5 text-gray-500"></i>
                            <input type="text" name="search" placeholder="{{ $searchPlaceholder }}" class="search-input" value="{{ request('search') }}">
                            <button type="submit" class="search-btn">Search</button>
                        </div>
                    </form>
                @endif
                @if ($createRoute)
                    <a href="{{ $createRoute }}" class="action-btn create-btn">
                        <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
                        {{ $createLabel }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="index-table">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach
                    @if (!empty($actions))
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        @foreach ($columns as $column)
                            <td>
                                @if ($column['type'] === 'image')
                                    <img src="{{ asset('storage/' . $item->{$column['key']}) }}" alt="{{ $item->title ?? $item->name ?? 'Item' }}" class="table-image">
                                @elseif ($column['type'] === 'boolean')
                                    {{ $item->{$column['key']} ? 'Yes' : 'No' }}
                                @elseif ($column['type'] === 'relation')
                                    {{ $item->{$column['relation']}->{$column['relation_key']} ?? 'N/A' }}
                                @elseif ($column['type'] === 'currency')
                                    ${{ number_format($item->{$column['key']}, 2) }}
                                @elseif ($column['type'] === 'date')
                                    {{ $item->{$column['key']} ? \Carbon\Carbon::parse($item->{$column['key']})->format('Y-m-d') : 'N/A' }}
                                @else
                                    {{ $item->{$column['key']} ?? 'N/A' }}
                                @endif
                            </td>
                        @endforeach
                        @if (!empty($actions))
                            <td>
                                <div class="action-icons">
                                    @foreach ($actions as $action)
                                        @if ($action['type'] === 'link')
                                            <a href="{{ $action['route']($item) }}" class="action-icon {{ $action['class'] }}" title="{{ $action['label'] }}">
                                                <i data-lucide="{{ $action['icon'] }}" class="w-5 h-5"></i>
                                            </a>
                                        @elseif ($action['type'] === 'form')
                                            <form action="{{ $action['route']($item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method($action['method'])
                                                <button type="submit" class="action-icon {{ $action['class'] }}" title="{{ $action['label'] }}">
                                                    <i data-lucide="{{ $action['icon'] }}" class="w-5 h-5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (!empty($actions) ? 1 : 0) }}" class="empty-state">
                            <i data-lucide="alert-circle" class="w-8 h-8 text-gray-500"></i>
                            <p>No {{ strtolower($title) }} found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($pagination)
        <div class="pagination">
            {{ $pagination->links() }}
        </div>
    @endif
</div>