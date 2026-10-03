@extends('errors.layout')

@section('title', 'Acceso denegado')
@section('code', '403')
@section('badge_class', 'bg-amber-100 text-amber-700')

@section('icon')
<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
</svg>
@endsection

@section('message', $exception->getMessage() ?: 'No tienes los permisos necesarios para acceder a este recurso institucional. Si consideras que esto es un error, por favor contacta al administrador del sistema.')
