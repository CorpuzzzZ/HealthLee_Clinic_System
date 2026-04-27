<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    private function getDoctor()
    {
        return Auth::user()->doctor;
    }

    public function index()
    {
        $doctor = $this->getDoctor();

        $availabilities = $doctor->availabilities()
                                 ->orderBy('available_date')
                                 ->orderBy('start_time')
                                 ->paginate(10);

        // Stat counts
        $upcomingCount = $doctor->availabilities()
                                ->whereDate('available_date', '>', today())
                                ->count();

        $todayCount    = $doctor->availabilities()
                                ->whereDate('available_date', today())
                                ->count();

        $pastCount     = $doctor->availabilities()
                                ->whereDate('available_date', '<', today())
                                ->count();

        return view('doctor.availabilities.index', compact(
            'doctor',
            'availabilities',
            'upcomingCount',
            'todayCount',
            'pastCount',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'available_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'     => ['required'],
            'end_time'       => ['required', 'after:start_time'],
        ]);

        $this->getDoctor()->availabilities()->create([
            'available_date' => $request->available_date,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
        ]);

        return redirect()->route('doctor.availabilities.index')
                         ->with('success', 'Time slot added successfully.');
    }

    public function update(Request $request, Availability $availability)
    {
        abort_if($availability->doctor_id !== $this->getDoctor()->id, 403);

        $request->validate([
            'available_date' => ['required', 'date'],
            'start_time'     => ['required'],
            'end_time'       => ['required', 'after:start_time'],
        ]);

        $availability->update([
            'available_date' => $request->available_date,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
        ]);

        return redirect()->route('doctor.availabilities.index')
                         ->with('success', 'Time slot updated successfully.');
    }

    public function destroy(Availability $availability)
    {
        abort_if($availability->doctor_id !== $this->getDoctor()->id, 403);

        $availability->delete();

        return redirect()->route('doctor.availabilities.index')
                         ->with('success', 'Time slot deleted successfully.');
    }
}