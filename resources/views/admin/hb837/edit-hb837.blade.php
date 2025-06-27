@extends('layouts.admin')

@section('title', 'Edit HB837 Record')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Edit HB837 Record</h1>
            <p class="text-muted mb-0">{{ $hb837->property_name }} - {{ $hb837->address }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837</a></li>
                <li class="breadcrumb-item active">Edit #{{ $hb837->id }}</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Editing: {{ $hb837->property_name }}
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('admin.hb837.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('admin.hb837.report', $hb837->id) }}" class="btn btn-sm btn-success" target="_blank">
                                <i class="fas fa-file-pdf"></i> Property Report
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" id="editTabs" role="tablist">
                            @foreach (['general', 'address', 'contacts', 'financials', 'files', 'notes', 'google_maps'] as $tabName)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $tab == $tabName ? 'active' : '' }}"
                                       id="{{ $tabName }}-tab"
                                       href="{{ url('admin/hb837/' . $hb837->id . '/edit/' . $tabName) }}"
                                       role="tab"
                                       aria-controls="{{ $tabName }}"
                                       aria-selected="{{ $tab == $tabName ? 'true' : 'false' }}">
                                        <i class="fas fa-{{ 
                                            $tabName == 'general' ? 'info-circle' : 
                                            ($tabName == 'address' ? 'map-marker-alt' : 
                                            ($tabName == 'contacts' ? 'users' : 
                                            ($tabName == 'financials' ? 'dollar-sign' : 
                                            ($tabName == 'files' ? 'paperclip' : 
                                            ($tabName == 'notes' ? 'sticky-note' : 'map'))))) }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $tabName)) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content p-3">
                        @include('admin.hb837.partials.content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultant Actions -->
    @if($hb837->assigned_consultant_id)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie"></i>
                            Consultant Management
                        </h3>
                    </div>
                    <div class="card-body">
                        <p>This record is currently assigned to a consultant. You can detach the consultant if needed.</p>
                        <form id="detach-consultant-form" action="{{ route('admin.consultants.detach', $hb837->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to detach the consultant?')">
                                <i class="fas fa-user-times"></i> Detach Consultant
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Danger Zone -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Danger Zone
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-danger">This section contains actions that are irreversible. Please proceed with caution.</p>
                    <form action="{{ route('admin.hb837.destroy', $hb837->id) }}" method="POST" style="display:inline;">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record? This action cannot be undone.');">
                            <i class="fas fa-trash"></i> Delete Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('custom_css')
    <style>
        .nav-tabs-custom {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs .nav-link {
            border-radius: 0;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #007bff;
            color: #007bff;
        }
        
        .nav-tabs .nav-link.active {
            background-color: transparent;
            color: #007bff;
            border-bottom-color: #007bff;
            font-weight: bold;
        }
        
        .tab-content {
            background: #fff;
        }
    </style>
@stop

@section('custom_js')
    <script>

@section('custom_js')
    <script>
        $(document).ready(function() {
            // Consultant details loading
            const consultantSelect = $("#consultant_id");
            const consultantDetails = $("#consultant-details");
            
            if (consultantSelect.length && consultantDetails.length) {
                consultantSelect.on("change", function() {
                    const consultantId = $(this).val();
                    if (consultantId === "-1") {
                        consultantDetails.html(`<p class="text-muted">No consultant selected.</p>`);
                        return;
                    }
                    
                    // Show loading
                    consultantDetails.html(`<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>`);
                    
                    fetch(`/admin/consultants/get/${consultantId}`)
                        .then(response => response.json())
                        .then(data => {
                            consultantDetails.html(`
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Consultant Details</h4>
                                        <hr>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Name:</strong> ${data.first_name} ${data.last_name}</li>
                                            <li class="list-group-item"><strong>Email:</strong> ${data.email}</li>
                                            <li class="list-group-item"><strong>Phone:</strong> ${data.phone}</li>
                                        </ul>
                                    </div>
                                </div>
                            `);
                        })
                        .catch(error => {
                            consultantDetails.html(`<div class="alert alert-danger">Error retrieving consultant details: ${error.message}</div>`);
                        });
                });
            }
            
            // Detach consultant handling
            $(document).on("click", "#detach-consultant", function(e) {
                const detachForm = $("#detach-consultant-form");
                if (detachForm.length) {
                    if (confirm("Are you sure you want to detach the consultant?")) {
                        detachForm.submit();
                    }
                }
            });
        });
    </script>
@stop
