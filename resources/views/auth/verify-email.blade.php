@extends('layouts.guest')

@section('content')
    <livewire:auth.verify-email :token="$token ?? null" />
@endsection
