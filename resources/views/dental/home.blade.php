@extends('dental.layouts.app')

@section('title', 'Luma Dental')

@section('content')
    @include('dental.partials.hero')
    @include('dental.partials.benefits')
    @include('dental.partials.treatments')
    @include('dental.partials.clinic')
    @include('dental.partials.team')
    @include('dental.partials.faq')
    @include('dental.partials.closing')
@endsection