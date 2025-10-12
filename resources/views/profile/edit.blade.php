@php
    $user = Auth::user();
    $isAdmin = $user && $user->hasRole(['admin', 'super-admin']);
@endphp

@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section($isAdmin ? 'page-title' : 'header')
    {{ __('Profile') }}
@endsection

@section('content')
    <div class="profile-edit-container">
        <div class="profile-edit-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="profile-edit-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="profile-edit-card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection