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
                            <label for="role" class="form-label fw-semibold">Role <span
                                    class="text-danger">*</span></label>
                            <select id="role" name="role"
                                class="form-select form-select-lg @error('role') is-invalid @enderror" required
                                onchange="handleRoleChange(this.value)">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role...</option>
                                <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
                                <option value="patient" {{ old('role')==='patient' ? 'selected' : '' }}>Patient</option>
                                <option value="doctor" {{ old('role')==='doctor' ? 'selected' : '' }}>Doctor</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Personal Information</p>

                        {{-- Name Row --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                    class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                    class="form-control @error('middle_name') is-invalid @enderror">
                                @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                    class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Gender & Contact --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror"
                                    required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select...</option>
                                    <option value="male" {{ old('gender')==='male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender')==='female' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="other" {{ old('gender')==='other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                                    class="form-control @error('contact_number') is-invalid @enderror" required>
                                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Specialty (Doctor only) --}}
                        <div class="row g-3 mb-3" id="specialtyField"
                            style="{{ old('role') === 'doctor' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Specialty <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="specialty" value="{{ old('specialty') }}"
                                    class="form-control @error('specialty') is-invalid @enderror"
                                    placeholder="e.g. Cardiology, Pediatrics...">
                                @error('specialty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Services Section (Doctor only) --}}
                        <div id="servicesSection" style="{{ old('role') === 'doctor' ? '' : 'display: none;' }}">
                            <hr class="my-4">

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p class="fw-semibold text-muted mb-0">Services Offered</p>
                                    <small class="text-muted">Add the services this doctor provides</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                    onclick="addServiceRow()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Service
                                </button>
                            </div>

                            {{-- Validation errors for services --}}
                            @if($errors->has('services.*'))
                            <div class="alert alert-danger rounded-3 border-0 mb-3">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                Please check the service fields below.
                            </div>
                            @endif

                            <div id="servicesContainer">
                                {{-- Re-populate on validation error --}}
                                @if(old('services'))
                                @foreach(old('services') as $i => $svc)
                                <div class="service-row card border-0 mb-3"
                                    style="background: #f8f9fa; border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-semibold small text-muted">
                                                <i class="bi bi-clipboard2-pulse me-1"></i> Service {{ $i + 1 }}
                                            </span>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1"
                                                onclick="removeServiceRow(this)" title="Remove">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">
                                                    Service Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="services[{{ $i }}][name]"
                                                    value="{{ $svc['name'] ?? '' }}"
                                                    class="form-control form-control-sm @error('services.'.$i.'.name') is-invalid @enderror"
                                                    placeholder="e.g. General Consultation" required>
                                                @error('services.'.$i.'.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Price (₱)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" name="services[{{ $i }}][price]"
                                                        value="{{ $svc['price'] ?? '' }}"
                                                        class="form-control @error('services.'.$i.'.price') is-invalid @enderror"
                                                        placeholder="0.00" step="0.01" min="0">
                                                    @error('services.'.$i.'.price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Description</label>
                                                <textarea name="services[{{ $i }}][description]"
                                                    class="form-control form-control-sm @error('services.'.$i.'.description') is-invalid @enderror"
                                                    rows="2"
                                                    placeholder="Brief description of this service...">{{ $svc['description'] ?? '' }}</textarea>
                                                @error('services.'.$i.'.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                {{-- Default: one empty row when doctor is selected --}}
                                <div id="servicesPlaceholder" class="text-center py-4 text-muted"
                                    style="{{ old('role') === 'doctor' ? 'display: none;' : '' }}">
                                    <i class="bi bi-clipboard2-pulse fs-2 d-block mb-2 opacity-25"></i>
                                    <small>No services added yet. Click <strong>Add Service</strong> to start.</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Medical Info (Patient only) --}}
                        <div id="medicalFields" style="{{ old('role') === 'patient' ? '' : 'display: none;' }}">
                            <hr class="my-4">
                            <p class="fw-semibold text-muted mb-3">Medical Information</p>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Birthdate</label>
                                    <input type="date" name="birthdate" value="{{ old('birthdate') }}"
                                        max="{{ now()->subDay()->format('Y-m-d') }}"
                                        class="form-control @error('birthdate') is-invalid @enderror">
                                    @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Blood Type</label>
                                    <select name="blood_type"
                                        class="form-select @error('blood_type') is-invalid @enderror">
                                        <option value="">Select blood type...</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                        <option value="{{ $bt }}" {{ old('blood_type')===$bt ? 'selected' : '' }}>
                                            {{ $bt }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('blood_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Height (cm)</label>
                                    <div class="input-group">
                                        <input type="number" name="height" value="{{ old('height') }}" step="0.01"
                                            min="1" max="300" class="form-control @error('height') is-invalid @enderror"
                                            placeholder="e.g. 165.50">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('height') <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Weight (kg)</label>
                                    <div class="input-group">
                                        <input type="number" name="weight" value="{{ old('weight') }}" step="0.01"
                                            min="1" max="700" class="form-control @error('weight') is-invalid @enderror"
                                            placeholder="e.g. 60.00">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('weight') <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Address</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Street</label>
                            <input type="text" name="street" value="{{ old('street') }}"
                                class="form-control @error('street') is-invalid @enderror">
                            @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" name="barangay" value="{{ old('barangay') }}"
                                    class="form-control @error('barangay') is-invalid @enderror">
                                @error('barangay') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                    class="form-control @error('city') is-invalid @enderror">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Province</label>
                                <input type="text" name="province" value="{{ old('province') }}"
                                    class="form-control @error('province') is-invalid @enderror">
                                @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Zip Code</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                                    class="form-control @error('zip_code') is-invalid @enderror">
                                @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Account Credentials</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required autocomplete="off">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required
                                    autocomplete="new-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required
                                    autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="bi bi-person-plus me-1"></i> Create User
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4 rounded-3">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let serviceIndex = {{ old('services') ? count(old('services')) : 0 }};

        function handleRoleChange(role) {
            document.getElementById('specialtyField').style.display  = role === 'doctor'  ? '' : 'none';
            document.getElementById('servicesSection').style.display = role === 'doctor'  ? '' : 'none';
            document.getElementById('medicalFields').style.display   = role === 'patient' ? '' : 'none';
        }

        function addServiceRow() {
            // Hide placeholder if visible
            const placeholder = document.getElementById('servicesPlaceholder');
            if (placeholder) placeholder.style.display = 'none';

            const container = document.getElementById('servicesContainer');
            const idx       = serviceIndex++;

            const row = document.createElement('div');
            row.className = 'service-row card border-0 mb-3';
            row.style.cssText = 'background: #f8f9fa; border-radius: 10px;';

            row.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold small text-muted">
                            <i class="bi bi-clipboard2-pulse me-1"></i> Service
                        </span>
                        <button type="button"
                            class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1"
                            onclick="removeServiceRow(this)" title="Remove">
                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Service Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="services[${idx}][name]"
                                class="form-control form-control-sm"
                                placeholder="e.g. General Consultation"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Price (₱)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number"
                                    name="services[${idx}][price]"
                                    class="form-control"
                                    placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description</label>
                            <textarea
                                name="services[${idx}][description]"
                                class="form-control form-control-sm"
                                rows="2"
                                placeholder="Brief description of this service..."></textarea>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(row);
        }

        function removeServiceRow(btn) {
            const row = btn.closest('.service-row');
            row.remove();

            const remaining = document.querySelectorAll('.service-row').length;
            const placeholder = document.getElementById('servicesPlaceholder');
            if (remaining === 0 && placeholder) {
                placeholder.style.display = '';
            }
        }

        (function () {
            const roleEl = document.getElementById('role');
            if (roleEl.value === 'doctor' && document.querySelectorAll('.service-row').length === 0) {
                addServiceRow();
            }
        })();
    </script>

</x-app-layout>