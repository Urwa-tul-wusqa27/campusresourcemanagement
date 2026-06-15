@extends('layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>Booking approvals</h1>
        <p class="muted">Approve, reject, and resolve conflicting requests</p>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>Event</th>
            <th>User</th>
            <th>Resource</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td>
                    <strong>{{ $booking->event_name }}</strong>
                    <span class="muted block">{{ $booking->purpose }}</span>
                </td>
                <td>{{ $booking->user->name }}</td>
                <td>{{ $booking->resource->name }}</td>
                <td>{{ date('M j, Y g:i A', strtotime($booking->start_time)) }} - {{ date('g:i A', strtotime($booking->end_time)) }}</td>
                <td><span class="status {{ $booking->status }}">{{ $booking->status }}</span></td>
                <td>
                    @if ($booking->status === 'pending')
                        <form method="post" class="inline-actions">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ (int) $booking->id }}">
                            <button name="action" value="approved">Approve</button>
                            <button name="action" value="declined" class="danger">Decline</button>
                        </form>
                    @else
                        <span class="muted">No action</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
