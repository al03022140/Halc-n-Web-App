<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Restringir gestión de usuarios a rol Admin
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || optional(auth()->user()->role)->name !== 'Admin') {
                return redirect()->route('dashboard')->with('error', 'Acceso solo para administradores.');
            }
            return $next($request);
        });
    }
    public function index()
    {
        $users = User::where('active', true)
            ->with(['role', 'department'])
            ->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $departments = Department::all();
        return view('users.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:6',
            'role_id'    => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'department_id' => $request->department_id,
            'active'        => true
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = Department::all();
        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role_id'    => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'active'     => 'required|boolean'
        ]);

        $data = $request->only(['name', 'email', 'role_id', 'department_id', 'active']);
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        $user->update(['active' => false]);
        return redirect()->route('users.index')->with('success', 'Usuario inactivado.');
    }
}
