<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\CampusBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConflictController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $resourceId = (int) $request->query('resource_id', 0);
        $startTime = str_replace('T', ' ', (string) $request->query('start_time', '')).':00';
        $endTime = str_replace('T', ' ', (string) $request->query('end_time', '')).':00';

        if (!$resourceId || strtotime($startTime) === false || strtotime($endTime) === false || strtotime($endTime) <= strtotime($startTime)) {
            return response()->json(['error' => 'Invalid request'], 422);
        }

        $conflict = CampusBooking::hasConflict($resourceId, $startTime, $endTime);

        return response()->json([
            'conflict' => $conflict,
            'suggestions' => $conflict ? CampusBooking::suggestSlots($resourceId, $startTime, $endTime) : [],
        ]);
    }
}
