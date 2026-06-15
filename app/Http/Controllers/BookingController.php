<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\CampusBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $resources = CampusBooking::activeResources();

        return view('bookings.create', [
            'pageTitle' => 'New Booking',
            'user' => $request->user(),
            'resources' => $resources,
            'selectedResource' => (int) $request->query('resource_id', 0),
            'resourceCount' => $resources->count(),
            'roomCount' => $resources->where('type', 'room')->count(),
            'labCount' => $resources->where('type', 'lab')->count(),
            'equipmentCount' => $resources->where('type', 'equipment')->count(),
            'suggestions' => session('suggestions', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $resourceId = (int) $request->input('resource_id', 0);
        $eventName = trim((string) $request->input('event_name', ''));
        $participants = (int) $request->input('participants', 1);
        $purpose = trim((string) $request->input('purpose', ''));
        $startTime = str_replace('T', ' ', (string) $request->input('start_time', '')).':00';
        $endTime = str_replace('T', ' ', (string) $request->input('end_time', '')).':00';

        if (!$resourceId || !$eventName || !$purpose || strtotime($startTime) === false || strtotime($endTime) === false || strtotime($endTime) <= strtotime($startTime)) {
            return back()->with('error', 'Complete all booking details with a valid time range.')->withInput();
        }

        if (CampusBooking::hasConflict($resourceId, $startTime, $endTime)) {
            return back()
                ->with('error', 'This resource is already booked or pending approval for that time.')
                ->with('suggestions', CampusBooking::suggestSlots($resourceId, $startTime, $endTime))
                ->withInput();
        }

        Booking::create([
            'user_id' => $request->user()->id,
            'resource_id' => $resourceId,
            'event_name' => $eventName,
            'participants' => $participants,
            'purpose' => $purpose,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
        ]);

        CampusBooking::createNotification($request->user()->id, 'Your booking "'.$eventName.'" is pending approval.');

        return back()->with('success', 'Booking submitted for admin approval.');
    }
}
