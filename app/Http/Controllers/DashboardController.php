<?php

namespace App\Http\Controllers;

use App\Support\CampusBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $resources = CampusBooking::activeResources();
        $bookings = $user->role === 'admin'
            ? CampusBooking::bookingList()
            : CampusBooking::bookingList($user->id);

        $pendingCount = $bookings->where('status', 'pending')->count();
        $approvedCount = $bookings->where('status', 'approved')->count();
        $upcomingCount = $bookings
            ->filter(fn ($booking) => strtotime((string) $booking->start_time) >= time() && $booking->status !== 'declined')
            ->count();
        $declinedCount = $bookings->where('status', 'declined')->count();
        $nextBooking = $bookings
            ->filter(fn ($booking) => strtotime((string) $booking->start_time) >= time() && $booking->status !== 'declined')
            ->sortBy('start_time')
            ->first();

        return view('dashboard', [
            'pageTitle' => 'Dashboard',
            'user' => $user,
            'resources' => $resources,
            'bookings' => $bookings,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'upcomingCount' => $upcomingCount,
            'declinedCount' => $declinedCount,
            'nextBooking' => $nextBooking,
        ]);
    }
}
