<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Edit User</h5>
                <small class="text-muted">Update account and profile information</small>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">

                {{-- User Info Header --}}
                <div class="card-header border-0 pt-4 px-4 pb-3"
                     style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width: 50px; height: 50px; font-size: 1rem;
                             background: {{ $user->role === 'admin' ? '#0d6efd' : ($user->role === 'doctor' ? '#0dcaf0' : '#198754') }};">
                            {{ strtoupper(substr($user->email, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $user->email }}</div>
                            <small class="text-muted">Registered {{ $user->created_at->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 mb-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        {{-- Role --}}
                        <div class="mb-4">
                            <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role"
                                    class="form-select form-select-lg @error('role') is-invalid @enderror"
                                    required onchange="toggleSpecialty(this.value)">
                                <option value="admin"   {{ old('role', $user->role) === 'admin'   ? 'selected' : '' }}>Admin</option>
                                <option value="patient" {{ old('role', $user->role) === 'patient' ? 'selected' : '' }}>Patient</option>
                                <option value="doctor"  {{ old('role', $user->role) === 'doctor'  ? 'selected' : '' }}>Doctor</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Changing the role will affect system access.
                            </small>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Personal Information</p>

                        {{-- Name Row --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                       value="{{ old('first_name', $profile?->first_name) }}"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" name="middle_name"
                                       value="{{ old('middle_name', $profile?->middle_name) }}"
                                       class="form-control @error('middle_name') is-invalid @enderror">
                                @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name"
                                       value="{{ old('last_name', $profile?->last_name) }}"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Gender, Age, Contact --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender"
                                        class="form-select @error('gender') is-invalid @enderror"
                                        required>
                                    <option value="" disabled>Select...</option>
                                    <option value="male"   {{ old('gender', $profile?->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $profile?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender', $profile?->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age"
                                       value="{{ old('age', $profile?->age) }}"
                                       class="form-control @error('age') is-invalid @enderror"
                                       min="1" max="120" required>
                                @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_number"
                                       value="{{ old('contact_number', $profile?->contact_number) }}"
                                       class="form-control @error('contact_number') is-invalid @enderror"
                                       required>
                                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Specialty (Doctor only) --}}
                        <div class="row g-3 mb-3" id="specialtyField"
                             style="{{ old('role', $user->role) === 'doctor' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Specialty <span class="text-danger">*</span></label>
                                <input type="text" name="specialty"
                                       value="{{ old('specialty', $profile?->specialty ?? '') }}"
                                       class="form-control @error('specialty') is-invalid @enderror"
                                       placeholder="e.g. Cardiology, Pediatrics...">
                                @error('specialty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Address</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Street</label>
                            <input type="text" name="street"
                                   value="{{ old('street', $profile?->street) }}"
                                   class="form-control @error('street') is-invalid @enderror">
                            @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" name="barangay"
                                       value="{{ old('barangay', $profile?->barangay) }}"
                                       class="form-control @error('barangay') is-invalid @enderror">
                                @error('barangay') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city"
                                       value="{{ old('city', $profile?->city) }}"
                                       class="form-control @error('city') is-invalid @enderror">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Province</label>
                                <input type="text" name="province"
                                       value="{{ old('province', $profile?->province) }}"
                                       class="form-control @error('province') is-invalid @enderror">
                                @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Zip Code</label>
                                <input type="text" name="zip_code"
                                       value="{{ old('zip_code', $profile?->zip_code) }}"
                                       class="form-control @error('zip_code') is-invalid @enderror">
                                @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Account Credentials</p>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="bi bi-check-lg me-1"></i> Update User
                            </button>
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="btn btn-outline-secondary px-4 rounded-3">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSpecialty(role) {
            const field = document.getElementById('specialtyField');
            field.style.display = role === 'doctor' ? '' : 'none';
        }
    </script>

</x-app-layout>