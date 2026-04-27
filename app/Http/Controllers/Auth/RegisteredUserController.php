<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role'         => ['required', 'in:patient,admin'],
            'first_name'   => ['required', 'string', 'max:255'],
            'middle_name'  => ['nullable', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'gender'       => ['required', 'in:male,female,other'],
            'age'          => ['required', 'integer', 'min:1', 'max:120'],
            'contact_number' => ['required', 'string', 'max:20'],
            'street'       => ['nullable', 'string', 'max:255'],
            'barangay'     => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:255'],
            'province'     => ['nullable', 'string', 'max:255'],
            'zip_code'     => ['nullable', 'string', 'max:10'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the User account
        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Profile data shared between Admin and Patient
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

        // Create the corresponding profile record
        match ($request->role) {
            'admin'   => Admin::create($profileData),
            'patient' => Patient::create($profileData),
        };

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}