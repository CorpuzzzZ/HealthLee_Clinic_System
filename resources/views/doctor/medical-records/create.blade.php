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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
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

                    @if($appointments->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                        <div class="fw-medium">No completed appointments available</div>
                        <small>Medical records can only be created from completed appointments that don't have a record
                            yet.</small>
                    </div>
                    @else

                    <form method="POST" action="{{ route('doctor.medical-records.store') }}">
                        @csrf

                        <p class="fw-semibold text-muted mb-3">Consultation Details</p>

                        {{-- Appointment Combo Box --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Appointment <span class="text-danger">*</span>
                            </label>
                            <select name="appointment_id" id="appointmentSelect"
                                class="form-select @error('appointment_id') is-invalid @enderror" style="width: 100%;"
                                required>
                                <option value="">Search appointment...</option>
                                @foreach($appointments as $appt)
                                @php
                                // Pre-select priority: validation old() first, then URL preselection
                                $isSelected = old('appointment_id')
                                ? old('appointment_id') == $appt->id
                                : ($preselectedAppointment?->id === $appt->id);
                                @endphp
                                <option value="{{ $appt->id }}"
                                    data-date="{{ $appt->appointment_date->format('d M Y') }}"
                                    data-time="{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}"
                                    data-patient="{{ $appt->patient->first_name }} {{ $appt->patient->last_name }}"
                                    data-service="{{ $appt->service->name ?? 'No service' }}" {{ $isSelected
                                    ? 'selected' : '' }}>
                                    {{ $appt->appointment_date->format('d M Y') }}
                                    — {{ $appt->patient->first_name }} {{ $appt->patient->last_name }}
                                    ({{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }})
                                </option>
                                @endforeach
                            </select>
                            @error('appointment_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Only completed appointments without an existing record are shown.
                            </small>
                        </div>

                        {{-- Appointment Preview Card (shown after selection) --}}
                        <div id="appointmentPreview" class="mb-4" style="display: none;">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #0d6efd;">
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <small class="text-muted d-block">Patient</small>
                                        <span class="fw-semibold small" id="previewPatient"></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <small class="text-muted d-block">Date & Time</small>
                                        <span class="fw-semibold small" id="previewDateTime"></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <small class="text-muted d-block">Service</small>
                                        <span class="fw-semibold small" id="previewService"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Diagnosis --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Diagnosis <span class="text-danger">*</span>
                            </label>
                            <textarea name="diagnosis" rows="4"
                                class="form-control @error('diagnosis') is-invalid @enderror"
                                placeholder="Enter diagnosis details..." required>{{ old('diagnosis') }}</textarea>
                            @error('diagnosis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Treatment --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Treatment <span class="text-danger">*</span>
                            </label>
                            <textarea name="treatment" rows="4"
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
                            <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
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

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#appointmentSelect').select2({
                placeholder: 'Search by patient name or date...',
                allowClear: true,
                width: '100%',
                templateResult: formatAppointment,
                templateSelection: formatAppointmentSelected,
            });

            // ── Show preview card whenever selection changes ──
            $('#appointmentSelect').on('change', function () {
                const opt = this.options[this.selectedIndex];
                if (!this.value) {
                    document.getElementById('appointmentPreview').style.display = 'none';
                    return;
                }
                document.getElementById('previewPatient').textContent  = opt.dataset.patient;
                document.getElementById('previewDateTime').textContent = opt.dataset.date + ' ' + opt.dataset.time;
                document.getElementById('previewService').textContent  = opt.dataset.service;
                document.getElementById('appointmentPreview').style.display = '';
            });

            function formatAppointment(option) {
                if (!option.id) return option.text;

                const el      = option.element;
                const patient = $(el).data('patient');
                const date    = $(el).data('date');
                const time    = $(el).data('time');
                const service = $(el).data('service');

                const initials = patient.split(' ')
                                        .map(n => n[0])
                                        .join('')
                                        .substring(0, 2)
                                        .toUpperCase();

                return $(`
                    <div class="d-flex align-items-center gap-2 py-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width:32px;height:32px;font-size:0.7rem;background:#198754;">
                            ${initials}
                        </div>
                        <div>
                            <div style="font-size:0.875rem;font-weight:500;">${patient}</div>
                            <div style="font-size:0.75rem;color:#6c757d;">
                                ${date} · ${time}
                                ${service !== 'No service' ? ' · ' + service : ''}
                            </div>
                        </div>
                    </div>
                `);
            }

            function formatAppointmentSelected(option) {
                return option.text;
            }

            // ── Auto-trigger preview on load if appointment is already selected ──
            // Covers: redirect from appointment show page + validation error re-fill
            if ($('#appointmentSelect').val()) {
                $('#appointmentSelect').trigger('change');
            }
        });
    </script>

</x-app-layout>