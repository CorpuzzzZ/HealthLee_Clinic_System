<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\SearchController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AvailabilityController;
use App\Http\Controllers\Doctor\MedicalRecordController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PatientRecordController as AdminPatientRecordController;
use App\Http\Controllers\Doctor\PatientRecordController as DoctorPatientRecordController;


Route::get('/', function () {
    return view('auth.login');
});

// Dashboard - redirects based on role
Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'doctor'  => redirect()->route('doctor.dashboard'),
        'patient' => redirect()->route('patient.dashboard'),
        default   => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes (Breeze default - kept as is)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin Routes ──────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class)
            ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::resource('patient-records', AdminPatientRecordController::class)
            ->only(['index', 'show', 'edit', 'update'])
            ->parameters(['patient-records' => 'patient']);
    });

// ── Shared Notification Routes (patient + doctor) ─────────
Route::prefix('notifications')
     ->name('notifications.')
     ->middleware(['auth'])
     ->group(function () {
         Route::get('/',                      [NotificationController::class, 'index'])->name('index');
         Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
         Route::delete('/{notification}',     [NotificationController::class, 'destroy'])->name('destroy');
     });

// ── Patient Routes ────────────────────────────────────────
Route::prefix('patient')
    ->name('patient.')
    ->middleware(['auth', 'role:patient'])
    ->group(function () {

        Route::get('/dashboard', [PatientDashboardController::class, 'index'])
            ->name('dashboard');

        // Doctor Search
        Route::get('/doctors',          [SearchController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}', [SearchController::class, 'show'])->name('doctors.show');

        // Appointments — static/action routes MUST come before {appointment} wildcard
        Route::get('/appointments',               [PatientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create',        [PatientAppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/appointments/slots',         [PatientAppointmentController::class, 'slots'])->name('appointments.slots'); // ← moved up
        Route::post('/appointments',              [PatientAppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
        
    });

// ── Doctor Routes ─────────────────────────────────────────
Route::prefix('doctor')
    ->name('doctor.')
    ->middleware(['auth', 'role:doctor'])
    ->group(function () {

        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])
            ->name('dashboard');

        // Appointments
        Route::get('/appointments',                        [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}',          [DoctorAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.status');

        // Medical Records
        Route::resource('medical-records', MedicalRecordController::class)
             ->only(['index', 'show', 'create', 'store', 'edit', 'update']);

        // Availability
        Route::prefix('availabilities')
             ->name('availabilities.')
             ->group(function () {
                 Route::get('/',                  [AvailabilityController::class, 'index'])->name('index');
                 Route::post('/',                 [AvailabilityController::class, 'store'])->name('store');
                 Route::put('/{availability}',    [AvailabilityController::class, 'update'])->name('update');
                 Route::delete('/{availability}', [AvailabilityController::class, 'destroy'])->name('destroy');
             });

        Route::resource('patient-records', DoctorPatientRecordController::class)
             ->only(['index', 'show', 'edit', 'update']);
    });


require __DIR__.'/auth.php';