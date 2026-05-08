<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">User Details</h5>
                <small class="text-muted">Viewing account information</small>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </x-slot>

    <div class="row g-4">

        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
                <div class="card-body p-4">

                    {{-- Avatar --}}
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 1.5rem;
                         background: {{ $user->role === 'admin' ? '#0d6efd' : ($user->role === 'doctor' ? '#0dcaf0' : '#198754') }};">
                        {{ strtoupper(substr($user->email, 0, 2)) }}
                    </div>

                    {{-- Name --}}
                    @php
                    $profile = $user->admin ?? $user->patient ?? $user->doctor ?? null;
                    @endphp

                    @if($profile)
                    <h6 class="fw-bold mb-1">
                        {{ $profile->first_name }}
                        {{ $profile->middle_name ? $profile->middle_name . ' ' : '' }}
                        {{ $profile->last_name }}
                    </h6>
                    @else
                    <h6 class="fw-bold mb-1 text-muted fst-italic">No profile yet</h6>
                    @endif

                    <small class="text-muted d-block mb-3">{{ $user->email }}</small>

                    {{-- Role Badge --}}
                    @if($user->role === 'admin')
                    <span class="badge rounded-pill px-3 py-2" style="background: #e7f1ff; color: #0d6efd;">
                        <i class="bi bi-shield-fill me-1"></i> Admin
                    </span>
                    @elseif($user->role === 'doctor')
                    <span class="badge rounded-pill px-3 py-2" style="background: #e0f7fc; color: #0097a7;">
                        <i class="bi bi-person-badge-fill me-1"></i> Doctor
                    </span>
                    @else
                    <span class="badge rounded-pill px-3 py-2" style="background: #e8f5ee; color: #198754;">
                        <i class="bi bi-person-heart-fill me-1"></i> Patient
                    </span>
                    @endif

                    <hr class="my-4">

                    <div class="text-start">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-calendar3 text-muted" style="font-size: 0.85rem;"></i>
                            <small class="text-muted">Joined {{ $user->created_at->format('d M Y') }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i>
                            <small class="text-muted">Account Active</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-pencil me-1"></i> Edit User
                        </a>
                        @if(Auth::id() !== $user->id)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 w-100">
                                <i class="bi bi-trash me-1"></i> Delete User
                            </button>
                        </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- Profile Details --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Profile Information</h6>
                    <small class="text-muted">Personal details and address</small>
                </div>
                <div class="card-body px-4 pb-4 pt-3">

                    @if($profile)

                    <p class="text-muted small fw-semibold mb-3 text-uppercase" style="letter-spacing: 0.05em;">
                        Personal Details
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">First Name</small>
                                <span class="fw-medium small">{{ $profile->first_name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Middle Name</small>
                                <span class="fw-medium small">{{ $profile->middle_name ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Last Name</small>
                                <span class="fw-medium small">{{ $profile->last_name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Gender</small>
                                <span class="fw-medium small">{{ ucfirst($profile->gender ?? '—') }}</span>
                            </div>
                        </div>
                        @if($user->role === 'patient' && $profile->age)
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Age</small>
                                <span class="fw-medium small">{{ $profile->age ?? '—' }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Contact Number</small>
                                <span class="fw-medium small">{{ $user->contact->contact_number ?? '—' }}</span>
                            </div>
                        </div>
                        @if($user->role === 'doctor')
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Specialty</small>
                                <span class="fw-medium small">{{ $profile->specialty ?? '—' }}</span>
                            </div>
                        </div>
                        @endif
                        @if($user->role === 'patient')
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Birthdate</small>
                                <span class="fw-medium small">{{ $profile->birthdate ? $profile->birthdate->format('d M
                                    Y') : '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Blood Type</small>
                                <span class="fw-medium small">{{ $profile->blood_type ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Height</small>
                                <span class="fw-medium small">{{ $profile->height ? $profile->height . ' cm' : '—'
                                    }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Weight</small>
                                <span class="fw-medium small">{{ $profile->weight ? $profile->weight . ' kg' : '—'
                                    }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <hr class="my-3">

                    <p class="text-muted small fw-semibold mb-3 text-uppercase" style="letter-spacing: 0.05em;">
                        Address
                    </p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Street</small>
                                <span class="fw-medium small">{{ $user->address->street ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Barangay</small>
                                <span class="fw-medium small">{{ $user->address->barangay ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">City</small>
                                <span class="fw-medium small">{{ $user->address->city ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Province</small>
                                <span class="fw-medium small">{{ $user->address->province ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Zip Code</small>
                                <span class="fw-medium small">{{ $user->address->zip_code ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-person-x fs-1 d-block mb-2 opacity-25"></i>
                        <div class="fw-medium">No profile information found</div>
                        <small>This user has not completed their profile yet.</small>
                    </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

</x-app-layout>