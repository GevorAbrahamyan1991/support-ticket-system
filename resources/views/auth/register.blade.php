@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <x-card :header="'Register'">
            <form id="register-form" method="POST" action="{{ route('register') }}" data-ajax="true">
                @csrf
                <x-input name="name" label="Name" type="text" :value="old('name')" :required="true" :autofocus="true" />
                <x-input name="email" label="Email" type="email" :value="old('email')" :required="true" />
                <x-input name="password" label="Password" type="password" :required="true" />
                <x-input name="password_confirmation" label="Confirm Password" type="password" :required="true" />
                <div class="ajax-feedback mb-2"></div>
                <x-button type="submit" class="card-button w-100" id="register-btn">
                    <span id="register-btn-text">Register</span>
                </x-button>
            </form>
        </x-card>
    </div>
</div>
@endsection 