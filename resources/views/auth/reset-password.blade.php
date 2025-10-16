@extends('layouts.guest')

@section('content')
    <livewire:auth.reset-password :token="$token" :email="request('email')" />
@endsection
