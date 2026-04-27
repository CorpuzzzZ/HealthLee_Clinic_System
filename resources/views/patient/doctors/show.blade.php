<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Doctor Profile</h5>
                <small class="text-muted">
                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                </small>
            </div>
            <a href="{{ route('patient.doctors.index') }}"
               class="btn btn-outline-secondary btn-sm rounded-pill px-3">
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
                        {{ strtoupper(substr($doctor->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->last_name, 0, 1)) }}
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
                                    <small class="text-muted d-block mb-1">Age</small>
                                    <span class="fw-medium small">{{ $doctor->age ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Contact</small>
                                    <span class="fw-medium small">{{ $doctor->contact_number ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Address</small>
                                    <span class="fw-medium small">
                                        {{ collect([
                                            $doctor->barangay,
                                            $doctor->city,
                                            $doctor->province
                                        ])->filter()->implode(', ') ?: '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Available Slots Count --}}
                    <div class="mt-3 p-3 rounded-3 text-center"
                         style="background: #e8f5ee; border: 1px solid #c3e6cb;">
                        <div class="fw-bold fs-4" style="color: #198754;">
                            {{ $availabilities->count() }}
                        </div>
                        <small class="text-muted">Upcoming Available Slots</small>
                    </div>

                </div>
            </div>
        </div>

        {{-- Availability Slots --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Available Time Slots</h6>
                    <small class="text-muted">
                        Select a slot to book an appointment
                    </small>
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
                        @foreach($availabilities->groupBy(fn($slot) => $slot->available_date->format('Y-m-d')) as $date => $slots)
                            <div class="mb-4">

                                {{-- Date Header --}}
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="rounded-3 px-3 py-2"
                                         style="background: #e7f1ff;">
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

                                {{-- Time Slots --}}
                                <div class="row g-2">
                                    @foreach($slots as $slot)
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="p-3 rounded-3 border h-100"
                                                 style="border-color: #e0f7fc !important; background: #f0fdfc;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <i class="bi bi-clock" style="color: #0dcaf0;"></i>
                                                    <span class="fw-medium small">
                                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                        —
                                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                    </span>
                                                </div>

                                                @php
                                                    $start    = \Carbon\Carbon::parse($slot->start_time);
                                                    $end      = \Carbon\Carbon::parse($slot->end_time);
                                                    $duration = $start->diffInMinutes($end);
                                                    $hours    = intdiv($duration, 60);
                                                    $mins     = $duration % 60;
                                                    $label    = ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                                                @endphp

                                                <small class="text-muted d-block mb-3">
                                                    <i class="bi bi-hourglass-split me-1"></i> {{ $label }}
                                                </small>

                                                {{-- Book button - links to patient appointments (TODO) --}}
                                                <a href="{{ route('patient.appointments.create', [
               'doctor_id'        => $doctor->id,
               'appointment_date' => $slot->available_date->format('Y-m-d'),
               'appointment_time' => \Carbon\Carbon::parse($slot->start_time)->format('H:i'),
           ]) }}"
   class="btn btn-sm w-100 rounded-3"
   style="background: #0dcaf0; color: white; font-size: 0.75rem;">
    <i class="bi bi-calendar-plus me-1"></i> Book Slot
</a>
                                            </div>
                                        </div>
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