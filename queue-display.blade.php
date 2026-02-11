@extends('layouts.app')

@section('title', 'Your Queue Number')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-kwsp text-center py-5">
                <div class="card-body">
                    <h5 class="text-muted mb-2">Your Queue Number</h5>
                    <h1 class="display-1 fw-bold text-danger mb-3">{{ $queue->display_number }}</h1>
                    <p class="text-muted">{{ $queue->service->name }}</p>
                    <p class="small">Please wait for your number to be called on the display board.</p>
                    <div class="mt-4">
                        <a href="{{ route('board') }}" class="btn btn-kwsp me-2">View Display Board</a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
