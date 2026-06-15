@extends('layouts.app')

@php
    use App\Support\CampusBooking;
@endphp

@section('content')
<section class="dashboard-hero">
    <div>
        <span class="section-kicker">Campus control center</span>
        <h1>Dashboard</h1>
        <p>{{ CampusBooking::roleLabel($user->role) }} schedule, booking status, and live resource availability.</p>
        <div class="hero-meta">
            <span>{{ $user->name }}</span>
            <span>{{ date('l, M j') }}</span>
            <span>8:00 AM - 4:00 PM</span>
        </div>
    </div>
    <div class="hero-actions">
        <a class="button" href="/booking_create.php">Create booking</a>
        <a class="button secondary" href="/resources.php">View resources</a>
    </div>
</section>

<section class="stats-grid" aria-label="Booking overview">
    <article class="stat-card accent-approved">
        <span>Approved</span>
        <strong>{{ $approvedCount }}</strong>
        <small>confirmed bookings</small>
    </article>
    <article class="stat-card accent-pending">
        <span>Pending</span>
        <strong>{{ $pendingCount }}</strong>
        <small>awaiting review</small>
    </article>
    <article class="stat-card accent-upcoming">
        <span>Upcoming</span>
        <strong>{{ $upcomingCount }}</strong>
        <small>active schedule items</small>
    </article>
    <article class="stat-card accent-resource">
        <span>Resources</span>
        <strong>{{ count($resources) }}</strong>
        <small>available to book</small>
    </article>
</section>

<section class="dashboard-insights">
    <article class="insight-card">
        <span class="section-kicker">Next booking</span>
        @if ($nextBooking)
            <h2>{{ $nextBooking->event_name }}</h2>
            <p class="muted">{{ $nextBooking->resource->name }} at {{ date('M j, g:i A', strtotime($nextBooking->start_time)) }}</p>
        @else
            <h2>No upcoming booking</h2>
            <p class="muted">Create a new request to reserve a campus resource.</p>
        @endif
    </article>
    <article class="insight-card">
        <span class="section-kicker">Request health</span>
        <h2>{{ $pendingCount }} pending review</h2>
        <p class="muted">{{ $declinedCount }} declined request{{ $declinedCount === 1 ? '' : 's' }} in this view.</p>
    </article>
</section>

<section class="workspace-panel">
    <div class="panel-head">
        <div>
            <span class="section-kicker">Live schedule</span>
            <h2>Resource calendar</h2>
        </div>
        <div class="status-legend" aria-label="Calendar legend">
            <span><i class="legend-approved"></i> Approved</span>
            <span><i class="legend-pending"></i> Pending</span>
            <span><i class="legend-declined"></i> Declined</span>
        </div>
    </div>

    <section class="toolbar dashboard-toolbar">
        <label>Resource type
            <select id="resourceTypeFilter">
                <option value="">All</option>
                <option value="room">Rooms</option>
                <option value="lab">Labs</option>
                <option value="equipment">Equipment</option>
            </select>
        </label>
        <label>Resource
            <select id="resourceFilter">
                <option value="">All resources</option>
                @foreach ($resources as $resource)
                    <option value="{{ (int) $resource->id }}" data-type="{{ $resource->type }}">
                        {{ $resource->name }}
                    </option>
                @endforeach
            </select>
        </label>
    </section>

    <div class="calendar-shell">
        <div id="calendar"></div>
    </div>
</section>

<section class="workspace-panel">
    <div class="panel-head">
        <div>
            <span class="section-kicker">Request log</span>
            <h2>{{ $user->role === 'admin' ? 'All bookings' : 'My bookings' }}</h2>
        </div>
        <span class="table-count">{{ count($bookings) }} record{{ count($bookings) === 1 ? '' : 's' }}</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Event</th>
                <th>Resource</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>
                        <strong>{{ $booking->event_name }}</strong>
                        @if (!empty($booking->purpose))
                            <span class="muted block">{{ $booking->purpose }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $booking->resource->name }}
                        @if (!empty($booking->resource->type))
                            <span class="type-pill type-{{ $booking->resource->type }}">{{ ucfirst($booking->resource->type) }}</span>
                        @endif
                    </td>
                    <td>{{ date('M j, Y g:i A', strtotime($booking->start_time)) }} - {{ date('g:i A', strtotime($booking->end_time)) }}</td>
                    <td><span class="status {{ $booking->status }}">{{ $booking->status }}</span></td>
                </tr>
            @endforeach
            @if (!$bookings->count())
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <strong>No bookings yet</strong>
                            <span>Create your first booking request to populate this dashboard.</span>
                        </div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</section>
@endsection
