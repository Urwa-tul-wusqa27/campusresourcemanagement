@extends('layouts.app')

@section('content')
<div class="page-head">
    <div>
        <h1>Manage resources</h1>
        <p class="muted">Create and update campus rooms, labs, and equipment</p>
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
        <label>Type
            <select name="type" required>
                @foreach (['room', 'lab', 'equipment'] as $resourceType)
                    <option value="{{ $resourceType }}" {{ old('type', $edit->type ?? '') === $resourceType ? 'selected' : '' }}>{{ ucfirst($resourceType) }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="form-row">
        <label>Capacity
            <input type="number" name="capacity" min="0" value="{{ old('capacity', $edit->capacity ?? 0) }}">
        </label>
        <label>Status
            <select name="status">
                @foreach (['active', 'inactive', 'maintenance'] as $status)
                    <option value="{{ $status }}" {{ old('status', $edit->status ?? 'active') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <label>Features
        <input type="text" name="features" value="{{ old('features', $edit->features ?? '') }}" placeholder="Projector, whiteboard, HDMI">
    </label>
    <button type="submit">{{ $edit ? 'Update resource' : 'Add resource' }}</button>
</form>

<div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Features</th>
            <th>Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($resources as $resource)
            <tr>
                <td>{{ $resource->name }}</td>
                <td>{{ $resource->type }}</td>
                <td>{{ (int) $resource->capacity }}</td>
                <td>{{ $resource->features }}</td>
                <td>{{ $resource->status }}</td>
                <td><a href="/admin/resources.php?edit={{ (int) $resource->id }}">Edit</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
