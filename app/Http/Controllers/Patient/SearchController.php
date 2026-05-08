<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Availability;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['availabilities', 'user.contact', 'user.address'])
                       ->orderBy('last_name');

        // Search by name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%');
            });
        }

        // Filter by specialty
        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        // Filter by availability date
        if ($request->filled('date')) {
            $query->whereHas('availabilities', function ($q) use ($request) {
                $q->whereDate('available_date', $request->date);
            });
        }

        $doctors    = $query->paginate(9)->withQueryString();
        $specialties = Doctor::select('specialty')
                             ->distinct()
                             ->orderBy('specialty')
                             ->pluck('specialty');

        return view('patient.doctors.index', compact('doctors', 'specialties'));
    }

    public function show(Doctor $doctor)
    {
        // FIXED: Load user.contact and user.address instead of contact and address
        $doctor->load(['availabilities', 'user.contact', 'user.address']);

        // Only future availability slots
        $availabilities = $doctor->availabilities()
                             ->whereDate('available_date', '>=', today())
                             ->orderBy('available_date')
                             ->orderBy('start_time')
                             ->get();

        // Build booked times as "date_HH:mm" keys for easy lookup in the view
        $bookedTimes = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', '>=', today())
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->map(fn($a) => \Carbon\Carbon::parse($a->appointment_date)->format('Y-m-d')
                          . '_'
                          . \Carbon\Carbon::parse($a->appointment_time)->format('H:i'))
            ->toArray();

        return view('patient.doctors.show', compact('doctor', 'availabilities', 'bookedTimes'));
    }
}