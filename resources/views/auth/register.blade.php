@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <form id="register-form" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div id="register-auth-error"></div>
                    <button type="submit" class="btn card-button w-100" id="register-btn">
                        <span id="register-btn-text">Register</span>
                        <span id="register-btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('register-form');
                    const registerBtn = document.getElementById('register-btn');
                    const registerBtnText = document.getElementById('register-btn-text');
                    const registerBtnSpinner = document.getElementById('register-btn-spinner');
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        document.getElementById('name-error').textContent = '';
                        document.getElementById('email-error').textContent = '';
                        document.getElementById('password-error').textContent = '';
                        document.getElementById('register-auth-error').textContent = '';
                        document.getElementById('name').classList.remove('is-invalid');
                        document.getElementById('email').classList.remove('is-invalid');
                        document.getElementById('password').classList.remove('is-invalid');
                        registerBtn.disabled = true;
                        registerBtnSpinner.classList.remove('d-none');
                        registerBtnText.classList.add('d-none');
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
                                    if (data.errors.name) {
                                        document.getElementById('name-error').textContent = data.errors.name[0];
                                        document.getElementById('name').classList.add('is-invalid');
                                    }
                                    if (data.errors.email) {
                                        document.getElementById('email-error').textContent = data.errors.email[0];
                                        document.getElementById('email').classList.add('is-invalid');
                                    }
                                    if (data.errors.password) {
                                        document.getElementById('password-error').textContent = data.errors.password[0];
                                        document.getElementById('password').classList.add('is-invalid');
                                    }
                                    if (data.errors.auth) {
                                        document.getElementById('register-auth-error').innerHTML = '<div class="alert alert-danger">' + data.errors.auth[0] + '</div>';
                                    }
                                } else if (data.message) {
                                    document.getElementById('register-auth-error').innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                                }
                            }
                        } catch (error) {
                            document.getElementById('register-auth-error').innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                        } finally {
                            registerBtn.disabled = false;
                            registerBtnSpinner.classList.add('d-none');
                            registerBtnText.classList.remove('d-none');
                        }
                    });
                });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection 