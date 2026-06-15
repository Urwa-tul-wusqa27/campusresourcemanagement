@extends('layouts.app')

@section('content')
<section class="auth-panel">
    <h1>Create account</h1>
    @if (session('error'))<p class="alert error">{{ session('error') }}</p>@endif
    <form method="post" class="form-card">
        @csrf
        <label>Name
            <input type="text" name="name" value="{{ old('name') }}" required>
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <label>Role
            <select name="role">
                <option value="student" @selected(old('role') === 'student')>Student</option>
                <option value="faculty" @selected(old('role') === 'faculty')>Faculty/Staff</option>
            </select>
        </label>
        <label>Password
            <input type="password" name="password" minlength="8" required>
        </label>
        <button type="submit">Register</button>
    </form>
</section>
@endsection
