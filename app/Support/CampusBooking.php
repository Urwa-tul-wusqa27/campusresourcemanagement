<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CampusBooking
{
    public static function roleLabel(string $role): string
    {
        return match ($role) {
            'student' => 'Student',
            'faculty' => 'Faculty/Staff',
            'admin' => 'Admin',
            default => ucfirst($role),
        };
    }

    public static function activeResources(?string $type = null): Collection
    {
        return Resource::query()
            ->where('status', 'active')
            ->when($type, fn (Builder $query) => $query->where('type', $type))
            ->when($type, fn (Builder $query) => $query->orderBy('name'))
            ->when(!$type, fn (Builder $query) => $query->orderBy('type')->orderBy('name'))
            ->get();
    }

    public static function hasConflict(int $resourceId, string $startTime, string $endTime, ?int $ignoreBookingId = null): bool
    {
        return Booking::query()
            ->where('resource_id', $resourceId)
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->when($ignoreBookingId, fn (Builder $query) => $query->where('id', '<>', $ignoreBookingId))
            ->exists();
    }

    public static function suggestSlots(int $resourceId, string $startTime, string $endTime, int $limit = 3): array
    {
        $duration = strtotime($endTime) - strtotime($startTime);
        $candidateStart = strtotime($endTime);
        $suggestions = [];

        for ($i = 0; $i < 48 && count($suggestions) < $limit; $i++) {
            $candidateEnd = $candidateStart + $duration;
            $start = date('Y-m-d H:i:s', $candidateStart);
            $end = date('Y-m-d H:i:s', $candidateEnd);

            if (!self::hasConflict($resourceId, $start, $end)) {
                $suggestions[] = ['start_time' => $start, 'end_time' => $end];
            }

            $candidateStart += 30 * 60;
        }

        return $suggestions;
    }

    public static function createNotification(int $userId, string $message): void
    {
        Notification::create([
            'user_id' => $userId,
            'message' => $message,
        ]);
    }

    public static function bookingList(?int $userId = null): Collection
    {
        return Booking::query()
            ->with(['user', 'resource'])
            ->when($userId, fn (Builder $query) => $query->where('user_id', $userId))
            ->orderByDesc('start_time')
            ->get();
    }
}
