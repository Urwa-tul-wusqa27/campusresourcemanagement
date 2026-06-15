<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceAdminController extends Controller
{
    public function index(Request $request): View
    {
        $edit = null;
        if ($request->query('edit')) {
            $edit = Resource::find((int) $request->query('edit'));
        }

        return view('admin.resources', [
            'pageTitle' => 'Manage Resources',
            'resources' => Resource::orderBy('type')->orderBy('name')->get(),
            'edit' => $edit,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $id = (int) $request->input('id', 0);
        $name = trim((string) $request->input('name', ''));
        $type = (string) $request->input('type', '');
        $capacity = (int) $request->input('capacity', 0);
        $features = trim((string) $request->input('features', ''));
        $status = (string) $request->input('status', 'active');

        if (!$name || !in_array($type, ['room', 'lab', 'equipment'], true) || $capacity < 0 || !in_array($status, ['active', 'inactive', 'maintenance'], true)) {
            return back()->with('error', 'Enter valid resource details.')->withInput();
        }

        if ($id > 0) {
            Resource::where('id', $id)->update(compact('name', 'type', 'capacity', 'features', 'status'));
        } else {
            Resource::create(compact('name', 'type', 'capacity', 'features', 'status'));
        }

        return redirect('/admin/resources.php');
    }
}
