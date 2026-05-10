<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            'first_name'     => ['required', 'string', 'max:255'],
            'middle_name'    => ['nullable', 'string', 'max:255'],
            'last_name'      => ['required', 'string', 'max:255'],
            'gender'         => ['required', 'in:male,female,other'],
            'contact_number' => ['required', 'string', 'max:20'],
            'birthdate'      => ['nullable', 'date', 'before:today'],
            'blood_type'     => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'height'         => ['nullable', 'numeric', 'min:1', 'max:300'],
            'weight'         => ['nullable', 'numeric', 'min:1', 'max:700'],
            'street'         => ['nullable', 'string', 'max:255'],
            'barangay'       => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:255'],
            'province'       => ['nullable', 'string', 'max:255'],
            'zip_code'       => ['nullable', 'string', 'max:10'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the User account (role is always 'patient')
        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'patient',
        ]);

        // Create contact and address for the user (centralized)
        $user->contact()->create([
            'contact_number' => $request->contact_number,
        ]);

        $user->address()->create([
            'street'   => $request->street,
            'barangay' => $request->barangay,
            'city'     => $request->city,
            'province' => $request->province,
            'zip_code' => $request->zip_code,
        ]);

        // Create patient profile (without contact/address fields)
        $user->patient()->create([
            'user_id'    => $user->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'  => $request->last_name,
            'gender'     => $request->gender,
            'birthdate'  => $request->birthdate,
            'height'     => $request->height,
            'weight'     => $request->weight,
            'blood_type' => $request->blood_type,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}