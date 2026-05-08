<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Carbon\Carbon;
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

        // Order by most recent dates first (descending)
        $availabilities = $doctor->availabilities()
                                 ->orderBy('available_date', 'desc')
                                 ->orderBy('start_time', 'asc')
                                 ->paginate(10);

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
            'start_time'     => ['required', 'date_format:H:i'],
            'end_time'       => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'end_time.after' => 'End time must be after start time.',
        ]);

        $doctor = $this->getDoctor();
        
        // Removed the 1-hour minimum restriction - doctors can set any duration

        // ── Check for overlapping slots on the same date ──
        $overlap = Availability::where('doctor_id', $doctor->id)
            ->whereDate('available_date', $request->available_date)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time',   '>', $request->start_time);
            })
            ->exists();

        if ($overlap) {
            session()->flash('_add_error', 'This time slot overlaps with an existing slot on the same date. Please choose a different time.');
            return back()->withInput();
        }

        $doctor->availabilities()->create([
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
            'start_time'     => ['required', 'date_format:H:i'],
            'end_time'       => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'end_time.after' => 'End time must be after start time.',
        ]);

        $doctor = $this->getDoctor();
        
        // Removed the 1-hour minimum restriction - doctors can set any duration

        // ── Check for overlapping slots on the same date (exclude current slot) ──
        $overlap = Availability::where('doctor_id', $doctor->id)
            ->whereDate('available_date', $request->available_date)
            ->where('id', '!=', $availability->id)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time',   '>', $request->start_time);
            })
            ->exists();

        if ($overlap) {
            session()->flash('_edit_slot_id', $availability->id);
            session()->flash('_edit_date',    $request->available_date);
            session()->flash('_edit_start',   $request->start_time);
            session()->flash('_edit_end',     $request->end_time);
            session()->flash('_edit_error',   'This time slot overlaps with an existing slot on the same date. Please choose a different time.');
            return back()->withInput();
        }

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