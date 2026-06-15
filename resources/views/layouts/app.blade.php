@php
    $authUser = auth()->user();
    $currentPath = request()->path();
    $isActive = static fn (string $path): string => $currentPath === ltrim($path, '/') ? ' class="active"' : '';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'Campus Booking' }}</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="/dashboard.php" aria-label="Campus Booking home">
        <span class="brand-mark">CB</span>
        <span>
            <strong>Campus Booking</strong>
            <small>Resource scheduling</small>
        </span>
    </a>
    <nav class="main-nav" aria-label="Primary navigation">
        @if ($authUser)
            <a{!! $isActive('/dashboard.php') !!} href="/dashboard.php">Dashboard</a>
            <a{!! $isActive('/resources.php') !!} href="/resources.php">Resources</a>
            <a{!! $isActive('/booking_create.php') !!} href="/booking_create.php">New Booking</a>
            @if ($authUser->role === 'admin')
                <a{!! $isActive('/admin/bookings.php') !!} href="/admin/bookings.php">Approvals</a>
                <a{!! $isActive('/admin/users.php') !!} href="/admin/users.php">Users</a>
                <a{!! $isActive('/admin/resources.php') !!} href="/admin/resources.php">Inventory</a>
            @endif
            <a href="/logout.php">Logout</a>
        @else
            <a{!! $isActive('/login.php') !!} href="/login.php">Login</a>
            <a{!! $isActive('/register.php') !!} href="/register.php">Register</a>
        @endif
    </nav>
</header>
<main class="container">
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
