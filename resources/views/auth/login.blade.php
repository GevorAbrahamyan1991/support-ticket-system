@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                @if ($errors->has('auth'))
                    <div class="alert alert-danger">
                        {{ $errors->first('auth') }}
                    </div>
                @endif
                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>
                    <div id="auth-error"></div>
                    <button type="submit" class="btn card-button w-100" id="login-btn">
                        <span id="login-btn-text">Login</span>
                        <span id="login-btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('login-form');
                    const loginBtn = document.getElementById('login-btn');
                    const loginBtnText = document.getElementById('login-btn-text');
                    const loginBtnSpinner = document.getElementById('login-btn-spinner');
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        // Clear previous errors
                        document.getElementById('email-error').textContent = '';
                        document.getElementById('password-error').textContent = '';
                        document.getElementById('auth-error').textContent = '';
                        document.getElementById('email').classList.remove('is-invalid');
                        document.getElementById('password').classList.remove('is-invalid');
                        // Show spinner and disable button
                        loginBtn.disabled = true;
                        loginBtnSpinner.classList.remove('d-none');
                        loginBtnText.classList.add('d-none');
                        const formData = new FormData(form);
                        const csrfToken = form.querySelector('input[name="_token"]').value;
                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: formData
                            });
                            const data = await response.json();
                            if (response.ok && data.success) {
                                window.location.href = data.redirect;
                            } else {
                                if (data.errors) {
                                    if (data.errors.email) {
                                        document.getElementById('email-error').textContent = data.errors.email[0];
                                        document.getElementById('email').classList.add('is-invalid');
                                    }
                                    if (data.errors.password) {
                                        document.getElementById('password-error').textContent = data.errors.password[0];
                                        document.getElementById('password').classList.add('is-invalid');
                                    }
                                    if (data.errors.auth) {
                                        document.getElementById('auth-error').innerHTML = '<div class="alert alert-danger">' + data.errors.auth[0] + '</div>';
                                    }
                                } else if (data.message) {
                                    document.getElementById('auth-error').innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                                }
                            }
                        } catch (error) {
                            document.getElementById('auth-error').innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                        } finally {
                            loginBtn.disabled = false;
                            loginBtnSpinner.classList.add('d-none');
                            loginBtnText.classList.remove('d-none');
                        }
                    });
                });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection 