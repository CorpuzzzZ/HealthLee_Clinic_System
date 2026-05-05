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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* ── Select2 ── */
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

        /* ── Date Cards ── */
        .date-card {
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
            padding: 10px 16px;
            cursor: pointer;
            background: #fff;
            text-align: center;
            min-width: 72px;
            transition: all 0.15s;
            user-select: none;
        }

        .date-card:hover {
            border-color: #0d6efd;
            background: #e7f1ff;
        }

        .date-card.selected {
            border-color: #0d6efd;
            border-width: 2px;
            background: #e7f1ff;
        }

        .date-card .dc-day {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .date-card .dc-num {
            font-size: 22px;
            font-weight: 500;
            color: #212529;
            line-height: 1.2;
        }

        .date-card .dc-month {
            font-size: 11px;
            color: #6c757d;
        }

        .date-card.selected .dc-day,
        .date-card.selected .dc-num,
        .date-card.selected .dc-month {
            color: #0d6efd;
        }

        /* ── Slot Cards ── */
        .slot-card {
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
            padding: 10px 18px;
            cursor: pointer;
            background: #fff;
            text-align: center;
            min-width: 120px;
            transition: all 0.15s;
            user-select: none;
        }

        .slot-card:hover:not(.booked) {
            border-color: #0d6efd;
            background: #e7f1ff;
        }

        .slot-card.selected {
            border-color: #0d6efd;
            border-width: 2px;
            background: #0d6efd;
        }

        .slot-card .sc-start {
            font-size: 0.85rem;
            font-weight: 500;
            color: #212529;
        }

        .slot-card .sc-end {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .slot-card.selected .sc-start {
            color: #fff;
        }

        .slot-card.selected .sc-end {
            color: rgba(255, 255, 255, 0.8);
        }

        .slot-card.booked {
            background: #f8f9fa;
            border-color: #e9ecef;
            cursor: not-allowed;
            opacity: 0.65;
        }

        .slot-card.booked .sc-start {
            color: #adb5bd;
            text-decoration: line-through;
        }

        .slot-card.booked .sc-end {
            color: #adb5bd;
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

                    <form method="POST" action="{{ route('patient.appointments.store') }}" id="bookingForm">
                        @csrf

                        <p class="fw-semibold text-muted mb-3">Appointment Details</p>

                        {{-- Doctor Select --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Doctor <span class="text-danger">*</span>
                            </label>
                            <select name="doctor_id" id="doctorSelect"
                                class="form-select @error('doctor_id') is-invalid @enderror" style="width: 100%;"
                                required>
                                <option value="">Search for a doctor...</option>
                                @foreach($doctors as $d)
                                <option value="{{ $d->id }}" data-specialty="{{ $d->specialty ?? 'General' }}"
                                    data-services="{{ $d->services->toJson() }}" {{ old('doctor_id')==$d->id ?
                                    'selected' : '' }}>
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
                                Only doctors with available slots are shown.
                                <a href="{{ route('patient.doctors.index') }}" class="text-primary">Browse all
                                    doctors</a>
                            </small>
                        </div>

                        {{-- Service Type --}}
                        <div class="mb-4" id="serviceSection" style="display: none;">
                            <label class="form-label fw-semibold">
                                Service Type <span class="text-danger">*</span>
                            </label>
                            <select name="service_id" id="serviceSelect"
                                class="form-select @error('service_id') is-invalid @enderror">
                                <option value="">Select a service...</option>
                            </select>
                            @error('service_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="serviceDetails" class="mt-2" style="display: none;">
                                <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted" id="serviceDescription"></small>
                                    <span class="badge rounded-pill ms-2" style="background: #e8f5ee; color: #198754;"
                                        id="servicePrice"></span>
                                </div>
                            </div>
                            <div id="noServicesMsg" class="mt-2" style="display: none;">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    This doctor has not listed specific services yet.
                                </small>
                            </div>
                        </div>

                        {{-- Available Date Cards --}}
                        <div class="mb-4" id="dateSection" style="display: none;">
                            <label class="form-label fw-semibold">
                                Available Dates <span class="text-danger">*</span>
                            </label>
                            <div id="dateGrid" class="d-flex flex-wrap gap-2 mt-2"></div>
                            <input type="hidden" name="appointment_date" id="appointmentDate"
                                value="{{ old('appointment_date') }}">
                            @error('appointment_date')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Time Slot Cards --}}
                        <div class="mb-4" id="slotSection" style="display: none;">
                            <label class="form-label fw-semibold">
                                Available Time Slots <span class="text-danger">*</span>
                            </label>
                            <input type="hidden" name="appointment_time" id="appointmentTime"
                                value="{{ old('appointment_time') }}">
                            <div id="slotGrid" class="d-flex flex-wrap gap-2 mt-2"></div>
                            <div id="slotLoading" style="display: none;" class="mt-2">
                                <small class="text-muted">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Loading available slots...
                                </small>
                            </div>
                            <div id="slotEmpty" style="display: none;" class="mt-2">
                                <small class="text-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    No available slots on this date. Please choose another date.
                                </small>
                            </div>
                            @error('appointment_time')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4" id="notesSection" style="display: none;">
                            <label class="form-label fw-semibold">
                                Notes <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                placeholder="Describe your symptoms or reason for visit...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info Box --}}
                        <div class="p-3 rounded-3 mb-4" style="background: #e7f1ff; border-left: 3px solid #0d6efd;">
                            <small class="text-primary">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Your appointment will be <strong>Pending</strong> until the doctor confirms.
                                Each slot is <strong>1 hour</strong>.
                                Slots marked
                                <span style="text-decoration: line-through; color: #adb5bd;">like this</span>
                                are already booked.
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" onclick="submitBooking()" class="btn btn-primary px-4 rounded-3">
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

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const SLOTS_URL = "{{ route('patient.appointments.slots') }}";

        const DAY_NAMES   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        const MONTH_NAMES = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // ── Doctor data map ──
        const doctorData = {};
        @foreach($doctors as $d)
            doctorData[{{ $d->id }}] = {
                specialty  : "{{ $d->specialty ?? 'General' }}",
                services   : {!! $d->services->toJson() !!},
                availDates : {!! $d->availabilities->pluck('available_date')->map(fn($dt) => $dt->format('Y-m-d'))->unique()->values()->toJson() !!},
            };
        @endforeach

        // ── Select2 ──
        $(document).ready(function () {
            $('#doctorSelect').select2({
                placeholder: 'Search by name or specialty...',
                allowClear: true,
                width: '100%',
                templateResult: formatDoctor,
                templateSelection: formatDoctorSelected,
            });

            $('#doctorSelect').on('change', function () {
                const id = $(this).val();
                onDoctorChange(id ? parseInt(id) : null);
            });

            function formatDoctor(option) {
                if (!option.id) return option.text;
                const specialty = $(option.element).data('specialty');
                const initials  = option.text.replace('Dr. ', '').split(' ')
                                    .map(n => n[0]).join('').substring(0, 2).toUpperCase();
                return $(`
                    <div class="d-flex align-items-center gap-2 py-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width:32px;height:32px;font-size:0.7rem;background:#0dcaf0;">
                            ${initials}
                        </div>
                        <div>
                            <div style="font-size:0.875rem;font-weight:500;">
                                ${option.text.split('—')[0].trim()}
                            </div>
                            <div style="font-size:0.78rem;color:#6c757d;">
                                ${specialty || 'General'}
                            </div>
                        </div>
                    </div>
                `);
            }

            function formatDoctorSelected(option) {
                return option.text;
            }
        });

        // ── On doctor change ──
        function onDoctorChange(doctorId) {
            resetSlots();
            resetDateCards();
            document.getElementById('appointmentDate').value = '';
            document.getElementById('serviceSelect').innerHTML = '<option value="">Select a service...</option>';

            if (!doctorId) {
                hide('serviceSection');
                hide('dateSection');
                hide('slotSection');
                hide('notesSection');
                return;
            }

            const data = doctorData[doctorId];

            // ── Services ──
            show('serviceSection');
            const svc        = document.getElementById('serviceSelect');
            const noSvc      = document.getElementById('noServicesMsg');
            const svcDetails = document.getElementById('serviceDetails');

            svcDetails.style.display = 'none';
            svc.innerHTML = '<option value="">Select a service...</option>';

            if (data.services.length === 0) {
                svc.style.display = 'none';
                noSvc.style.display = 'block';
            } else {
                svc.style.display = '';
                noSvc.style.display = 'none';
                data.services.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value               = s.id;
                    opt.textContent         = s.name + (s.price ? ` — ₱${parseFloat(s.price).toFixed(2)}` : '');
                    opt.dataset.description = s.description || '';
                    opt.dataset.price       = s.price || '';
                    svc.appendChild(opt);
                });
            }

            // ── Date Cards ──
            const dateGrid = document.getElementById('dateGrid');
            dateGrid.innerHTML = '';

            if (data.availDates.length === 0) {
                dateGrid.innerHTML = '<small class="text-muted">No upcoming dates available.</small>';
            } else {
                data.availDates.forEach(dateStr => {
                    const dt   = new Date(dateStr + 'T00:00:00');
                    const card = document.createElement('div');
                    card.className        = 'date-card';
                    card.dataset.date     = dateStr;
                    card.innerHTML = `
                        <div class="dc-day">${DAY_NAMES[dt.getDay()]}</div>
                        <div class="dc-num">${dt.getDate()}</div>
                        <div class="dc-month">${MONTH_NAMES[dt.getMonth()]}</div>`;

                    card.addEventListener('click', () => selectDate(card, dateStr, doctorId));
                    dateGrid.appendChild(card);
                });
            }

            show('dateSection');
            show('notesSection');
        }

        // ── Select a date card ──
        function selectDate(card, dateStr, doctorId) {
            document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById('appointmentDate').value = dateStr;

            // Reset and fetch slots
            resetSlots();
            show('slotSection');
            show('slotLoading');

            fetch(`${SLOTS_URL}?doctor_id=${doctorId}&date=${dateStr}`)
                .then(r => r.json())
                .then(data => {
                    hide('slotLoading');
                    renderSlots(data.slots);
                })
                .catch(() => {
                    hide('slotLoading');
                    show('slotEmpty');
                });
        }

        // ── Service detail preview ──
        document.getElementById('serviceSelect').addEventListener('change', function () {
            const opt     = this.options[this.selectedIndex];
            const desc    = opt.dataset.description;
            const price   = opt.dataset.price;
            const details = document.getElementById('serviceDetails');

            if (this.value && (desc || price)) {
                document.getElementById('serviceDescription').textContent = desc || '';
                document.getElementById('servicePrice').textContent =
                    price ? `₱${parseFloat(price).toFixed(2)}` : '';
                details.style.display = 'block';
            } else {
                details.style.display = 'none';
            }
        });

        // ── Render slot cards ──
        function renderSlots(slots) {
            const grid  = document.getElementById('slotGrid');
            grid.innerHTML = '';

            const available = slots.filter(s => !s.booked);
            if (available.length === 0) {
                show('slotEmpty');
                return;
            }

            const oldTime = "{{ old('appointment_time') }}";

            slots.forEach(slot => {
                const parts = slot.label.split(' – ');
                const card  = document.createElement('div');
                card.className        = 'slot-card' + (slot.booked ? ' booked' : '');
                card.dataset.value    = slot.value;
                card.innerHTML = `
                    <div class="sc-start">${parts[0] ?? slot.label}</div>
                    <div class="sc-end">${parts[1] ? '– ' + parts[1] : '1 hr'}</div>`;

                if (!slot.booked) {
                    card.addEventListener('click', () => selectSlot(card, slot.value));
                    // Restore after validation error
                    if (oldTime && slot.value === oldTime) {
                        selectSlot(card, slot.value);
                    }
                }

                grid.appendChild(card);
            });
        }

        function selectSlot(card, value) {
            document.querySelectorAll('.slot-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById('appointmentTime').value = value;
        }

        // ── Reset helpers ──
        function resetSlots() {
            document.getElementById('slotGrid').innerHTML    = '';
            document.getElementById('appointmentTime').value = '';
            hide('slotSection');
            hide('slotLoading');
            hide('slotEmpty');
        }

        function resetDateCards() {
            document.getElementById('dateGrid').innerHTML = '';
        }

        function show(id) { document.getElementById(id).style.display = ''; }
        function hide(id) { document.getElementById(id).style.display = 'none'; }

        // ── Client-side validation before submit ──
        function submitBooking() {
            const doctorId = document.getElementById('doctorSelect').value;
            const date     = document.getElementById('appointmentDate').value;
            const time     = document.getElementById('appointmentTime').value;

            if (!doctorId) {
                alert('Please select a doctor.');
                return;
            }
            if (!date) {
                alert('Please select an appointment date.');
                return;
            }
            if (!time) {
                alert('Please select a time slot.');
                return;
            }

            document.getElementById('bookingForm').submit();
        }

        // ── Restore state on validation error ──
        window.addEventListener('load', function () {
            const oldDoctor = "{{ old('doctor_id') }}";
            const oldDate   = "{{ old('appointment_date') }}";

            if (oldDoctor) {
                $('#doctorSelect').val(oldDoctor).trigger('change');

                if (oldDate) {
                    // Wait for date cards to render then auto-select the old date
                    setTimeout(() => {
                        const card = document.querySelector(`.date-card[data-date="${oldDate}"]`);
                        if (card) {
                            card.click();
                        }
                    }, 150);
                }
            }
        });
    </script>

</x-app-layout>