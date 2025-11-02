{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Create User')

@section('content')
    @include('partials.form-template', [
        'title'        => 'Create New User',
        'backRoute'    => route('admin.users.index'),
        'backLabel'    => 'Back to Users',
        'formAction'   => route('admin.users.store'),
        'method'       => 'POST',
        'isEdit'       => false,
        'submitLabel'  => 'Create User',
        'submitIcon'   => 'plus-circle',

        'sections' => [
            [
                'title' => 'User Details',
                'icon'  => 'user-plus',
                'fields' => [
                    ['name' => 'name',  'label' => 'Name',  'type' => 'text',  'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'required' => true],
                    ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'required' => true],
                ]
            ],
            [
                'title' => 'Assign Roles',
                'icon'  => 'shield',
                'fields' => [
                    [
                        'type' => 'checkbox-group',
                        'checkboxes' => \Spatie\Permission\Models\Role::all()->map(function ($role) {
                            return [
                                'name'  => 'roles[]',
                                'value' => $role->name,
                                'label' => ucfirst($role->name),
                                'checked' => false
                            ];
                        })->toArray()
                    ]
                ]
            ]
        ],

        'values' => []
    ])
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush