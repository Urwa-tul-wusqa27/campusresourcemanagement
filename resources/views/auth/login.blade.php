@extends('layouts.app')

@section('content')
<section class="auth-panel">
    <h1>Sign in</h1>
    @if (session('error'))<p class="alert error">{{ session('error') }}</p>@endif
    <form method="post" class="form-card">
        @csrf
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
    </form>
    <p class="muted">Seed users use password <strong>password</strong>.</p>
</section>
@endsection
