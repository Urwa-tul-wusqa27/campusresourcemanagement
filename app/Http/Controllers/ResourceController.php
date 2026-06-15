<?php

namespace App\Http\Controllers;

use App\Support\CampusBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $type = (string) $request->query('type', '');
        $resources = CampusBooking::activeResources(in_array($type, ['room', 'lab', 'equipment'], true) ? $type : null);

        return view('resources.index', [
            'pageTitle' => 'Resources',
            'type' => $type,
            'resources' => $resources,
        ]);
    }
}
