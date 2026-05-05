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
            'role'                   => ['required', 'in:admin,patient,doctor'],
            'first_name'             => ['required', 'string', 'max:255'],
            'middle_name'            => ['nullable', 'string', 'max:255'],
            'last_name'              => ['required', 'string', 'max:255'],
            'gender'                 => ['required', 'in:male,female,other'],
            'contact_number'         => ['required', 'string', 'max:20'],
            'specialty'              => ['nullable', 'required_if:role,doctor', 'string', 'max:255'],
            'birthdate'              => ['nullable', 'date', 'before:today'],
            'height'                 => ['nullable', 'numeric', 'min:1', 'max:300'],
            'weight'                 => ['nullable', 'numeric', 'min:1', 'max:700'],
            'blood_type'             => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'street'                 => ['nullable', 'string', 'max:255'],
            'barangay'               => ['nullable', 'string', 'max:255'],
            'city'                   => ['nullable', 'string', 'max:255'],
            'province'               => ['nullable', 'string', 'max:255'],
            'zip_code'               => ['nullable', 'string', 'max:10'],
            'email'                  => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'               => ['required', 'confirmed', Rules\Password::defaults()],
            // Services (doctor only)
            'services'               => ['nullable', 'array'],
            'services.*.name'        => ['required_with:services.*', 'string', 'max:255'],
            'services.*.description' => ['nullable', 'string'],
            'services.*.price'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Admin core data (not normalized — stays flat)
        $adminProfileData = [
            'user_id'        => $user->id,
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'gender'         => $request->gender,
            'contact_number' => $request->contact_number,
            'street'         => $request->street,
            'barangay'       => $request->barangay,
            'city'           => $request->city,
            'province'       => $request->province,
            'zip_code'       => $request->zip_code,
        ];

        // Patient core data (normalized + medical fields, no age)
        $patientCoreData = [
            'user_id'     => $user->id,
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'gender'      => $request->gender,
            'birthdate'   => $request->birthdate,
            'height'      => $request->height,
            'weight'      => $request->weight,
            'blood_type'  => $request->blood_type,
        ];

        // Doctor core data (normalized, no age)
        $doctorCoreData = [
            'user_id'    => $user->id,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'gender'     => $request->gender,
            'specialty'  => $request->specialty,
        ];

        match ($request->role) {
            'admin'   => Admin::create($adminProfileData),

            'patient' => tap(Patient::create($patientCoreData), function (Patient $patient) use ($request) {
                            $patient->contact()->create([
                                'contact_number' => $request->contact_number,
                            ]);
                            $patient->address()->create([
                                'street'   => $request->street,
                                'barangay' => $request->barangay,
                                'city'     => $request->city,
                                'province' => $request->province,
                                'zip_code' => $request->zip_code,
                            ]);
                         }),

            'doctor'  => tap(Doctor::create($doctorCoreData), function (Doctor $doctor) use ($request) {
                            $doctor->contact()->create([
                                'contact_number' => $request->contact_number,
                            ]);
                            $doctor->address()->create([
                                'street'   => $request->street,
                                'barangay' => $request->barangay,
                                'city'     => $request->city,
                                'province' => $request->province,
                                'zip_code' => $request->zip_code,
                            ]);

                            // ── Save services ──
                            if ($request->filled('services')) {
                                foreach ($request->services as $svc) {
                                    if (!empty($svc['name'])) {
                                        $doctor->services()->create([
                                            'name'        => $svc['name'],
                                            'description' => $svc['description'] ?? null,
                                            'price'       => $svc['price'] ?? null,
                                        ]);
                                    }
                                }
                            }
                         }),
        };

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    public function index(Request $request)
    {
        $query = User::with(['admin', 'patient', 'doctor'])
                     ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'admin',
            'patient.contact', 'patient.address',
            'doctor.contact',  'doctor.address', 'doctor.services',
        ]);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load([
            'admin',
            'patient.contact', 'patient.address',
            'doctor.contact',  'doctor.address', 'doctor.services',
        ]);
        $profile = $user->admin ?? $user->patient ?? $user->doctor ?? null;
        return view('admin.users.edit', compact('user', 'profile'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role'                   => ['required', 'in:admin,patient,doctor'],
            'first_name'             => ['required', 'string', 'max:255'],
            'middle_name'            => ['nullable', 'string', 'max:255'],
            'last_name'              => ['required', 'string', 'max:255'],
            'gender'                 => ['required', 'in:male,female,other'],
            'contact_number'         => ['required', 'string', 'max:20'],
            'specialty'              => ['nullable', 'required_if:role,doctor', 'string', 'max:255'],
            'birthdate'              => ['nullable', 'date', 'before:today'],
            'height'                 => ['nullable', 'numeric', 'min:1', 'max:300'],
            'weight'                 => ['nullable', 'numeric', 'min:1', 'max:700'],
            'blood_type'             => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'street'                 => ['nullable', 'string', 'max:255'],
            'barangay'               => ['nullable', 'string', 'max:255'],
            'city'                   => ['nullable', 'string', 'max:255'],
            'province'               => ['nullable', 'string', 'max:255'],
            'zip_code'               => ['nullable', 'string', 'max:10'],
            'email'                  => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            // Services (doctor only)
            'services'               => ['nullable', 'array'],
            'services.*.name'        => ['required_with:services.*', 'string', 'max:255'],
            'services.*.description' => ['nullable', 'string'],
            'services.*.price'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $user->update([
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        $adminProfileData = [
            'first_name'     => $request->first_name,
            'middle_name'    => $request->middle_name,
            'last_name'      => $request->last_name,
            'gender'         => $request->gender,
            'contact_number' => $request->contact_number,
            'street'         => $request->street,
            'barangay'       => $request->barangay,
            'city'           => $request->city,
            'province'       => $request->province,
            'zip_code'       => $request->zip_code,
        ];

        $patientCoreData = [
            'first_name'  => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'   => $request->last_name,
            'gender'      => $request->gender,
            'birthdate'   => $request->birthdate,
            'height'      => $request->height,
            'weight'      => $request->weight,
            'blood_type'  => $request->blood_type,
        ];

        $doctorCoreData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'gender'     => $request->gender,
            'specialty'  => $request->specialty,
        ];

        match ($request->role) {
            'admin'   => $user->admin()->updateOrCreate(
                            ['user_id' => $user->id],
                            $adminProfileData
                         ),

            'patient' => tap(
                            $user->patient()->updateOrCreate(
                                ['user_id' => $user->id],
                                $patientCoreData
                            ),
                            function (Patient $patient) use ($request) {
                                $patient->contact()->updateOrCreate(
                                    ['patient_id' => $patient->id],
                                    ['contact_number' => $request->contact_number]
                                );
                                $patient->address()->updateOrCreate(
                                    ['patient_id' => $patient->id],
                                    [
                                        'street'   => $request->street,
                                        'barangay' => $request->barangay,
                                        'city'     => $request->city,
                                        'province' => $request->province,
                                        'zip_code' => $request->zip_code,
                                    ]
                                );
                            }
                         ),

            'doctor'  => tap(
                            $user->doctor()->updateOrCreate(
                                ['user_id' => $user->id],
                                $doctorCoreData
                            ),
                            function (Doctor $doctor) use ($request) {
                                $doctor->contact()->updateOrCreate(
                                    ['doctor_id' => $doctor->id],
                                    ['contact_number' => $request->contact_number]
                                );
                                $doctor->address()->updateOrCreate(
                                    ['doctor_id' => $doctor->id],
                                    [
                                        'street'   => $request->street,
                                        'barangay' => $request->barangay,
                                        'city'     => $request->city,
                                        'province' => $request->province,
                                        'zip_code' => $request->zip_code,
                                    ]
                                );

                                // ── Sync services ──
                                // Delete all existing services then re-insert from form.
                                // This is the simplest reliable sync for an admin-managed list.
                                $doctor->services()->delete();

                                if ($request->filled('services')) {
                                    foreach ($request->services as $svc) {
                                        if (!empty($svc['name'])) {
                                            $doctor->services()->create([
                                                'name'        => $svc['name'],
                                                'description' => $svc['description'] ?? null,
                                                'price'       => $svc['price'] ?? null,
                                            ]);
                                        }
                                    }
                                }
                            }
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