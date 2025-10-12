<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

public function search(Request $request)
{
    $query = $request->input('query');

    $users = User::with('roles')
        ->where('name', 'like', "%{$query}%")
        ->orWhere('email', 'like', "%{$query}%")
        ->orWhereHas('roles', function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->paginate(20)
        ->appends(['query' => $query]);

    return view('admin.users.index', compact('users'));
}


    // Add edit, update, destroy methods as needed
}