<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Appointment;

class NotificationService
{
    public function appointmentConfirmation(Appointment $appointment): void
    {
        // Notify patient
        Notification::create([
            'user_id' => $appointment->patient->user_id,
            'message' => "Your appointment with Dr. {$appointment->doctor->first_name} {$appointment->doctor->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') .
                         " has been confirmed.",
            'type'   => 'appointment_confirmation',
            'status' => 'unread',
        ]);

        // Notify doctor
        Notification::create([
            'user_id' => $appointment->doctor->user_id,
            'message' => "New appointment booked with patient {$appointment->patient->first_name} {$appointment->patient->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') . ".",
            'type'   => 'appointment_confirmation',
            'status' => 'unread',
        ]);
    }

    public function appointmentCancelled(Appointment $appointment): void
    {
        // Notify patient
        Notification::create([
            'user_id' => $appointment->patient->user_id,
            'message' => "Your appointment with Dr. {$appointment->doctor->first_name} {$appointment->doctor->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " has been cancelled.",
            'type'   => 'appointment_cancelled',
            'status' => 'unread',
        ]);

        // Notify doctor
        Notification::create([
            'user_id' => $appointment->doctor->user_id,
            'message' => "Appointment with patient {$appointment->patient->first_name} {$appointment->patient->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " has been cancelled.",
            'type'   => 'appointment_cancelled',
            'status' => 'unread',
        ]);
    }

    public function appointmentRescheduled(Appointment $appointment): void
    {
        // Notify patient
        Notification::create([
            'user_id' => $appointment->patient->user_id,
            'message' => "Your appointment with Dr. {$appointment->doctor->first_name} {$appointment->doctor->last_name} has been rescheduled to " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') . ".",
            'type'   => 'appointment_rescheduled',
            'status' => 'unread',
        ]);

        // Notify doctor
        Notification::create([
            'user_id' => $appointment->doctor->user_id,
            'message' => "Appointment with patient {$appointment->patient->first_name} {$appointment->patient->last_name} has been rescheduled to " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') . ".",
            'type'   => 'appointment_rescheduled',
            'status' => 'unread',
        ]);
    }
}