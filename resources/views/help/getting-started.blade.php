@extends('adminlte::page')

@section('title', 'Getting Started - Help Center')

@section('content_header')
    <h1>
        <i class="fas fa-play-circle"></i> Getting Started
        <small>Learn the basics of using the KKP Security Project Tracker</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Help Center</a></li>
            <li class="breadcrumb-item active">Getting Started</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <!-- System Overview -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> System Overview</h3>
                </div>
                <div class="card-body">
                    <p>Welcome to the KKP Security Project Tracker! This system is designed to help you manage projects, consultant records, and track various aspects of security-related work.</p>

                    <h5><i class="fas fa-star text-warning"></i> Key Features:</h5>
                    <ul>
                        <li><strong>Dashboard:</strong> Get an overview of all your projects and activities</li>
                        <li><strong>HB837 Projects:</strong> Manage specific HB837-related projects with advanced features</li>
                        <li><strong>Consultant Records:</strong> Track and manage consultant information</li>
                        <li><strong>Maps & Plots:</strong> Visual representation of project locations and data</li>
                        <li><strong>Admin Center:</strong> User management and system settings (for administrators)</li>
                    </ul>
                </div>
            </div>

            <!-- First Steps -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-ol"></i> First Steps</h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-inverse">
                        <div class="time-label">
                            <span class="bg-success">Step 1</span>
                        </div>
                        <div>
                            <i class="fas fa-tachometer-alt bg-primary"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Explore the Dashboard</h3>
                                <div class="timeline-body">
                                    Start by visiting the <a href="{{ route('dashboard') }}">Dashboard</a> to get an overview of the system and see your current projects and activities.
                                </div>
                            </div>
                        </div>

                        <div class="time-label">
                            <span class="bg-success">Step 2</span>
                        </div>
                        <div>
                            <i class="fas fa-project-diagram bg-success"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Create Your First Project</h3>
                                <div class="timeline-body">
                                    Navigate to <strong>HB837 Projects</strong> → <strong>Create Project</strong> to add your first project to the system.
                                </div>
                            </div>
                        </div>

                        <div class="time-label">
                            <span class="bg-success">Step 3</span>
                        </div>
                        <div>
                            <i class="fas fa-user-tie bg-info"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Add Consultant Records</h3>
                                <div class="timeline-body">
                                    Use the <strong>Consultant Records</strong> section to add and manage consultant information.
                                </div>
                            </div>
                        </div>

                        <div class="time-label">
                            <span class="bg-success">Step 4</span>
                        </div>
                        <div>
                            <i class="fas fa-map bg-warning"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Explore Maps & Plots</h3>
                                <div class="timeline-body">
                                    Check out the mapping features to visualize your project locations and data.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Guide -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-compass"></i> Navigation Guide</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-bars text-primary"></i> Sidebar Menu</h5>
                            <p>The main navigation is located on the left sidebar. It's organized into sections:</p>
                            <ul>
                                <li><strong>Project Management:</strong> Core functionality</li>
                                <li><strong>Analytics & Reports:</strong> Data analysis tools</li>
                                <li><strong>Account Management:</strong> User settings</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-search text-success"></i> Search Feature</h5>
                            <p>Use the search bar at the top of the sidebar to quickly find menu items and features.</p>

                            <h5><i class="fas fa-expand text-warning"></i> Fullscreen Mode</h5>
                            <p>Click the fullscreen icon in the top navbar for a distraction-free experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Quick Links -->
        <div class="col-md-3">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-rocket"></i> Quick Start</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt text-primary"></i> Go to Dashboard
                        </a>
                        <a href="{{ route('admin.hb837.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus-circle text-success"></i> Create New Project
                        </a>
                        <a href="{{ route('admin.consultants.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-tie text-info"></i> View Consultants
                        </a>
                        <a href="{{ route('help.user-guide') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-book text-warning"></i> User Guide
                        </a>
                    </div>
                </div>
            </div>

            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-question-circle"></i> Need Help?</h3>
                </div>
                <div class="card-body">
                    <p>If you have questions or need assistance:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-book text-primary"></i> <a href="{{ route('help.user-guide') }}">User Guide</a></li>
                        <li><i class="fas fa-question text-warning"></i> <a href="{{ route('help.faq') }}">FAQ</a></li>
                        <li><i class="fas fa-headset text-danger"></i> <a href="{{ route('help.contact') }}">Contact Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.timeline-inverse .timeline-item .timeline-header {
    color: #333;
}
.list-group-item {
    border: none;
    padding: 10px 15px;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>
@stop
