<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Doctor $doctor)
    {
        $doctor->load('availabilities');
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        return view('admin.doctors.availabilities.index', compact('doctor', 'days'));
    }

    public function store(Request $request, Doctor $doctor)
{
    $request->validate([
        'available_date' => ['required', 'date', 'after_or_equal:today'],
        'start_time'     => ['required'],
        'end_time'       => ['required', 'after:start_time'],
    ]);

    $doctor->availabilities()->create([
        'available_date' => $request->available_date,
        'start_time'     => $request->start_time,
        'end_time'       => $request->end_time,
    ]);

    return redirect()->route('admin.doctors.availabilities.index', $doctor)
                     ->with('success', 'Time slot added successfully.');
}

public function update(Request $request, Doctor $doctor, Availability $availability)
{
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

    return redirect()->route('admin.doctors.availabilities.index', $doctor)
                     ->with('success', 'Time slot updated successfully.');
}

    public function destroy(Doctor $doctor, Availability $availability)
    {
        $availability->delete();

        return redirect()->route('admin.doctors.availabilities.index', $doctor)
                         ->with('success', 'Time slot deleted successfully.');
    }
}