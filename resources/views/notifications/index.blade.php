<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-primary fs-3">Notifications</h5>
                <small class="text-muted">Your alerts and reminders</small>
            </div>
            @if($unreadCount > 0)
            <span class="badge rounded-pill px-3 py-2" style="background: #fdecea; color: #dc3545; font-size: 0.8rem;">
                <i class="bi bi-bell-fill me-1"></i>
                {{ $unreadCount }} unread
            </span>
            @endif
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Notifications List --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">

            @forelse($notifications as $notification)
            @php
            $typeStyles = [
            'appointment_confirmation' => ['bg' => '#e7f1ff', 'color' => '#0d6efd', 'icon' => 'bi-check-circle-fill'],
            'appointment_reminder' => ['bg' => '#fff8e1', 'color' => '#e6a800', 'icon' => 'bi-clock-fill'],
            'appointment_cancelled' => ['bg' => '#fdecea', 'color' => '#dc3545', 'icon' => 'bi-x-circle-fill'],
            'appointment_rescheduled' => ['bg' => '#f3e8ff', 'color' => '#7c3aed', 'icon' => 'bi-arrow-repeat'],
            'general' => ['bg' => '#e8f5ee', 'color' => '#198754', 'icon' => 'bi-info-circle-fill'],
            ];
            $style = $typeStyles[$notification->type] ?? $typeStyles['general'];
            @endphp

            <div class="d-flex align-items-start gap-3 px-4 py-4 border-bottom
                            {{ $notification->status === 'unread' ? '' : 'opacity-75' }}"
                style="{{ $notification->status === 'unread' ? 'background: #fafbff;' : '' }}">

                {{-- Icon --}}
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 42px; height: 42px; background: {{ $style['bg'] }};">
                    <i class="bi {{ $style['icon'] }}" style="color: {{ $style['color'] }}; font-size: 1rem;"></i>
                </div>

                {{-- Content --}}
                <div class="flex-grow-1">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            {{-- Unread dot --}}
                            @if($notification->status === 'unread')
                            <span class="badge rounded-pill me-1 mb-1"
                                style="background: #dc3545; font-size: 0.65rem; padding: 3px 7px;">
                                New
                            </span>
                            @endif

                            {{-- Type label --}}
                            <span class="badge rounded-pill mb-1"
                                style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; font-size: 0.7rem;">
                                {{ $notification->typeLabel() }}
                            </span>

                            <p class="mb-1 small fw-medium text-dark mt-1">
                                {{ $notification->message }}
                            </p>

                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                                &bull; {{ $notification->created_at->format('d M Y, h:i A') }}
                            </small>
                        </div>

                        {{-- Delete --}}
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}"
                            onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1 flex-shrink-0" title="Delete">
                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
                <div class="fw-medium">No notifications yet</div>
                <small>You'll see appointment updates and reminders here.</small>
            </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
        <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
            <small class="text-muted">
                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }}
                of {{ $notifications->total() }} notifications
            </small>
            {{ $notifications->links() }}
        </div>
        @endif

    </div>

</x-app-layout>