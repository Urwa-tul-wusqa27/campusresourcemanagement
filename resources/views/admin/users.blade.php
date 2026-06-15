@extends('layouts.app')

@php
    use App\Support\CampusBooking;
@endphp

@section('content')
<div class="page-head">
    <div>
        <h1>Manage users</h1>
        <p class="muted">Create, update, and remove student, faculty, and admin accounts</p>
    </div>
</div>

@if (session('error'))<p class="alert error">{{ session('error') }}</p>@endif

<form method="post" class="form-card wide">
    @csrf
    <input type="hidden" name="id" value="{{ (int) old('id', $edit->id ?? 0) }}">
    <div class="form-row">
        <label>Name
            <input type="text" name="name" value="{{ old('name', $edit->name ?? '') }}" required>
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email', $edit->email ?? '') }}" required>
        </label>
    </div>
    <div class="form-row">
        <label>Role
            <select name="role" required>
                @foreach (['student', 'faculty', 'admin'] as $role)
                    <option value="{{ $role }}" {{ old('role', $edit->role ?? 'student') === $role ? 'selected' : '' }}>{{ CampusBooking::roleLabel($role) }}</option>
                @endforeach
            </select>
        </label>
        <label>{{ $edit ? 'New password (optional)' : 'Password' }}
            <input type="password" name="password" {{ $edit ? '' : 'required' }} minlength="8">
        </label>
    </div>
    <button type="submit" name="action" value="save">{{ $edit ? 'Update user' : 'Add user' }}</button>
</form>

<div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ CampusBooking::roleLabel($user->role) }}</td>
                <td>{{ date('M j, Y', strtotime($user->created_at)) }}</td>
                <td>
                    <form method="post" class="inline-actions">
                        @csrf
                        <a href="/admin/users.php?edit={{ (int) $user->id }}">Edit</a>
                        <input type="hidden" name="id" value="{{ (int) $user->id }}">
                        @if ((int) $user->id !== (int) $admin->id)
                            <button type="submit" name="action" value="delete" class="danger" onclick="return confirm('Delete this user and their bookings?')">Delete</button>
                        @endif
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
