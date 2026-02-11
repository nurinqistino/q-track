@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h2>Dashboard</h2>
    <p>Welcome, {{ auth()->user()->name }}</p>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-outline-secondary">Logout</button></form>
</div>
@endsection
