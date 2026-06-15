<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\CampusBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function index(Request $request): View
    {
        $edit = null;
        if ($request->query('edit')) {
            $edit = User::select('id', 'name', 'email', 'role')->find((int) $request->query('edit'));
        }

        return view('admin.users', [
            'pageTitle' => 'Manage Users',
            'admin' => $request->user(),
            'users' => User::select('id', 'name', 'email', 'role', 'created_at')->orderBy('role')->orderBy('name')->get(),
            'edit' => $edit,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $action = (string) $request->input('action', 'save');
        $id = (int) $request->input('id', 0);

        if ($action === 'delete' && $id > 0 && $id !== (int) $request->user()->id) {
            User::where('id', $id)->delete();

            return redirect('/admin/users.php');
        }

        $name = trim((string) $request->input('name', ''));
        $email = trim((string) $request->input('email', ''));
        $role = (string) $request->input('role', '');
        $password = (string) $request->input('password', '');

        if (!$name || !$email || !in_array($role, ['student', 'faculty', 'admin'], true)) {
            return back()->with('error', 'Enter a valid name, email, and role.')->withInput();
        }

        try {
            if ($id > 0) {
                $user = User::findOrFail($id);
                $payload = compact('name', 'email', 'role');

                if ($password !== '') {
                    if (strlen($password) < 8) {
                        return back()->with('error', 'New password must be at least 8 characters.')->withInput();
                    }

                    $payload['password'] = Hash::make($password);
                }

                $user->update($payload);
            } else {
                if (strlen($password) < 8) {
                    return back()->with('error', 'Password must be at least 8 characters.')->withInput();
                }

                User::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'password' => Hash::make($password),
                ]);
            }
        } catch (\Throwable) {
            return back()->with('error', 'That email is already in use.')->withInput();
        }

        return redirect('/admin/users.php');
    }
}
