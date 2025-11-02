@foreach($user->roles as $role)
    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
        {{ $role->name === 'super-admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
        {{ ucfirst($role->name) }}
    </span>
@endforeach

@if($user->roles->isEmpty())
    <span class="text-gray-400 text-xs italic">No role</span>
@endif