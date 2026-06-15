@extends('layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>Resources</h1>
        <p class="muted">Rooms, labs, equipment, capacities, and features</p>
    </div>
    <a class="button" href="/booking_create.php">Book resource</a>
</div>

<form class="toolbar" method="get">
    <label>Filter by type
        <select name="type" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="room" {{ $type === 'room' ? 'selected' : '' }}>Room</option>
            <option value="lab" {{ $type === 'lab' ? 'selected' : '' }}>Lab</option>
            <option value="equipment" {{ $type === 'equipment' ? 'selected' : '' }}>Equipment</option>
        </select>
    </label>
</form>

<div class="resource-grid">
    @foreach ($resources as $resource)
        <article class="resource-card">
            <div>
                <h2>{{ $resource->name }}</h2>
                <p class="muted">
                    <span class="type-pill type-{{ $resource->type }}">{{ ucfirst($resource->type) }}</span>
                    Capacity {{ (int) $resource->capacity }}
                </p>
            </div>
            <p>{{ $resource->features }}</p>
            <a class="button secondary" href="/booking_create.php?resource_id={{ (int) $resource->id }}">Book</a>
        </article>
    @endforeach
</div>
@endsection
