@extends('layouts.app')

@section('styles')
    <style>
        .nav-tabs .nav-link {
            color: #11467a; /* Dark blue */
        }

        .nav-tabs .nav-link.active {
            color: #ffffff;
            background-color: #11467a; /* Dark blue */
            border-color: #11467a;
        }

        .tab-content {
            border: 1px solid #dee2e6; /* Light gray */
            border-top: 0;
            padding: 15px;
        }

        /* Consultant edit form styles */
        .consultant-form {
            background-color: #f7f7f7; /* grayscale background */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .consultant-form input,
        .consultant-form select,
        .consultant-form textarea {
            background-color: #eaeaea; /* light gray for fields */
            border: 1px solid #999;
            color: #333;
        }
        .consultant-title {
            color: #ffffff; /* white title text */
            background-color: #0e3a60; /* matching blue background */
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@php
    $assignments = App\Models\HB837::where('assigned_consultant_id', $consultant->id)
        ->whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
        ->where('contracting_status', 'executed')
        ->paginate(10);

    $completedProjects = App\Models\HB837::where('assigned_consultant_id', $consultant->id)
        ->where('report_status', 'completed')
        ->paginate(10);
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center p-3">
                <h2 class="text-dark">Consultant Details - {{ $consultant->first_name }} {{ $consultant->last_name }}</h2>
                <div>
                    <a class="btn btn-primary" href="{{ route('admin.consultants.index') }}">Back</a>
                </div>
            </div>
        </div>

        <!-- Bootstrap Nav Tabs -->
        <ul class="nav nav-tabs bg-light border rounded" id="consultantTabs">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#consultantDetails" id="tab-consultant">
                    Consultant Details
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#currentAssignments" id="tab-current-assignments">
                    Current Assignments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#completedProjects" id="tab-completed-projects">
                    Completed Projects
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-4 bg-white shadow rounded border">
            <div class="tab-pane fade" id="consultantDetails">
                @include('admin.consultants.forms.form', ['consultant' => $consultant])
            </div>
            <div class="tab-pane fade" id="currentAssignments">
                @include('admin.consultants.tables.orders', ['assignOrders' => $assignments ?? collect()])
            </div>
            <div class="tab-pane fade" id="completedProjects">
                @include('admin.consultants.tables.completed', ['completedOrders' => $completedProjects ?? collect()])
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let storedTab = localStorage.getItem("activeTab");
            let defaultTab = "#consultantDetails"; // Default tab

            if (storedTab) {
                let activeTab = document.querySelector(`a[href="${storedTab}"]`);
                if (activeTab) {
                    new bootstrap.Tab(activeTab).show();
                }
            } else {
                new bootstrap.Tab(document.querySelector(`a[href="${defaultTab}"]`)).show();
            }

            // Listen for tab clicks and store the active tab
            document.querySelectorAll(".nav-tabs a").forEach(tab => {
                tab.addEventListener("click", function () {
                    localStorage.setItem("activeTab", this.getAttribute("href"));
                });
            });
        });
    </script>
@endsection
