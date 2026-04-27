<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Edit Medical Record</h5>
                <small class="text-muted">Record #{{ $medicalRecord->id }}</small>
            </div>
            <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}"
               class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Record
            </a>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">

                {{-- Patient & Appointment Summary Header --}}
                <div class="card-header border-0 pt-4 px-4 pb-3"
                     style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                        {{-- Patient --}}
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                 style="width: 44px; height: 44px; font-size: 0.85rem; background: #198754;">
                                {{ strtoupper(substr($medicalRecord->patient->first_name, 0, 1)) }}{{ strtoupper(substr($medicalRecord->patient->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold small">
                                    {{ $medicalRecord->patient->first_name }} {{ $medicalRecord->patient->last_name }}
                                </div>
                                <small class="text-muted">
                                    Appointment on {{ $medicalRecord->appointment->appointment_date->format('d M Y') }}
                                    at {{ \Carbon\Carbon::parse($medicalRecord->appointment->appointment_time)->format('h:i A') }}
                                </small>
                            </div>
                        </div>

                        <span class="badge rounded-pill px-3 py-2"
                              style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ ucfirst($medicalRecord->appointment->status) }}
                        </span>

                    </div>
                </div>

                <div class="card-body p-5">

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 mb-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('doctor.medical-records.update', $medicalRecord) }}">
                        @csrf
                        @method('PUT')

                        <p class="fw-semibold text-muted mb-3">Consultation Details</p>

                        {{-- Diagnosis --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Diagnosis <span class="text-danger">*</span>
                            </label>
                            <textarea name="diagnosis"
                                      rows="4"
                                      class="form-control @error('diagnosis') is-invalid @enderror"
                                      placeholder="Enter diagnosis details..."
                                      required>{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
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
                                      required>{{ old('treatment', $medicalRecord->treatment) }}</textarea>
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
                                      placeholder="Additional observations or follow-up instructions...">{{ old('notes', $medicalRecord->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="bi bi-check-lg me-1"></i> Update Record
                            </button>
                            <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}"
                               class="btn btn-outline-secondary px-4 rounded-3">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>