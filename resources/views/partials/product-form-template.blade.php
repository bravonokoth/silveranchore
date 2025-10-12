@props([
    'title' => 'Create New Item',
    'backRoute' => null,
    'backLabel' => 'Back',
    'formAction' => '',
    'method' => 'POST',
    'sections' => [],
    'submitLabel' => 'Create',
    'submitIcon' => 'check-circle',
    'values' => [], // New prop for pre-filled values (used in edit forms)
    'isEdit' => false // New prop to indicate edit form (for @method('PUT'))
])

<link href="{{ asset('css/product-form.css') }}" rel="stylesheet">

<div class="product-form-page">
    <!-- Header -->
    <div class="sticky-header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
                {{ $title }}
            </h2>
            @if ($backRoute)
                <a href="{{ $backRoute }}" class="action-btn">
                    {{ $backLabel }}
                </a>
            @endif
        </div>
    </div>

    <!-- Form -->
    <form method="{{ $method }}" action="{{ $formAction }}" enctype="multipart/form-data" class="product-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        @foreach ($sections as $section)
            <div class="form-section">
                <h3 class="section-title">
                    @if (isset($section['icon']))
                        <i data-lucide="{{ $section['icon'] }}" class="w-5 h-5 text-blue-600 mr-2"></i>
                    @endif
                    {{ $section['title'] }}
                </h3>
                <div class="form-grid">
                    @foreach ($section['fields'] as $field)
                        <div class="form-group {{ $field['fullWidth'] ?? false ? 'col-span-full' : '' }}">
                            @if ($field['type'] === 'checkbox-group')
                                <div class="flex items-center gap-6">
                                    @foreach ($field['checkboxes'] as $checkbox)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" name="{{ $checkbox['name'] }}" class="form-checkbox" {{ isset($values[$checkbox['name']]) && $values[$checkbox['name']] ? 'checked' : '' }}>
                                            <span class="text-sm font-medium text-gray-700">{{ $checkbox['label'] }}</span>
                                        </label>
                                        @error($checkbox['name'])
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    @endforeach
                                </div>
                            @else
                                <label for="{{ $field['name'] }}" class="form-label">{{ $field['label'] }}</label>
                                @if ($field['type'] === 'select')
                                    <select name="{{ $field['name'] }}" class="form-input" {{ $field['required'] ?? false ? 'required' : '' }}>
                                        @foreach ($field['options'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" {{ isset($values[$field['name']]) && $values[$field['name']] == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'textarea')
                                    <textarea name="{{ $field['name'] }}" rows="{{ $field['rows'] ?? 4 }}" class="form-input" placeholder="{{ $field['placeholder'] ?? '' }}">{{ $values[$field['name']] ?? '' }}</textarea>
                                @else
                                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" class="form-input" placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ isset($field['step']) ? "step=\"{$field['step']}\"" : '' }} {{ $field['type'] !== 'file' && isset($values[$field['name']]) ? "value=\"{$values[$field['name']]}\"" : '' }}>
                                @endif
                                @error($field['name'])
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Submit Button -->
        <div class="form-actions">
            <button type="submit" class="action-btn">
                <i data-lucide="{{ $submitIcon }}" class="w-5 h-5 mr-2"></i>
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>