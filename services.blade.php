@extends('layouts.app')

@section('title', 'Get Queue Number')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Get Queue Number</h2>
            <p class="text-muted mb-0">Select a service to receive your virtual queue number.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">← Back to Dashboard</a>
    </div>
    <div class="row g-4">
        @foreach($services as $service)
        <div class="col-md-6 col-lg-4">
            <div class="card card-kwsp border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title fw-bold mb-0">{{ $service->name }}</h5>
                        <span class="badge bg-danger rounded-pill">{{ $service->code }}</span>
                    </div>
                    <p class="card-text text-muted small">{{ Str::limit($service->description, 100) }}</p>
                    @if($service->estimated_time)
                        <p class="small mb-3"><span class="text-danger fw-semibold">Est. {{ $service->estimated_time }}</span></p>
                    @endif
                    <form method="POST" action="{{ route('queue.store') }}">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="btn btn-kwsp w-100">Take Queue</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
