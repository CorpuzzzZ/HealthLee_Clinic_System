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

                        {{-- Role (read-only — cannot be changed after creation) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Role</label>
                            {{-- Hidden input so $request->role is still available in the controller --}}
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="form-control-plaintext d-flex align-items-center gap-2"
                                style="padding: 0.5rem 0;">
                                @if($user->role === 'admin')
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #e7f1ff; color: #0d6efd; font-size: 0.85rem;">
                                    <i class="bi bi-shield-fill me-1"></i> Admin
                                </span>
                                @elseif($user->role === 'doctor')
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #e0f7fc; color: #0097a7; font-size: 0.85rem;">
                                    <i class="bi bi-person-badge-fill me-1"></i> Doctor
                                </span>
                                @else
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #e8f5ee; color: #198754; font-size: 0.85rem;">
                                    <i class="bi bi-person-heart-fill me-1"></i> Patient
                                </span>
                                @endif
                                <small class="text-muted">
                                    <i class="bi bi-lock-fill me-1"></i> Role cannot be changed after account creation.
                                </small>
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Personal Information</p>

                        {{-- Name Row --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                    value="{{ old('first_name', $profile?->first_name) }}"
                                    class="form-control @error('first_name') is-invalid @enderror" required>
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
                                <label class="form-label fw-semibold">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name', $profile?->last_name) }}"
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
                                    <option value="" disabled>Select...</option>
                                    <option value="male" {{ old('gender', $profile?->gender) === 'male' ? 'selected' :
                                        '' }}>Male</option>
                                    <option value="female" {{ old('gender', $profile?->gender) === 'female' ? 'selected'
                                        : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $profile?->gender) === 'other' ? 'selected' :
                                        '' }}>Other</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Number <span
                                        class="text-danger">*</span></label>
                                @php
                                $contactNumber = old('contact_number',
                                $profile?->contact->contact_number // patient/doctor (normalized)
                                ?? $profile?->contact_number // admin (flat)
                                );
                                @endphp
                                <input type="text" name="contact_number" value="{{ $contactNumber }}"
                                    class="form-control @error('contact_number') is-invalid @enderror" required>
                                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Specialty (Doctor only) --}}
                        <div class="row g-3 mb-3" id="specialtyField"
                            style="{{ $user->role === 'doctor' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Specialty <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="specialty"
                                    value="{{ old('specialty', $profile?->specialty ?? '') }}"
                                    class="form-control @error('specialty') is-invalid @enderror"
                                    placeholder="e.g. Cardiology, Pediatrics...">
                                @error('specialty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- ── Services Section (Doctor only) ── --}}
                        <div id="servicesSection" style="{{ $user->role === 'doctor' ? '' : 'display: none;' }}">
                            <hr class="my-4">

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p class="fw-semibold text-muted mb-0">Services Offered</p>
                                    <small class="text-muted">Add, update, or remove the services this doctor
                                        provides</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                    onclick="addServiceRow()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Service
                                </button>
                            </div>

                            @if($errors->has('services.*'))
                            <div class="alert alert-danger rounded-3 border-0 mb-3">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                Please check the service fields below.
                            </div>
                            @endif

                            <div id="servicesContainer">

                                {{-- ── Repopulate from old() on validation error ── --}}
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

                                {{-- ── Pre-fill from existing saved services ── --}}
                                @elseif($user->role === 'doctor' && $profile?->services?->isNotEmpty())
                                @foreach($profile->services as $i => $service)
                                <div class="service-row card border-0 mb-3"
                                    style="background: #f8f9fa; border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-semibold small text-muted">
                                                <i class="bi bi-clipboard2-pulse me-1"></i>
                                                {{ $service->name }}
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
                                                    value="{{ $service->name }}" class="form-control form-control-sm"
                                                    placeholder="e.g. General Consultation" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Price (₱)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" name="services[{{ $i }}][price]"
                                                        value="{{ $service->price }}" class="form-control"
                                                        placeholder="0.00" step="0.01" min="0">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Description</label>
                                                <textarea name="services[{{ $i }}][description]"
                                                    class="form-control form-control-sm" rows="2"
                                                    placeholder="Brief description of this service...">{{ $service->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                @else
                                {{-- No services yet --}}
                                <div id="servicesPlaceholder" class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard2-pulse fs-2 d-block mb-2 opacity-25"></i>
                                    <small>No services added yet. Click <strong>Add Service</strong> to start.</small>
                                </div>
                                @endif

                            </div>
                        </div>

                        {{-- Medical Info (Patient only) --}}
                        <div id="medicalFields" style="{{ $user->role === 'patient' ? '' : 'display: none;' }}">
                            <hr class="my-4">
                            <p class="fw-semibold text-muted mb-3">Medical Information</p>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Birthdate</label>
                                    <input type="date" name="birthdate"
                                        value="{{ old('birthdate', $profile?->birthdate?->format('Y-m-d')) }}"
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
                                        <option value="{{ $bt }}" {{ old('blood_type', $profile?->blood_type) === $bt ?
                                            'selected' : '' }}>
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
                                        <input type="number" name="height"
                                            value="{{ old('height', $profile?->height) }}" step="0.01" min="1" max="300"
                                            class="form-control @error('height') is-invalid @enderror"
                                            placeholder="e.g. 165.50">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('height') <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Weight (kg)</label>
                                    <div class="input-group">
                                        <input type="number" name="weight"
                                            value="{{ old('weight', $profile?->weight) }}" step="0.01" min="1" max="700"
                                            class="form-control @error('weight') is-invalid @enderror"
                                            placeholder="e.g. 60.00">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('weight') <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Address --}}
                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Address</p>

                        @php
                        $addr = $profile?->address ?? $profile;
                        @endphp

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Street</label>
                            <input type="text" name="street" value="{{ old('street', $addr?->street) }}"
                                class="form-control @error('street') is-invalid @enderror">
                            @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" name="barangay" value="{{ old('barangay', $addr?->barangay) }}"
                                    class="form-control @error('barangay') is-invalid @enderror">
                                @error('barangay') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" value="{{ old('city', $addr?->city) }}"
                                    class="form-control @error('city') is-invalid @enderror">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Province</label>
                                <input type="text" name="province" value="{{ old('province', $addr?->province) }}"
                                    class="form-control @error('province') is-invalid @enderror">
                                @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Zip Code</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code', $addr?->zip_code) }}"
                                    class="form-control @error('zip_code') is-invalid @enderror">
                                @error('zip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="fw-semibold text-muted mb-3">Account Credentials</p>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Address <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" required>
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
        // Start index after however many rows were server-rendered
        let serviceIndex = {{ old('services')
            ? count(old('services'))
            : ($user->role === 'doctor' && $profile?->services ? $profile->services->count() : 0) }};

        function addServiceRow() {
            // Hide the placeholder if it still exists
            const placeholder = document.getElementById('servicesPlaceholder');
            if (placeholder) placeholder.style.display = 'none';

            const container = document.getElementById('servicesContainer');
            const idx       = serviceIndex++;

            const row = document.createElement('div');
            row.className   = 'service-row card border-0 mb-3';
            row.style.cssText = 'background: #f8f9fa; border-radius: 10px;';

            row.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold small text-muted">
                            <i class="bi bi-clipboard2-pulse me-1"></i> New Service
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
            btn.closest('.service-row').remove();

            // Show placeholder again if no rows remain
            const remaining   = document.querySelectorAll('.service-row').length;
            const placeholder = document.getElementById('servicesPlaceholder');
            if (remaining === 0 && placeholder) {
                placeholder.style.display = '';
            }
        }
    </script>

</x-app-layout>