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

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container .select2-selection--single {
            height: 50px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px !important;
            color: #212529 !important;
            padding-left: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px !important;
            right: 10px !important;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #0d6efd !important;
        }

        .select2-dropdown {
            border-radius: 8px !important;
            border: 1px solid #dee2e6 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }

        .select2-search--dropdown .select2-search__field {
            border-radius: 6px !important;
            border: 1px solid #dee2e6 !important;
            padding: 6px 10px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
        }

        .doctor-option-specialty {
            font-size: 0.78rem;
            color: #6c757d;
        }
    </style>

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

                        {{-- Doctor Combo Box --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Doctor <span class="text-danger">*</span>
                            </label>
                            <select name="doctor_id"
                                    id="doctorSelect"
                                    class="form-select @error('doctor_id') is-invalid @enderror"
                                    style="width: 100%;"
                                    required>
                                <option value="">Search for a doctor...</option>
                                @foreach($doctors as $d)
                                    <option value="{{ $d->id }}"
                                            data-specialty="{{ $d->specialty ?? 'General' }}"
                                            {{ old('doctor_id', $doctor?->id) == $d->id ? 'selected' : '' }}>
                                        Dr. {{ $d->first_name }} {{ $d->last_name }}
                                        {{ $d->specialty ? '— ' . $d->specialty : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Type to search by name or specialty. You can also
                                <a href="{{ route('patient.doctors.index') }}" class="text-primary">
                                    browse doctors
                                </a>
                                to check their availability first.
                            </small>
                        </div>

                        {{-- Date & Time --}}
                        <div class="row g-3 mb-3">

                            {{-- Date --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Appointment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="appointment_date"
                                       value="{{ old('appointment_date', $prefilledDate ?? '') }}"
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

                            {{-- Time --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Appointment Time <span class="text-danger">*</span>
                                </label>
                                <input type="time"
                                       name="appointment_time"
                                       id="appointmentTime"
                                       value="{{ old('appointment_time', $prefilledTime ?? '') }}"
                                       class="form-control @error('appointment_time') is-invalid @enderror"
                                       required>
                                @error('appointment_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Dynamic slot hint --}}
                                <small class="text-muted mt-1 d-block" id="timeHint">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Each appointment slot is <strong>1 hour</strong>.
                                </small>

                                {{-- Show available time slots if doctor is pre-selected --}}
                                @if($doctor && $doctor->availabilities->isNotEmpty())
                                    <small class="text-muted mt-1 d-block">
                                        <i class="bi bi-clock me-1 text-success"></i>
                                        <strong>Available times:</strong>
                                        {{ $doctor->availabilities->map(fn($a) =>
                                            \Carbon\Carbon::parse($a->start_time)->format('h:i A') .
                                            ' — ' .
                                            \Carbon\Carbon::parse($a->end_time)->format('h:i A')
                                        )->implode(', ') }}
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
                                Each booking occupies a <strong>1-hour</strong> slot.
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

    {{-- jQuery (required for Select2) --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // ── Select2 Init ──
        $(document).ready(function () {
            $('#doctorSelect').select2({
                placeholder: 'Search for a doctor by name or specialty...',
                allowClear: true,
                width: '100%',
                templateResult: formatDoctor,
                templateSelection: formatDoctorSelected,
            });

            function formatDoctor(option) {
                if (!option.id) return option.text;

                const specialty = $(option.element).data('specialty');
                const initials  = option.text
                                    .replace('Dr. ', '')
                                    .split(' ')
                                    .map(n => n[0])
                                    .join('')
                                    .substring(0, 2)
                                    .toUpperCase();

                return $(`
                    <div class="d-flex align-items-center gap-2 py-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width: 32px; height: 32px; font-size: 0.7rem; background: #0dcaf0;">
                            ${initials}
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 500;">
                                ${option.text.split('—')[0].trim()}
                            </div>
                            <div class="doctor-option-specialty">
                                ${specialty || 'General'}
                            </div>
                        </div>
                    </div>
                `);
            }

            function formatDoctorSelected(option) {
                if (!option.id) return option.text;
                return option.text;
            }
        });

        // ── Time Slot Duration Hint ──
        function formatTime(h, m) {
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${String(m).padStart(2, '0')} ${ampm}`;
        }

        document.getElementById('appointmentTime').addEventListener('change', function () {
            const val = this.value;
            if (!val) return;

            const [h, m] = val.split(':').map(Number);
            const endH   = (h + 1) % 24;

            document.getElementById('timeHint').innerHTML = `
                <i class="bi bi-hourglass-split me-1"></i>
                Your slot: <strong>${formatTime(h, m)}</strong>
                &ndash;
                <strong>${formatTime(endH, m)}</strong>
                <span class="badge rounded-pill ms-1"
                      style="background: #e7f1ff; color: #0d6efd; font-size: 0.65rem;">
                    1 hour
                </span>
            `;
        });

        // ── Trigger time hint on page load if prefilled ──
        window.addEventListener('load', function () {
            const timeInput = document.getElementById('appointmentTime');
            if (timeInput.value) {
                timeInput.dispatchEvent(new Event('change'));
            }
        });
    </script>

</x-app-layout>