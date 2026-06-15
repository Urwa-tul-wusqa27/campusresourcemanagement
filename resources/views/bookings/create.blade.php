@extends('layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>New booking</h1>
        <p class="muted">Create a polished campus request with live availability checks</p>
    </div>
</div>

@if (session('error'))<p class="alert error">{{ session('error') }}</p>@endif
@if (session('success'))<p class="alert success">{{ session('success') }}</p>@endif

@if ($suggestions)
    <section class="suggestions">
        <h2>Next available slots</h2>
        @foreach ($suggestions as $slot)
            <button type="button" class="slot-button" data-start="{{ date('Y-m-d\TH:i', strtotime($slot['start_time'])) }}" data-end="{{ date('Y-m-d\TH:i', strtotime($slot['end_time'])) }}">
                {{ date('M j, g:i A', strtotime($slot['start_time'])) }} - {{ date('g:i A', strtotime($slot['end_time'])) }}
            </button>
        @endforeach
    </section>
@endif

<section class="booking-studio">
    <form method="post" class="form-card booking-card" id="bookingForm">
        @csrf
        <div class="form-section-head">
            <span class="section-kicker">Request details</span>
            <h2>Reserve a space or resource</h2>
        </div>

        <label>Resource
            <select name="resource_id" id="bookingResource" required>
                <option value="">Select resource</option>
                @foreach ($resources as $resource)
                    <option
                        value="{{ (int) $resource->id }}"
                        data-name="{{ $resource->name }}"
                        data-type="{{ $resource->type }}"
                        data-capacity="{{ (int) $resource->capacity }}"
                        data-features="{{ $resource->features }}"
                        {{ (int) old('resource_id', $selectedResource) === (int) $resource->id ? 'selected' : '' }}
                    >
                        {{ $resource->name }} ({{ $resource->type }}, cap {{ (int) $resource->capacity }})
                    </option>
                @endforeach
            </select>
        </label>

        <div class="form-row">
            <label>Start time
                <input type="datetime-local" name="start_time" id="startTime" value="{{ old('start_time') }}" required>
            </label>
            <label>End time
                <input type="datetime-local" name="end_time" id="endTime" value="{{ old('end_time') }}" required>
            </label>
        </div>

        <div class="quick-duration" aria-label="Quick duration">
            <button type="button" class="duration-chip" data-minutes="30">30 min</button>
            <button type="button" class="duration-chip" data-minutes="60">1 hour</button>
            <button type="button" class="duration-chip" data-minutes="90">1.5 hours</button>
            <button type="button" class="duration-chip" data-minutes="120">2 hours</button>
        </div>

        <div id="conflictResult" class="inline-message availability-message">Choose a resource and time to check availability.</div>

        <label>Event name
            <input type="text" name="event_name" id="eventName" value="{{ old('event_name') }}" placeholder="Example: Final project review" required>
        </label>

        <div class="form-row">
            <label>Participants
                <input type="number" name="participants" id="participants" min="1" value="{{ old('participants', 1) }}" required>
            </label>
            <label>Request owner
                <input type="text" value="{{ $user->name }}" readonly>
            </label>
        </div>
        <div id="capacityHint" class="capacity-hint"></div>

        <label>Purpose / notes
            <textarea name="purpose" id="purpose" rows="5" placeholder="Add the agenda, equipment needs, or approval context." required>{{ old('purpose') }}</textarea>
        </label>

        <button type="submit" class="button-primary-wide">Submit booking request</button>
    </form>

    <aside class="booking-side-panel" aria-live="polite">
        <div class="resource-preview">
            <div class="preview-visual">
                <span id="previewInitial">CB</span>
            </div>
            <div>
                <span id="previewType" class="type-pill">Resource</span>
                <h2 id="previewName">Select a resource</h2>
                <p id="previewFeatures" class="muted">Capacity and features will appear here.</p>
            </div>
        </div>

        <div class="booking-summary-grid">
            <div>
                <span>Total</span>
                <strong>{{ $resourceCount }}</strong>
            </div>
            <div>
                <span>Rooms</span>
                <strong>{{ $roomCount }}</strong>
            </div>
            <div>
                <span>Labs</span>
                <strong>{{ $labCount }}</strong>
            </div>
            <div>
                <span>Equipment</span>
                <strong>{{ $equipmentCount }}</strong>
            </div>
        </div>

        <div class="request-preview">
            <span class="section-kicker">Live preview</span>
            <h2 id="summaryTitle">Untitled event</h2>
            <p id="summaryTime" class="muted">No time selected</p>
            <p id="summaryPeople" class="muted">1 participant</p>
        </div>
    </aside>
</section>
@endsection
