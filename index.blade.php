@extends('layouts.admin')

@section('title', 'Staff')
@section('page-title', 'Staff')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h2 class="h5 mb-0 fw-bold">Staff</h2>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-sm text-white" style="background: #E93D5A;"><i class="bi bi-person-plus me-1"></i>Add Staff</a>
</div>
<div class="card card-dashboard border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Staff No. / Phone</th><th>Email</th><th>Role</th><th>Status</th><th>Counters</th><th width="140"></th></tr></thead>
            <tbody>
            @foreach($staff as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td><span class="small">{{ $s->staff_number ?? '-' }}</span>@if($s->phone)<br><span class="small text-muted">{{ $s->phone }}</span>@endif</td>
                <td>{{ $s->email }}</td>
                <td><span class="badge {{ $s->role === 'admin' ? 'bg-secondary' : 'bg-primary' }}">{{ ucfirst($s->role) }}</span></td>
                <td><span class="badge {{ ($s->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">{{ ($s->is_active ?? true) ? 'Active' : 'Non-active' }}</span></td>
                <td>@foreach($s->counters as $c){{ $c->number }} ({{ $c->service->name ?? '-' }}) @endforeach @if($s->counters->isEmpty())—@endif</td>
                <td>
                    <a href="{{ route('admin.staff.edit', $s) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    @if($s->id !== auth()->id())<form method="POST" action="{{ route('admin.staff.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete this staff?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>@endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
