@extends('backend.admin.layout')

@section('title', 'Daftar Reservasi')
@section('page_title', 'Daftar Reservasi')

@section('styles')
    @include('backend.partials.reservations-styles')
@endsection

@section('content')
    @include('backend.partials.reservations-content')
@endsection
