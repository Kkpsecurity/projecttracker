@extends('adminlte::page')

@section('title', 'User Guide - Help Center')

@section('content_header')
    <h1>
        <i class="fas fa-book"></i> User Guide
        <small>Comprehensive guide to using the system</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Help Center</a></li>
            <li class="breadcrumb-item active">User Guide</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <!-- Table of Contents -->
            <div class="card card-secondary sticky-top">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Table of Contents</h3>
                </div>
                <div class="card-body p-0">
                    <nav class="nav nav-pills nav-sidebar flex-column">
                        <a href="#dashboard" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="#projects" class="nav-link">
                            <i class="fas fa-project-diagram"></i> HB837 Projects
                        </a>
                        <a href="#consultants" class="nav-link">
                            <i class="fas fa-user-tie"></i> Consultant Records
                        </a>
                        <a href="#maps" class="nav-link">
                            <i class="fas fa-map"></i> Maps & Plots
                        </a>
                        <a href="#admin" class="nav-link">
                            <i class="fas fa-cogs"></i> Admin Center
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Dashboard Section -->
            <div id="dashboard" class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Dashboard</h3>
                </div>
                <div class="card-body">
                    <p>The Dashboard is your main control center, providing an overview of all your projects and activities.</p>

                    <h5>Key Features:</h5>
                    <ul>
                        <li><strong>Project Overview:</strong> See total projects, active projects, and recent activity</li>
                        <li><strong>Quick Actions:</strong> Direct links to create new projects or manage consultants</li>
                        <li><strong>Recent Activity:</strong> Track recent changes and updates</li>
                        <li><strong>Statistics:</strong> Visual charts and metrics</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Use the Dashboard as your starting point each day to get an overview of your work.
                    </div>
                </div>
            </div>

            <!-- HB837 Projects Section -->
            <div id="projects" class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-project-diagram"></i> HB837 Projects</h3>
                </div>
                <div class="card-body">
                    <p>Manage all your HB837-related projects with comprehensive tracking and reporting features.</p>

                    <h5>Creating a New Project:</h5>
                    <ol>
                        <li>Navigate to <strong>HB837 Projects</strong> → <strong>Create Project</strong></li>
                        <li>Fill in the project details:
                            <ul>
                                <li>Project name and description</li>
                                <li>Location information</li>
                                <li>Consultant assignments</li>
                                <li>Timeline and milestones</li>
                            </ul>
                        </li>
                        <li>Click <strong>Save</strong> to create the project</li>
                    </ol>

                    <h5>Managing Projects:</h5>
                    <ul>
                        <li><strong>View All Projects:</strong> See complete list with filtering and search</li>
                        <li><strong>Edit Projects:</strong> Update project information at any time</li>
                        <li><strong>Project Status:</strong> Track progress and completion</li>
                        <li><strong>Export Data:</strong> Download project information for reporting</li>
                    </ul>

                    <h5>Smart Import Feature:</h5>
                    <p>Use the Smart Import feature to upload multiple projects from a CSV file:</p>
                    <ol>
                        <li>Go to <strong>HB837 Projects</strong> → <strong>Smart Import</strong></li>
                        <li>Download the template CSV file</li>
                        <li>Fill in your project data</li>
                        <li>Upload the file and preview the data</li>
                        <li>Confirm the import to add all projects</li>
                    </ol>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> Always backup your data before performing bulk imports.
                    </div>
                </div>
            </div>

            <!-- Consultant Records Section -->
            <div id="consultants" class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-tie"></i> Consultant Records</h3>
                </div>
                <div class="card-body">
                    <p>Manage consultant information, assignments, and performance tracking.</p>

                    <h5>Adding Consultants:</h5>
                    <ol>
                        <li>Navigate to <strong>Consultant Records</strong></li>
                        <li>Click <strong>Add New Consultant</strong></li>
                        <li>Enter consultant details:
                            <ul>
                                <li>Personal information</li>
                                <li>Contact details</li>
                                <li>Skills and certifications</li>
                                <li>Availability</li>
                            </ul>
                        </li>
                        <li>Save the consultant record</li>
                    </ol>

                    <h5>Managing Consultant Assignments:</h5>
                    <ul>
                        <li><strong>Project Assignment:</strong> Assign consultants to specific projects</li>
                        <li><strong>Workload Tracking:</strong> Monitor consultant capacity</li>
                        <li><strong>Performance Reviews:</strong> Track consultant performance</li>
                        <li><strong>Contact Management:</strong> Maintain up-to-date contact information</li>
                    </ul>
                </div>
            </div>

            <!-- Maps & Plots Section -->
            <div id="maps" class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-map"></i> Maps & Plots</h3>
                </div>
                <div class="card-body">
                    <p>Visualize project locations and manage geographical data with integrated mapping tools.</p>

                    <h5>Google Maps Integration:</h5>
                    <ul>
                        <li><strong>Project Locations:</strong> View all project locations on an interactive map</li>
                        <li><strong>Search Functionality:</strong> Find specific locations quickly</li>
                        <li><strong>Multiple Views:</strong> Switch between map, satellite, and terrain views</li>
                        <li><strong>Zoom Controls:</strong> Detailed view of specific areas</li>
                    </ul>

                    <h5>Plot Management:</h5>
                    <ul>
                        <li><strong>Add Plots:</strong> Create new plot entries with coordinates</li>
                        <li><strong>Edit Plots:</strong> Update plot information and boundaries</li>
                        <li><strong>Plot Visualization:</strong> See plots overlaid on maps</li>
                        <li><strong>Export Data:</strong> Download plot information for external use</li>
                    </ul>

                    <div class="alert alert-success">
                        <i class="fas fa-check"></i> <strong>Feature:</strong> The mapping system automatically geocodes addresses for easy plotting.
                    </div>
                </div>
            </div>

            <!-- Admin Center Section -->
            <div id="admin" class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cogs"></i> Admin Center</h3>
                </div>
                <div class="card-body">
                    <p><em>Note: Admin features are only available to users with administrator privileges.</em></p>

                    <h5>User Management:</h5>
                    <ul>
                        <li><strong>Add Users:</strong> Create new user accounts</li>
                        <li><strong>Edit Users:</strong> Update user information and permissions</li>
                        <li><strong>Role Management:</strong> Assign user roles and permissions</li>
                        <li><strong>User Status:</strong> Activate or deactivate user accounts</li>
                    </ul>

                    <h5>System Settings:</h5>
                    <ul>
                        <li><strong>General Settings:</strong> Configure system-wide options</li>
                        <li><strong>Security Settings:</strong> Manage security policies</li>
                        <li><strong>Backup Management:</strong> Schedule and manage data backups</li>
                        <li><strong>System Maintenance:</strong> Perform system maintenance tasks</li>
                    </ul>

                    <h5>Activity Logs:</h5>
                    <ul>
                        <li><strong>User Activity:</strong> Track user login and actions</li>
                        <li><strong>System Events:</strong> Monitor system events and errors</li>
                        <li><strong>Audit Trail:</strong> Complete audit trail for compliance</li>
                        <li><strong>Report Generation:</strong> Generate activity reports</li>
                    </ul>
                </div>
            </div>

            <!-- Best Practices -->
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star"></i> Best Practices</h3>
                </div>
                <div class="card-body">
                    <h5>General Tips:</h5>
                    <ul>
                        <li><strong>Regular Backups:</strong> Always backup your data before making major changes</li>
                        <li><strong>Consistent Naming:</strong> Use consistent naming conventions for projects and consultants</li>
                        <li><strong>Regular Updates:</strong> Keep project information up to date</li>
                        <li><strong>Security:</strong> Use strong passwords and log out when finished</li>
                    </ul>

                    <h5>Performance Tips:</h5>
                    <ul>
                        <li><strong>Browser Cache:</strong> Clear your browser cache if experiencing slow performance</li>
                        <li><strong>Multiple Tabs:</strong> Avoid opening too many tabs simultaneously</li>
                        <li><strong>File Sizes:</strong> Keep uploaded files to reasonable sizes</li>
                        <li><strong>Regular Cleanup:</strong> Archive old projects to maintain system performance</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.nav-sidebar .nav-link {
    color: #6c757d;
    padding: 8px 15px;
    border-radius: 0;
}
.nav-sidebar .nav-link:hover,
.nav-sidebar .nav-link.active {
    color: #007bff;
    background-color: #f8f9fa;
}
.sticky-top {
    top: 20px;
}
h5 {
    color: #495057;
    margin-top: 20px;
    margin-bottom: 10px;
}
.card {
    margin-bottom: 20px;
}
.alert {
    margin-top: 15px;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Smooth scrolling for table of contents
    $('.nav-sidebar a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 500);

            // Update active state
            $('.nav-sidebar .nav-link').removeClass('active');
            $(this).addClass('active');
        }
    });

    // Update active state on scroll
    $(window).on('scroll', function() {
        var scrollPos = $(document).scrollTop() + 150;

        $('.nav-sidebar a[href^="#"]').each(function() {
            var target = $(this.attr('href'));
            if (target.length && target.offset().top <= scrollPos && target.offset().top + target.height() > scrollPos) {
                $('.nav-sidebar .nav-link').removeClass('active');
                $(this).addClass('active');
            }
        });
    });
});
</script>
@stop
