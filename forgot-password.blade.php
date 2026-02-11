@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-5">
            <div class="card card-kwsp">
                <div class="card-body p-4">
                    <h3 class="mb-4">Forgot Password</h3>
                    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
                    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3"><label for="email" class="form-label">Email (@kwsp.gov.my)</label><input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus></div>
                        <button type="submit" class="btn btn-kwsp w-100">Send Password Reset Link</button>
                    </form>
                    <div class="mt-3 text-center"><a href="{{ route('login') }}">Back to login</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
