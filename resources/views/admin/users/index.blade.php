@extends('layouts.admin')

@section('page-title', 'Users')

@section('content')
    @include('partials.index-table-template', [
        'title'          => 'Users',
        'createRoute'    => route('admin.users.create'),
        'createLabel'    => 'Add User',
        'searchRoute'    => route('admin.users.search'),
        'searchPlaceholder' => 'Search by name, email, or role...',

        'items'          => $users,

        'columns' => [
            ['label' => 'ID',           'key' => 'id',               'type' => 'text'],
            ['label' => 'Name',         'key' => 'name',             'type' => 'text', 'truncate' => true, 'maxLength' => 50],
            ['label' => 'Email',        'key' => 'email',            'type' => 'text', 'truncate' => true, 'maxLength' => 60],
            [
                'label' => 'Roles',
                'type'  => 'custom',
                'render' => fn($user) => view('admin.users.partials.roles', compact('user'))->render()
            ],
            ['label' => 'Created',      'key' => 'created_at',       'type' => 'date'],
        ],

        'actions' => [
            [
                'type'   => 'link',
                'label'  => 'Edit',
                'icon'   => 'edit',
                'class'  => 'text-blue-600 hover:text-blue-800',
                'route'  => fn($user) => route('admin.users.edit', $user)
            ],
            [
                'type'   => 'form',
                'label'  => 'Delete',
                'icon'   => 'trash-2',
                'class'  => 'text-red-600 hover:text-red-800',
                'method' => 'DELETE',
                'route'  => fn($user) => route('admin.users.destroy', $user)
            ]
        ],

        'pagination' => $users
    ])
@endsection

@push('scripts')
<script>
    feather.replace();
</script>
@endpush