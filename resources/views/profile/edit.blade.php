@extends('layouts.account')

@section('title', 'Profile settings')
@section('heading', 'Profile settings')
@section('subheading', 'Update your photo, personal info, and password.')

@section('content')
    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.update-password-form')
    @include('profile.partials.delete-user-form')
@endsection
