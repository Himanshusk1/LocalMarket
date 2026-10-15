@extends('layouts.app')

@section('title', 'Login to Your Account — LocalMarket')
@section('meta_description', 'Sign in to your LocalMarket account to manage your listings and communicate with buyers.')

@section('content')

<div class="row justify-content-center my-4">
    <div class="col-md-6 col-lg-5">
        
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background-color: #f1f5f9; color: var(--lm-primary);">
                    <i class="bi bi-person-fill fs-2"></i>
                </div>
                <h1 class="h4 fw-bold text-dark mb-1">Welcome Back</h1>
                <p class="text-muted small">Sign in to manage your ads and messages</p>
            </div>

            <!-- Demo accounts helper box -->
            <div class="alert alert-light border shadow-none small mb-4 p-3 rounded-3">
                <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                    <i class="bi bi-info-circle-fill text-primary"></i> Demo Login Credentials:
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>Email: <code>rahul@example.com</code></span>
                    <button type="button" class="btn btn-sm btn-link py-0 text-decoration-none" onclick="fillDemo('rahul@example.com', 'password123')">Use</button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Password: <code>password123</code></span>
                    <span class="text-muted" style="font-size: 0.72rem;">(Gwalior Seller)</span>
                </div>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Remember me
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold fs-6 shadow-sm mb-3">
                    Sign In
                </button>

                <div class="text-center text-muted small">
                    Don't have an account yet? 
                    <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">
                        Create Free Account
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function fillDemo(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
@endpush
