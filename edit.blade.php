@extends('layouts.admin')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff')

@section('content')
<div class="card card-dashboard border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-4">Edit Staff</h5>
        <form method="POST" action="{{ route('admin.staff.update', $staff) }}">
            @csrf @method('PUT')
            <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>@error('name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label">Staff Number (No. Kakitangan)</label><input type="text" name="staff_number" class="form-control" value="{{ old('staff_number', $staff->staff_number) }}">@error('staff_number')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label">Email (@kwsp.gov.my)</label><input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}" required>@error('email')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label">Phone Number</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}" placeholder="e.g. 012-3456789">@error('phone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label">New Password (leave blank to keep)</label><input type="password" name="password" class="form-control">@error('password')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-select"><option value="staff" {{ old('role', $staff->role) == 'staff' ? 'selected' : '' }}>Staff</option><option value="admin" {{ old('role', $staff->role) == 'admin' ? 'selected' : '' }}>Admin</option></select></div>
            <div class="mb-3">
                <label class="form-label">Assigned Counters</label>
                <p class="text-muted small mb-2">Select which counters this staff can operate.</p>
                <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                    @forelse($counters as $c)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="counters[]" value="{{ $c->id }}" id="counter-{{ $c->id }}" {{ in_array($c->id, old('counters', $staff->counters->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <label class="form-check-label" for="counter-{{ $c->id }}">Counter {{ $c->number }} – {{ $c->service->name ?? 'N/A' }}</label>
                    </div>
                    @empty
                    <p class="text-muted mb-0 small">No counters available. <a href="{{ route('admin.counters.create') }}">Add counters</a> first.</p>
                    @endforelse
                </div>
            </div>
            <button type="submit" class="btn text-white" style="background: #E93D5A;">Update</button>
        </form>
    </div>
</div>
@endsection
