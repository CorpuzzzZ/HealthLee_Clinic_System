<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Doctor Profile</h5>
                <small class="text-muted">
                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                </small>
            </div>
            <a href="{{ route('patient.doctors.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Search
            </a>
        </div>
    </x-slot>

    <div class="row g-4">

        {{-- Doctor Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4 text-center">

                    {{-- Avatar --}}
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 1.5rem; background: #0dcaf0;">
                        {{ strtoupper(substr($doctor->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->last_name, 0,
                        1)) }}
                    </div>

                    <h5 class="fw-bold mb-1">
                        Dr. {{ $doctor->first_name }}
                        {{ $doctor->middle_name ? $doctor->middle_name . ' ' : '' }}
                        {{ $doctor->last_name }}
                    </h5>

                    <span class="badge rounded-pill px-3 py-2 mb-3 d-inline-block"
                        style="background: #e0f7fc; color: #0097a7;">
                        <i class="bi bi-heart-pulse me-1"></i>
                        {{ $doctor->specialty ?? 'General' }}
                    </span>

                    <hr class="my-3">

                    {{-- Details --}}
                    <div class="text-start">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Gender</small>
                                    <span class="fw-medium small">{{ ucfirst($doctor->gender ?? '—') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Contact</small>
                                    {{-- Normalized relationship with flat fallback --}}
                                    <span class="fw-medium small">
                                        {{ $doctor->contact->contact_number
                                        ?? $doctor->contact_number
                                        ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Address</small>
                                    {{-- Normalized relationship with flat fallback --}}
                                    <span class="fw-medium small">
                                        @php
                                        $addr = $doctor->address;
                                        $addressStr = collect([
                                        $addr?->barangay ?? $doctor->barangay,
                                        $addr?->city ?? $doctor->city,
                                        $addr?->province ?? $doctor->province,
                                        ])->filter()->implode(', ');
                                        @endphp
                                        {{ $addressStr ?: '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Available Slots Count --}}
                    @php
                    $totalAvailable = $availabilities->sum(function ($slot) use ($bookedTimes) {
                    $start = \Carbon\Carbon::createFromFormat('H:i:s',
                    \Carbon\Carbon::parse($slot->start_time)->format('H:i:s'));
                    $end = \Carbon\Carbon::createFromFormat('H:i:s',
                    \Carbon\Carbon::parse($slot->end_time)->format('H:i:s'));
                    $date = \Carbon\Carbon::parse($slot->available_date)->format('Y-m-d');
                    $count = 0;
                    while ($start->copy()->addHour()->lte($end)) {
                    $key = $date . '_' . $start->format('H:i');
                    if (!in_array($key, $bookedTimes)) $count++;
                    $start->addHour();
                    }
                    return $count;
                    });
                    @endphp
                    <div class="mt-3 p-3 rounded-3 text-center" style="background: #e8f5ee; border: 1px solid #c3e6cb;">
                        <div class="fw-bold fs-4" style="color: #198754;">
                            {{ $totalAvailable }}
                        </div>
                        <small class="text-muted">Available 1-hour Slots</small>
                    </div>

                </div>
            </div>
        </div>

        {{-- Availability Slots --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Available Time Slots</h6>
                    <small class="text-muted">Select an available slot to book an appointment</small>
                </div>
                <div class="card-body px-4 pb-4 pt-3">

                    @if($availabilities->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                        <div class="fw-medium">No available slots at the moment</div>
                        <small>Please check back later or try another doctor.</small>
                    </div>
                    @else

                    {{-- Group by date --}}
                    @foreach($availabilities->groupBy(fn($slot) =>
                    \Carbon\Carbon::parse($slot->available_date)->format('Y-m-d')) as $date => $slots)
                    <div class="mb-4">

                        {{-- Date Header --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-3 px-3 py-2" style="background: #e7f1ff;">
                                <span class="fw-semibold small" style="color: #0d6efd;">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                                </span>
                            </div>
                            @if(\Carbon\Carbon::parse($date)->isToday())
                            <span class="badge rounded-pill"
                                style="background: #fff8e1; color: #e6a800; font-size: 0.7rem;">
                                Today
                            </span>
                            @endif
                        </div>

                        {{-- 1-hour slot cards generated from availability window --}}
                        <div class="row g-2">
                            @foreach($slots as $slot)
                            @php
                            $start = \Carbon\Carbon::createFromFormat('H:i:s',
                            \Carbon\Carbon::parse($slot->start_time)->format('H:i:s'));
                            $end = \Carbon\Carbon::createFromFormat('H:i:s',
                            \Carbon\Carbon::parse($slot->end_time)->format('H:i:s'));
                            @endphp

                            @while($start->copy()->addHour()->lte($end))
                            @php
                            $slotStart = $start->format('H:i');
                            $slotEnd = $start->copy()->addHour()->format('H:i');
                            $key = $date . '_' . $slotStart;
                            $isBooked = in_array($key, $bookedTimes);
                            @endphp

                            <div class="col-sm-6 col-lg-4">
                                <div class="p-3 rounded-3 border h-100" style="border-color: {{ $isBooked ? '#f5c6cb' : '#c3e6cb' }} !important;
                                                           background: {{ $isBooked ? '#fdecea' : '#f0fdfc' }};">

                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-clock"
                                            style="color: {{ $isBooked ? '#dc3545' : '#0dcaf0' }};"></i>
                                        <span class="fw-medium small">
                                            {{ \Carbon\Carbon::createFromFormat('H:i', $slotStart)->format('h:i A') }}
                                            —
                                            {{ \Carbon\Carbon::createFromFormat('H:i', $slotEnd)->format('h:i A') }}
                                        </span>
                                    </div>

                                    <small class="d-block mb-3">
                                        @if($isBooked)
                                        <span style="color: #dc3545;">
                                            <i class="bi bi-x-circle-fill me-1"></i> Booked
                                        </span>
                                        @else
                                        <span style="color: #198754;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Available
                                        </span>
                                        @endif
                                    </small>

                                    @if($isBooked)
                                    <button class="btn btn-sm w-100 rounded-3 disabled" style="background: #f5c6cb; color: #dc3545;
                                                                       font-size: 0.75rem; cursor: not-allowed;">
                                        <i class="bi bi-calendar-x me-1"></i> Unavailable
                                    </button>
                                    @else
                                    <a href="{{ route('patient.appointments.create', [
                                                               'doctor_id'        => $doctor->id,
                                                               'appointment_date' => $date,
                                                               'appointment_time' => $slotStart,
                                                           ]) }}" class="btn btn-sm w-100 rounded-3"
                                        style="background: #0dcaf0; color: white; font-size: 0.75rem;">
                                        <i class="bi bi-calendar-plus me-1"></i> Book Slot
                                    </a>
                                    @endif

                                </div>
                            </div>

                            @php $start->addHour(); @endphp
                            @endwhile
                            @endforeach
                        </div>

                    </div>
                    @endforeach

                    @endif

                </div>
            </div>
        </div>

    </div>

</x-app-layout>