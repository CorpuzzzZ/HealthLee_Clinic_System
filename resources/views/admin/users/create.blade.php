<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Add New User</h5>
                <small class="text-muted">Create a new account and profile</small>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-5">

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 mb-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        {{-- Role --}}
                        <div class="mb-4">
                            <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role"
                                    class="form-select form-select-lg @error('role') is-invalid @enderror"
                                    required onchange="toggleSpecialty(this.value)">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role...</option>
                                <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                                <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>Patient</option>
                                <option value="doctor"  {{ old('role') === 'doctor'  ? 'selected' : '' }}>Doctor</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Personal Information</p>

                        {{-- Name Row --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                       value="{{ old('first_name') }}"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" name="middle_name"
                                       value="{{ old('middle_name') }}"
                                       class="form-control @error('middle_name') is-invalid @enderror">
                                @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name"
                                       value="{{ old('last_name') }}"
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
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select...</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Age <span class="text-danger">*</span></label>
                                <input type="number" name="age"
                                       value="{{ old('age') }}"
                                       class="form-control @error('age') is-invalid @enderror"
                                       min="1" max="120" required>
                                @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_number"
                                       value="{{ old('contact_number') }}"
                                       class="form-control @error('contact_number') is-invalid @enderror"
                                       required>
                                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Specialty (Doctor only) --}}
                        <div class="row g-3 mb-3" id="specialtyField"
                             style="{{ old('role') === 'doctor' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Specialty <span class="text-danger">*</span></label>
                                <input type="text" name="specialty"
                                       value="{{ old('specialty') }}"
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
                                   value="{{ old('street') }}"
                                   class="form-control @error('street') is-invalid @enderror">
                            @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" name="barangay"
                                       value="{{ old('barangay') }}"
                                       class="form-control @error('barangay') is-invalid @enderror">
                                @error('barangay') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city"
                                       value="{{ old('city') }}"
                                       class="form-control @error('city') is-invalid @enderror">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Province</label>
                                <input type="text" name="province"
                                       value="{{ old('province') }}"
                                       class="form-control @error('province') is-invalid @enderror">
                                @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Zip Code</label>
                                <input type="text" name="zip_code"
                                       value="{{ old('zip_code') }}"
                                       class="form-control @error('zip_code') is-invalid @enderror">
                                @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Account Credentials</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autocomplete="off">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required autocomplete="new-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation"
                                       class="form-control"
                                       required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="bi bi-person-plus me-1"></i> Create User
                            </button>
                            <a href="{{ route('admin.users.index') }}"
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