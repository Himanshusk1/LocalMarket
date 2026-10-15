@extends('layouts.app')

@section('title', 'Register an Account — LocalMarket')
@section('meta_description', 'Join LocalMarket to start buying and selling second-hand goods and local services across India.')

@section('content')

<div class="row justify-content-center my-4">
    <div class="col-md-7 col-lg-6">
        
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background-color: #f1f5f9; color: var(--lm-accent);">
                    <i class="bi bi-person-plus-fill fs-2"></i>
                </div>
                <h1 class="h4 fw-bold text-dark mb-1">Create an Account</h1>
                <p class="text-muted small">Join India's local community marketplace in seconds</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Manish Sharma">
                    </div>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. manish@example.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone & City -->
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="phone" class="form-label small fw-bold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+91 98260 00000">
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        <label for="city" class="form-label small fw-bold">City</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', 'Gwalior') }}" placeholder="e.g. Gwalior">
                        </div>
                        @error('city')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="At least 6 characters">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label small fw-bold">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Re-enter your password">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold fs-6 shadow-sm mb-3">
                    Complete Registration
                </button>

                <div class="text-center text-muted small">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                        Sign In here
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
