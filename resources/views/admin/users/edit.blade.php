{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Edit User')

@section('content')
    @include('partials.form-template', [
        'title'        => 'Edit User: ' . $user->name,
        'backRoute'    => route('admin.users.index'),
        'backLabel'    => 'Back to Users',
        'formAction'   => route('admin.users.update', $user),
        'method'       => 'POST',
        'isEdit'       => true,
        'submitLabel'  => 'Update User',
        'submitIcon'   => 'check-circle',

        'sections' => [
            [
                'title' => 'Basic Information',
                'icon'  => 'user',
                'fields' => [
                    [
                        'name'     => 'name',
                        'label'    => 'Name',
                        'type'     => 'text',
                        'required' => true,
                        'placeholder' => 'Enter full name'
                    ],
                    [
                        'name'     => 'email',
                        'label'    => 'Email',
                        'type'     => 'email',
                        'required' => true,
                        'placeholder' => 'user@example.com'
                    ],
                    [
                        'name'     => 'password',
                        'label'    => 'New Password',
                        'type'     => 'password',
                        'required' => false,
                        'placeholder' => 'Leave blank to keep current'
                    ],
                    [
                        'name'     => 'password_confirmation',
                        'label'    => 'Confirm Password',
                        'type'     => 'password',
                        'required' => false,
                        'placeholder' => 'Confirm new password'
                    ],
                ]
            ],
            [
                'title' => 'Roles & Permissions',
                'icon'  => 'shield',
                'fields' => [
                    [
                        'type' => 'checkbox-group',
                        'checkboxes' => $roles->map(function ($role) use ($user) {
                            return [
                                'name'  => 'roles[]',
                                'value' => $role->name,
                                'label' => ucfirst($role->name),
                                'checked' => $user->hasRole($role->name)
                            ];
                        })->toArray()
                    ]
                ]
            ]
        ],

        'values' => [
            'name'  => $user->name,
            'email' => $user->email,
        ]
    ])
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush