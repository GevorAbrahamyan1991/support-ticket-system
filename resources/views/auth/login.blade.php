@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <x-card :header="'Login'">
            @if ($errors->has('auth'))
                <x-alert type="danger" dismissible>{{ $errors->first('auth') }}</x-alert>
            @endif
            <form id="login-form" method="POST" action="{{ route('login') }}" data-ajax="true">
                @csrf
                <x-input name="email" label="Email" type="email" :value="old('email')" :required="true" :autofocus="true" />
                <x-input name="password" label="Password" type="password" :required="true" />
                <div class="ajax-feedback mb-2"></div>
                <x-button type="submit" class="card-button w-100" id="login-btn">
                    <span id="login-btn-text">Login</span>
                </x-button>
            </form>
        </x-card>
    </div>
</div>
@endsection 