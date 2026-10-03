@extends('errors.layout')

@section('title', 'Sesión expirada')
@section('code', '419')
@section('badge_class', 'bg-yellow-100 text-yellow-700')

@section('icon')
<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('message', 'Tu sesión de seguridad ha expirado por inactividad. Por favor, vuelve a iniciar sesión o recarga la página para continuar.')
