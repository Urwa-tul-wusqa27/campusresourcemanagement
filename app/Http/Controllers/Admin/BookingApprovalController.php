<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\CampusBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingApprovalController extends Controller
{
    public function index(): View
    {
        return view('admin.bookings', [
            'pageTitle' => 'Admin Bookings',
            'bookings' => CampusBooking::bookingList(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $bookingId = (int) $request->input('booking_id', 0);
        $action = (string) $request->input('action', '');
        $booking = Booking::find($bookingId);

        if ($booking && in_array($action, ['approved', 'declined'], true)) {
            if ($action === 'approved' && CampusBooking::hasConflict((int) $booking->resource_id, (string) $booking->start_time, (string) $booking->end_time, (int) $booking->id)) {
                CampusBooking::createNotification((int) $booking->user_id, 'Your booking "'.$booking->event_name.'" could not be approved because of a conflict.');
            } else {
                $booking->update(['status' => $action]);
                CampusBooking::createNotification((int) $booking->user_id, 'Your booking "'.$booking->event_name.'" was '.$action.'.');
            }
        }

        return redirect('/admin/bookings.php');
    }
}
