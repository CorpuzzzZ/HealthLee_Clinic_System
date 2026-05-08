<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Edit Patient Record</h5>
                <small class="text-muted">
                    Update medical information for
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </small>
            </div>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.patient-records.show', $patient) }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @else
            <a href="{{ route('doctor.patient-records.show', $patient) }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @endif
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">

                {{-- Patient Header --}}
                <div class="card-header border-0 pt-4 px-4 pb-3"
                    style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                            style="width: 48px; height: 48px; font-size: 0.9rem; background: #198754;">
                            {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{
                            strtoupper(substr($patient->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">
                                {{ $patient->first_name }}
                                {{ $patient->middle_name ? $patient->middle_name . ' ' : '' }}
                                {{ $patient->last_name }}
                            </div>
                            <small class="text-muted">
                                {{ ucfirst($patient->gender ?? '—') }},
                                {{ $patient->birthdate ? $patient->birthdate->age : '—' }} yrs
                                &bull; {{ $patient->user->contact->contact_number ?? '—' }}
                            </small>
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

                    @if(Auth::user()->role === 'admin')
                    <form method="POST" action="{{ route('admin.patient-records.update', $patient) }}">
                        @else
                        <form method="POST" action="{{ route('doctor.patient-records.update', $patient) }}">
                            @endif
                            @csrf
                            @method('PUT')

                            <p class="fw-semibold text-muted mb-3">Medical Information</p>

                            {{-- Birthdate --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Birthdate</label>
                                <input type="date" name="birthdate"
                                    value="{{ old('birthdate', $patient->birthdate?->format('Y-m-d')) }}"
                                    max="{{ now()->subDay()->format('Y-m-d') }}"
                                    class="form-control @error('birthdate') is-invalid @enderror">
                                @error('birthdate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Blood Type --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Blood Type</label>
                                <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
                                    <option value="">Select blood type...</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                    <option value="{{ $bt }}" {{ old('blood_type', $patient->blood_type) === $bt ?
                                        'selected' : '' }}>
                                        {{ $bt }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('blood_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Height & Weight --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Height (cm)</label>
                                    <div class="input-group">
                                        <input type="number" name="height" value="{{ old('height', $patient->height) }}"
                                            step="0.01" min="1" max="300"
                                            class="form-control @error('height') is-invalid @enderror"
                                            placeholder="e.g. 165.50">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('height')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Weight (kg)</label>
                                    <div class="input-group">
                                        <input type="number" name="weight" value="{{ old('weight', $patient->weight) }}"
                                            step="0.01" min="1" max="700"
                                            class="form-control @error('weight') is-invalid @enderror"
                                            placeholder="e.g. 60.00">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('weight')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- BMI Preview --}}
                            <div class="p-3 rounded-3 mb-4"
                                style="background: #e7f1ff; border-left: 3px solid #0d6efd;">
                                <small class="text-primary">
                                    <i class="bi bi-calculator me-1"></i>
                                    BMI will be automatically calculated from height and weight.
                                </small>
                                <div id="bmiPreview" class="mt-1" style="display: none;">
                                    <small class="fw-semibold" id="bmiValue"></small>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 rounded-3">
                                    <i class="bi bi-check-lg me-1"></i> Update Record
                                </button>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.patient-records.show', $patient) }}"
                                    class="btn btn-outline-secondary px-4 rounded-3">
                                    Cancel
                                </a>
                                @else
                                <a href="{{ route('doctor.patient-records.show', $patient) }}"
                                    class="btn btn-outline-secondary px-4 rounded-3">
                                    Cancel
                                </a>
                                @endif
                            </div>

                        </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Live BMI preview
        const heightInput = document.querySelector('input[name="height"]');
        const weightInput = document.querySelector('input[name="weight"]');
        const bmiPreview  = document.getElementById('bmiPreview');
        const bmiValue    = document.getElementById('bmiValue');

        function calcBMI() {
            const h = parseFloat(heightInput.value);
            const w = parseFloat(weightInput.value);

            if (h > 0 && w > 0) {
                const bmi = w / ((h / 100) ** 2);
                let label, color;

                if (bmi < 18.5)      { label = 'Underweight'; color = '#e6a800'; }
                else if (bmi < 25)   { label = 'Normal';       color = '#198754'; }
                else if (bmi < 30)   { label = 'Overweight';   color = '#fd7e14'; }
                else                 { label = 'Obese';         color = '#dc3545'; }

                bmiValue.innerHTML = `BMI: <span style="color: ${color};">${bmi.toFixed(2)} — ${label}</span>`;
                bmiPreview.style.display = 'block';
            } else {
                bmiPreview.style.display = 'none';
            }
        }

        heightInput.addEventListener('input', calcBMI);
        weightInput.addEventListener('input', calcBMI);

        // Trigger on load if values exist
        window.addEventListener('load', calcBMI);
    </script>

</x-app-layout>