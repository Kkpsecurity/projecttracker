@extends('adminlte::page')

@section('title', 'Help Center')

@section('content_header')
    <h1>
        <i class="fas fa-question-circle"></i> Help Center
        <small>Get help and support for using the system</small>
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Search Box -->
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-search"></i> Search Help Topics</h3>
                </div>
                <div class="card-body">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" placeholder="Search for help topics, features, or questions...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Getting Started -->
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-play-circle"></i> Getting Started</h3>
                </div>
                <div class="card-body">
                    <p>New to the system? Start here to learn the basics and get up and running quickly.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> System Overview</li>
                        <li><i class="fas fa-check text-success"></i> Initial Setup</li>
                        <li><i class="fas fa-check text-success"></i> First Steps</li>
                        <li><i class="fas fa-check text-success"></i> Basic Navigation</li>
                    </ul>
                    <a href="{{ route('help.getting-started') }}" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-right"></i> Start Here
                    </a>
                </div>
            </div>
        </div>

        <!-- User Guide -->
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-book"></i> User Guide</h3>
                </div>
                <div class="card-body">
                    <p>Comprehensive guides for all system features and functionality.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Project Management</li>
                        <li><i class="fas fa-check text-success"></i> HB837 Projects</li>
                        <li><i class="fas fa-check text-success"></i> Consultant Records</li>
                        <li><i class="fas fa-check text-success"></i> Maps & Plots</li>
                    </ul>
                    <a href="{{ route('help.user-guide') }}" class="btn btn-success btn-block">
                        <i class="fas fa-arrow-right"></i> Browse Guides
                    </a>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="col-md-4">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-question"></i> FAQ</h3>
                </div>
                <div class="card-body">
                    <p>Find answers to frequently asked questions and common issues.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Common Questions</li>
                        <li><i class="fas fa-check text-success"></i> Troubleshooting</li>
                        <li><i class="fas fa-check text-success"></i> Error Solutions</li>
                        <li><i class="fas fa-check text-success"></i> Best Practices</li>
                    </ul>
                    <a href="{{ route('help.faq') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-arrow-right"></i> View FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- System Documentation -->
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-alt"></i> System Documentation</h3>
                </div>
                <div class="card-body">
                    <p>Technical documentation and detailed system information.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-cog text-primary"></i> System Architecture</li>
                        <li><i class="fas fa-database text-primary"></i> Database Schema</li>
                        <li><i class="fas fa-code text-primary"></i> API Documentation</li>
                        <li><i class="fas fa-shield-alt text-primary"></i> Security Features</li>
                    </ul>
                    <a href="{{ route('help.documentation') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-right"></i> View Documentation
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-headset"></i> Contact Support</h3>
                </div>
                <div class="card-body">
                    <p>Need additional help? Get in touch with our support team.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope text-danger"></i> Email Support</li>
                        <li><i class="fas fa-phone text-danger"></i> Phone Support</li>
                        <li><i class="fas fa-ticket-alt text-danger"></i> Submit Ticket</li>
                        <li><i class="fas fa-clock text-danger"></i> Support Hours</li>
                    </ul>
                    <a href="{{ route('help.contact') }}" class="btn btn-danger btn-block">
                        <i class="fas fa-arrow-right"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-link"></i> Quick Links</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h5><i class="fas fa-tachometer-alt text-primary"></i> Dashboard</h5>
                            <p class="text-muted">Return to the main dashboard</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary">Go to Dashboard</a>
                        </div>
                        <div class="col-md-3">
                            <h5><i class="fas fa-project-diagram text-success"></i> Projects</h5>
                            <p class="text-muted">Manage HB837 projects</p>
                            <a href="{{ route('admin.hb837.index') }}" class="btn btn-sm btn-outline-success">View Projects</a>
                        </div>
                        <div class="col-md-3">
                            <h5><i class="fas fa-user-tie text-info"></i> Consultants</h5>
                            <p class="text-muted">Manage consultant records</p>
                            <a href="{{ route('admin.consultants.index') }}" class="btn btn-sm btn-outline-info">View Consultants</a>
                        </div>
                        <div class="col-md-3">
                            <h5><i class="fas fa-map text-warning"></i> Maps</h5>
                            <p class="text-muted">View maps and plots</p>
                            <a href="{{ route('admin.maps.index') }}" class="btn btn-sm btn-outline-warning">View Maps</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}
.list-unstyled li {
    padding: 2px 0;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Add search functionality
    $('.input-group input').on('keypress', function(e) {
        if (e.which === 13) {
            var searchTerm = $(this).val();
            if (searchTerm.trim()) {
                // Here you could implement actual search functionality
                console.log('Searching for:', searchTerm);
                // For now, just show an alert
                alert('Search functionality coming soon!');
            }
        }
    });

    $('.input-group .btn').on('click', function() {
        var searchTerm = $('.input-group input').val();
        if (searchTerm.trim()) {
            console.log('Searching for:', searchTerm);
            alert('Search functionality coming soon!');
        }
    });
});
</script>
@stop
