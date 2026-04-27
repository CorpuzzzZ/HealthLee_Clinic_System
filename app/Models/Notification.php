<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'type',
        'status',
    ];

    // ── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ──

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }

    // ── Type label helper ──

    public function typeLabel(): string
    {
        return match($this->type) {
            'appointment_confirmation' => 'Confirmation',
            'appointment_reminder'     => 'Reminder',
            'appointment_cancelled'    => 'Cancellation',
            'appointment_rescheduled'  => 'Rescheduled',
            default                    => 'General',
        };
    }
}