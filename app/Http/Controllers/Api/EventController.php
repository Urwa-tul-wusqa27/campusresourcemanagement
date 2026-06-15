<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $resourceId = (int) $request->query('resource_id', 0);
        $type = (string) $request->query('type', '');
        $rangeStart = (string) $request->query('start', date('Y-m-d'));
        $rangeEnd = (string) $request->query('end', date('Y-m-d', strtotime('+30 days')));

        $bookings = Booking::query()
            ->with(['resource', 'user'])
            ->when($user->role !== 'admin', fn (Builder $query) => $query->where(fn (Builder $query) => $query->where('status', 'approved')->orWhere('user_id', $user->id)))
            ->when($resourceId > 0, fn (Builder $query) => $query->where('resource_id', $resourceId))
            ->when(in_array($type, ['room', 'lab', 'equipment'], true), fn (Builder $query) => $query->whereHas('resource', fn (Builder $query) => $query->where('type', $type)))
            ->get();

        $events = $bookings->map(function (Booking $booking): array {
            $color = match ($booking->status) {
                'approved' => '#d92d20',
                'pending' => '#fdb022',
                'declined' => '#667085',
                default => '#12b76a',
            };

            return [
                'id' => (string) $booking->id,
                'title' => $booking->resource->name.': '.$booking->event_name,
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $booking->status,
                    'resource' => $booking->resource->name,
                    'booked_by' => $booking->user->name,
                ],
            ];
        })->all();

        $cursor = strtotime(date('Y-m-d 08:00:00', strtotime($rangeStart)));
        $rangeLimit = strtotime(date('Y-m-d 00:00:00', strtotime($rangeEnd)));

        while ($cursor !== false && $rangeLimit !== false && $cursor < $rangeLimit) {
            $events[] = [
                'title' => 'Free slot',
                'start' => date('Y-m-d 08:00:00', $cursor),
                'end' => date('Y-m-d 16:00:00', $cursor),
                'display' => 'background',
                'backgroundColor' => '#d1fadf',
            ];
            $cursor = strtotime('+1 day', $cursor);
        }

        return response()->json($events);
    }
}
