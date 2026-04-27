<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{

    public function create()
{
    return view('admin.users.create');
}

public function store(Request $request)
{
    $request->validate([
        'role'           => ['required', 'in:admin,patient,doctor'],
        'first_name'     => ['required', 'string', 'max:255'],
        'middle_name'    => ['nullable', 'string', 'max:255'],
        'last_name'      => ['required', 'string', 'max:255'],
        'gender'         => ['required', 'in:male,female,other'],
        'age'            => ['required', 'integer', 'min:1', 'max:120'],
        'contact_number' => ['required', 'string', 'max:20'],
        'specialty'      => ['nullable', 'required_if:role,doctor', 'string', 'max:255'],
        'street'         => ['nullable', 'string', 'max:255'],
        'barangay'       => ['nullable', 'string', 'max:255'],
        'city'           => ['nullable', 'string', 'max:255'],
        'province'       => ['nullable', 'string', 'max:255'],
        'zip_code'       => ['nullable', 'string', 'max:10'],
        'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password'       => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => $request->role,
    ]);

    $profileData = [
        'user_id'        => $user->id,
        'first_name'     => $request->first_name,
        'middle_name'    => $request->middle_name,
        'last_name'      => $request->last_name,
        'gender'         => $request->gender,
        'age'            => $request->age,
        'contact_number' => $request->contact_number,
        'street'         => $request->street,
        'barangay'       => $request->barangay,
        'city'           => $request->city,
        'province'       => $request->province,
        'zip_code'       => $request->zip_code,
    ];

    match ($request->role) {
        'admin'   => Admin::create($profileData),
        'patient' => Patient::create($profileData),
        'doctor'  => Doctor::create(array_merge($profileData, [
                        'specialty' => $request->specialty,
                     ])),
    };

    return redirect()->route('admin.users.index')
                     ->with('success', 'User created successfully.');
}

    public function index(Request $request)
    {
        $query = User::with(['admin', 'patient', 'doctor'])
                     ->orderBy('created_at', 'desc');

        // Search by email
        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['admin', 'patient', 'doctor']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
{
    $user->load(['admin', 'patient', 'doctor']);
    $profile = $user->admin ?? $user->patient ?? $user->doctor ?? null;
    return view('admin.users.edit', compact('user', 'profile'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'role'           => ['required', 'in:admin,patient,doctor'],
        'first_name'     => ['required', 'string', 'max:255'],
        'middle_name'    => ['nullable', 'string', 'max:255'],
        'last_name'      => ['required', 'string', 'max:255'],
        'gender'         => ['required', 'in:male,female,other'],
        'age'            => ['required', 'integer', 'min:1', 'max:120'],
        'contact_number' => ['required', 'string', 'max:20'],
        'specialty'      => ['nullable', 'required_if:role,doctor', 'string', 'max:255'],
        'street'         => ['nullable', 'string', 'max:255'],
        'barangay'       => ['nullable', 'string', 'max:255'],
        'city'           => ['nullable', 'string', 'max:255'],
        'province'       => ['nullable', 'string', 'max:255'],
        'zip_code'       => ['nullable', 'string', 'max:10'],
        'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
    ]);

    // Update user account
    $user->update([
        'email' => $request->email,
        'role'  => $request->role,
    ]);

    $profileData = [
        'first_name'     => $request->first_name,
        'middle_name'    => $request->middle_name,
        'last_name'      => $request->last_name,
        'gender'         => $request->gender,
        'age'            => $request->age,
        'contact_number' => $request->contact_number,
        'street'         => $request->street,
        'barangay'       => $request->barangay,
        'city'           => $request->city,
        'province'       => $request->province,
        'zip_code'       => $request->zip_code,
    ];

    // Update or create the profile based on role
    match ($request->role) {
        'admin'   => $user->admin()->updateOrCreate(
                        ['user_id' => $user->id],
                        $profileData
                     ),
        'patient' => $user->patient()->updateOrCreate(
                        ['user_id' => $user->id],
                        $profileData
                     ),
        'doctor'  => $user->doctor()->updateOrCreate(
                        ['user_id' => $user->id],
                        array_merge($profileData, ['specialty' => $request->specialty])
                     ),
    };

    return redirect()->route('admin.users.index')
                     ->with('success', 'User updated successfully.');
}

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}