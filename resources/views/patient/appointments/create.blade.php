<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Book Appointment</h5>
                <small class="text-muted">Schedule a new appointment</small>
            </div>
            <a href="{{ route('patient.appointments.index') }}"
               class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Appointments
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

                    <form method="POST" action="{{ route('patient.appointments.store') }}">
                        @csrf

                        <p class="fw-semibold text-muted mb-3">Appointment Details</p>

                        {{-- Doctor --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Doctor <span class="text-danger">*</span>
                            </label>
                            <select name="doctor_id"
                                    class="form-select form-select-lg @error('doctor_id') is-invalid @enderror"
                                    required>
                                <option value="" disabled {{ old('doctor_id', $doctor?->id) ? '' : 'selected' }}>
                                    Select doctor...
                                </option>
                                @foreach($doctors as $d)
                                    <option value="{{ $d->id }}"
                                            {{ old('doctor_id', $doctor?->id) == $d->id ? 'selected' : '' }}>
                                        Dr. {{ $d->first_name }} {{ $d->last_name }}
                                        {{ $d->specialty ? '— ' . $d->specialty : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                You can also
                                <a href="{{ route('patient.doctors.index') }}" class="text-primary">
                                    browse doctors
                                </a>
                                to check their availability first.
                            </small>
                        </div>

                        {{-- Date & Time --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Appointment Date <span class="text-danger">*</span>
        </label>
        <input type="date"
               name="appointment_date"
               value="{{ old('appointment_date') }}"
               min="{{ now()->format('Y-m-d') }}"
               class="form-control @error('appointment_date') is-invalid @enderror"
               required>
        @error('appointment_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Show available dates if doctor is pre-selected --}}
        @if($doctor && $doctor->availabilities->isNotEmpty())
            <small class="text-muted mt-1 d-block">
                <i class="bi bi-calendar-check me-1 text-success"></i>
                <strong>Available dates:</strong>
                {{ $doctor->availabilities->pluck('available_date')->map(fn($d) => $d->format('d M Y'))->implode(', ') }}
            </small>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Appointment Time <span class="text-danger">*</span>
        </label>
        <input type="time"
               name="appointment_time"
               value="{{ old('appointment_time') }}"
               class="form-control @error('appointment_time') is-invalid @enderror"
               required>
        @error('appointment_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Show available time slots if doctor is pre-selected --}}
        @if($doctor && $doctor->availabilities->isNotEmpty())
            <small class="text-muted mt-1 d-block">
                <i class="bi bi-clock me-1 text-success"></i>
                <strong>Available times:</strong>
                {{ $doctor->availabilities->map(fn($a) => \Carbon\Carbon::parse($a->start_time)->format('h:i A') . ' — ' . \Carbon\Carbon::parse($a->end_time)->format('h:i A'))->implode(', ') }}
            </small>
        @endif
    </div>
</div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Notes <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea name="notes"
                                      rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Describe your symptoms or reason for visit...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info box --}}
                        <div class="p-3 rounded-3 mb-4"
                             style="background: #e7f1ff; border-left: 3px solid #0d6efd;">
                            <small class="text-primary">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Your appointment will be set to <strong>Pending</strong> and the
                                doctor will confirm it. You will receive a notification once confirmed.
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="bi bi-calendar-plus me-1"></i> Book Appointment
                            </button>
                            <a href="{{ route('patient.appointments.index') }}"
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