<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Add Medical Record</h5>
                <small class="text-muted">Create a new consultation record</small>
            </div>
            <a href="{{ route('doctor.medical-records.index') }}"
               class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Records
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

                    @if($appointments->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                            <div class="fw-medium">No completed appointments available</div>
                            <small>Medical records can only be created from completed appointments that don't have a record yet.</small>
                        </div>
                    @else

                        <form method="POST" action="{{ route('doctor.medical-records.store') }}">
                            @csrf

                            <p class="fw-semibold text-muted mb-3">Consultation Details</p>

                            {{-- Appointment --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Appointment <span class="text-danger">*</span>
                                </label>
                                <select name="appointment_id"
                                        class="form-select form-select-lg @error('appointment_id') is-invalid @enderror"
                                        required>
                                    <option value="" disabled {{ old('appointment_id') ? '' : 'selected' }}>
                                        Select appointment...
                                    </option>
                                    @foreach($appointments as $appointment)
                                        <option value="{{ $appointment->id }}"
                                                {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                                            {{ $appointment->appointment_date->format('d M Y') }}
                                            — {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                            ({{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('appointment_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Only completed appointments without an existing record are shown.
                                </small>
                            </div>

                            <hr class="my-4">

                            {{-- Diagnosis --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Diagnosis <span class="text-danger">*</span>
                                </label>
                                <textarea name="diagnosis"
                                          rows="4"
                                          class="form-control @error('diagnosis') is-invalid @enderror"
                                          placeholder="Enter diagnosis details..."
                                          required>{{ old('diagnosis') }}</textarea>
                                @error('diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Treatment --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Treatment <span class="text-danger">*</span>
                                </label>
                                <textarea name="treatment"
                                          rows="4"
                                          class="form-control @error('treatment') is-invalid @enderror"
                                          placeholder="Enter treatment plan or medications..."
                                          required>{{ old('treatment') }}</textarea>
                                @error('treatment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Notes <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <textarea name="notes"
                                          rows="3"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Additional observations or follow-up instructions...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 rounded-3">
                                    <i class="bi bi-file-medical me-1"></i> Save Record
                                </button>
                                <a href="{{ route('doctor.medical-records.index') }}"
                                   class="btn btn-outline-secondary px-4 rounded-3">
                                    Cancel
                                </a>
                            </div>

                        </form>

                    @endif

                </div>
            </div>
        </div>
    </div>

</x-app-layout>