@extends('layouts.staff')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="card card-dashboard border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-4">Update Profile</h5>
        <form method="POST" action="{{ route('staff.profile.update') }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="staff_number" class="form-label">Staff ID (No. Kakitangan)</label>
                <input type="text" name="staff_number" id="staff_number" class="form-control" value="{{ old('staff_number', auth()->user()->staff_number) }}" placeholder="e.g. STF001">
                @error('staff_number')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="{{ auth()->user()->email }}" disabled>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" placeholder="e.g. 012-3456789">
                @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank to keep)</label>
                <input type="password" name="password" id="password" class="form-control">
                @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            <button type="submit" class="btn text-white" style="background: #E93D5A;">Update</button>
        </form>
    </div>
</div>
@endsection
