@extends('layouts.app')

@section('styles')
    @parent
@endsection

@php
    $hb837 = new App\Models\HB837();
    $property_types = config('hb837.property_types');
    $securitygauge_crime_risks = config('hb837.security_gauge');
    $consultants = App\Models\Consultant::all();
@endphp

@section('content')
<div class="container bg-light p-3">
    <h1>Create HB837 Record</h1>
    <form action="{{ route('admin.hb837.store') }}" method="POST">
        @csrf

        <div class="tab-content">
            <!-- General Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
               @include('admin.hb837.partials.general')
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Submit</button>
    </form>
</div>
@endsection
