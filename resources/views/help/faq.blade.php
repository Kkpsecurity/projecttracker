@extends('adminlte::page')

@section('title', 'FAQ - Help Center')

@section('content_header')
    <h1>
        <i class="fas fa-question"></i> Frequently Asked Questions
        <small>Find answers to common questions</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Help Center</a></li>
            <li class="breadcrumb-item active">FAQ</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <!-- General Questions -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> General Questions</h3>
                </div>
                <div class="card-body">
                    <div id="general-accordion" role="tablist" aria-multiselectable="true">
                        <div class="card">
                            <div class="card-header" role="tab" id="general-heading-1">
                                <h5 class="mb-0">
                                    <a data-toggle="collapse" data-parent="#general-accordion" href="#general-collapse-1" aria-expanded="true" aria-controls="general-collapse-1">
                                        <i class="fas fa-chevron-down"></i> What is the KKP Security Project Tracker?
                                    </a>
                                </h5>
                            </div>
                            <div id="general-collapse-1" class="collapse show" role="tabpanel" aria-labelledby="general-heading-1">
                                <div class="card-body">
                                    The KKP Security Project Tracker is a comprehensive project management system designed specifically for security-related projects. It helps manage HB837 projects, consultant records, and provides mapping capabilities for project visualization.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="general-heading-2">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#general-accordion" href="#general-collapse-2" aria-expanded="false" aria-controls="general-collapse-2">
                                        <i class="fas fa-chevron-down"></i> How do I access the system?
                                    </a>
                                </h5>
                            </div>
                            <div id="general-collapse-2" class="collapse" role="tabpanel" aria-labelledby="general-heading-2">
                                <div class="card-body">
                                    You need valid login credentials provided by your administrator. Once you have your username and password, you can access the system through the login page. The system uses secure authentication to protect your data.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="general-heading-3">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#general-accordion" href="#general-collapse-3" aria-expanded="false" aria-controls="general-collapse-3">
                                        <i class="fas fa-chevron-down"></i> What browsers are supported?
                                    </a>
                                </h5>
                            </div>
                            <div id="general-collapse-3" class="collapse" role="tabpanel" aria-labelledby="general-heading-3">
                                <div class="card-body">
                                    The system works best with modern browsers including:
                                    <ul>
                                        <li>Google Chrome (recommended)</li>
                                        <li>Mozilla Firefox</li>
                                        <li>Microsoft Edge</li>
                                        <li>Safari (latest version)</li>
                                    </ul>
                                    Make sure JavaScript is enabled for full functionality.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Management -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-project-diagram"></i> Project Management</h3>
                </div>
                <div class="card-body">
                    <div id="project-accordion" role="tablist" aria-multiselectable="true">
                        <div class="card">
                            <div class="card-header" role="tab" id="project-heading-1">
                                <h5 class="mb-0">
                                    <a data-toggle="collapse" data-parent="#project-accordion" href="#project-collapse-1" aria-expanded="false" aria-controls="project-collapse-1">
                                        <i class="fas fa-chevron-down"></i> How do I create a new HB837 project?
                                    </a>
                                </h5>
                            </div>
                            <div id="project-collapse-1" class="collapse" role="tabpanel" aria-labelledby="project-heading-1">
                                <div class="card-body">
                                    To create a new HB837 project:
                                    <ol>
                                        <li>Navigate to <strong>HB837 Projects</strong> in the sidebar</li>
                                        <li>Click <strong>Create Project</strong></li>
                                        <li>Fill in the required information</li>
                                        <li>Click <strong>Save</strong> to create the project</li>
                                    </ol>
                                    You can also use the Smart Import feature to import multiple projects from a CSV file.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="project-heading-2">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#project-accordion" href="#project-collapse-2" aria-expanded="false" aria-controls="project-collapse-2">
                                        <i class="fas fa-chevron-down"></i> What is the Smart Import feature?
                                    </a>
                                </h5>
                            </div>
                            <div id="project-collapse-2" class="collapse" role="tabpanel" aria-labelledby="project-heading-2">
                                <div class="card-body">
                                    Smart Import allows you to upload multiple projects at once using a CSV file. The system will automatically map the columns and import the data. You can preview the data before importing and make adjustments as needed.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="project-heading-3">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#project-accordion" href="#project-collapse-3" aria-expanded="false" aria-controls="project-collapse-3">
                                        <i class="fas fa-chevron-down"></i> Can I edit projects after creation?
                                    </a>
                                </h5>
                            </div>
                            <div id="project-collapse-3" class="collapse" role="tabpanel" aria-labelledby="project-heading-3">
                                <div class="card-body">
                                    Yes! You can edit projects at any time. Go to the project list, find the project you want to edit, and click the edit button. All project information can be updated except the unique project ID.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-wrench"></i> Troubleshooting</h3>
                </div>
                <div class="card-body">
                    <div id="trouble-accordion" role="tablist" aria-multiselectable="true">
                        <div class="card">
                            <div class="card-header" role="tab" id="trouble-heading-1">
                                <h5 class="mb-0">
                                    <a data-toggle="collapse" data-parent="#trouble-accordion" href="#trouble-collapse-1" aria-expanded="false" aria-controls="trouble-collapse-1">
                                        <i class="fas fa-chevron-down"></i> The page is loading slowly. What should I do?
                                    </a>
                                </h5>
                            </div>
                            <div id="trouble-collapse-1" class="collapse" role="tabpanel" aria-labelledby="trouble-heading-1">
                                <div class="card-body">
                                    Try these steps:
                                    <ul>
                                        <li>Check your internet connection</li>
                                        <li>Clear your browser cache and cookies</li>
                                        <li>Try refreshing the page</li>
                                        <li>Close other browser tabs to free up memory</li>
                                        <li>If the problem persists, contact support</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="trouble-heading-2">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#trouble-accordion" href="#trouble-collapse-2" aria-expanded="false" aria-controls="trouble-collapse-2">
                                        <i class="fas fa-chevron-down"></i> I'm getting an error when uploading files. What's wrong?
                                    </a>
                                </h5>
                            </div>
                            <div id="trouble-collapse-2" class="collapse" role="tabpanel" aria-labelledby="trouble-heading-2">
                                <div class="card-body">
                                    Check the following:
                                    <ul>
                                        <li>File size - make sure it's within the allowed limit</li>
                                        <li>File format - only supported formats are allowed</li>
                                        <li>File name - avoid special characters</li>
                                        <li>Network connection - ensure stable internet</li>
                                    </ul>
                                    If you continue to have issues, try using a different browser or contact support.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="trouble-heading-3">
                                <h5 class="mb-0">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#trouble-accordion" href="#trouble-collapse-3" aria-expanded="false" aria-controls="trouble-collapse-3">
                                        <i class="fas fa-chevron-down"></i> I forgot my password. How do I reset it?
                                    </a>
                                </h5>
                            </div>
                            <div id="trouble-collapse-3" class="collapse" role="tabpanel" aria-labelledby="trouble-heading-3">
                                <div class="card-body">
                                    Contact your system administrator to reset your password. For security reasons, password resets must be handled by administrators who can verify your identity.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-search"></i> Search FAQ</h3>
                </div>
                <div class="card-body">
                    <input type="text" id="faq-search" class="form-control" placeholder="Search questions...">
                    <small class="text-muted">Type to search through all FAQ items</small>
                </div>
            </div>

            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-question-circle"></i> Still Need Help?</h3>
                </div>
                <div class="card-body">
                    <p>Can't find what you're looking for?</p>
                    <div class="list-group">
                        <a href="{{ route('help.user-guide') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-book text-success"></i> User Guide
                        </a>
                        <a href="{{ route('help.contact') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-headset text-danger"></i> Contact Support
                        </a>
                        <a href="{{ route('help.documentation') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-alt text-secondary"></i> Documentation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.card-header a {
    color: #333;
    text-decoration: none;
    display: block;
}
.card-header a:hover {
    color: #007bff;
    text-decoration: none;
}
.card-header a.collapsed i {
    transform: rotate(-90deg);
}
.card-header a i {
    transition: transform 0.2s;
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

@section('js')
<script>
$(document).ready(function() {
    // FAQ search functionality
    $('#faq-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();

        $('.card h5 a').each(function() {
            var questionText = $(this).text().toLowerCase();
            var cardBody = $(this).closest('.card').find('.card-body').text().toLowerCase();

            if (questionText.includes(searchTerm) || cardBody.includes(searchTerm)) {
                $(this).closest('.card').show();
            } else {
                $(this).closest('.card').hide();
            }
        });
    });

    // Smooth scrolling for anchors
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 300);
        }
    });
});
</script>
@stop
