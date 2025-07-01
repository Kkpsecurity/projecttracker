@extends('adminlte::page')

@section('title', 'Contact Support - Help Center')

@section('content_header')
    <h1>
        <i class="fas fa-headset"></i> Contact Support
        <small>Get help from our support team</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Help Center</a></li>
            <li class="breadcrumb-item active">Contact Support</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Contact Form -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-envelope"></i> Submit Support Request</h3>
                </div>
                <form>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="category">Issue Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="category" required>
                                <option value="">Select a category...</option>
                                <option value="technical">Technical Issue</option>
                                <option value="account">Account Access</option>
                                <option value="projects">Project Management</option>
                                <option value="consultants">Consultant Records</option>
                                <option value="maps">Maps & Plotting</option>
                                <option value="import">Data Import</option>
                                <option value="general">General Question</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority Level</label>
                            <select class="form-control" id="priority">
                                <option value="low">Low - General question or minor issue</option>
                                <option value="medium" selected>Medium - Standard support request</option>
                                <option value="high">High - Urgent issue affecting work</option>
                                <option value="critical">Critical - System down or data loss</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" placeholder="Brief description of your issue" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Detailed Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" rows="6" placeholder="Please provide as much detail as possible about your issue, including steps to reproduce if applicable" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="attachments" multiple>
                                    <label class="custom-file-label" for="attachments">Choose files...</label>
                                </div>
                            </div>
                            <small class="text-muted">You can attach screenshots, error logs, or other relevant files (max 10MB per file)</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Support Request
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Contact Information -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Support Information</h3>
                </div>
                <div class="card-body">
                    <h5><i class="fas fa-clock text-primary"></i> Support Hours</h5>
                    <p class="text-muted">Monday - Friday: 8:00 AM - 6:00 PM EST<br>
                    Saturday: 9:00 AM - 2:00 PM EST<br>
                    Sunday: Closed</p>

                    <h5><i class="fas fa-phone text-success"></i> Phone Support</h5>
                    <p class="text-muted">Call us at: <strong>(555) 123-HELP</strong><br>
                    <small>Available during support hours</small></p>

                    <h5><i class="fas fa-envelope text-warning"></i> Email Support</h5>
                    <p class="text-muted">Email: <strong>support@kkpsecurity.com</strong><br>
                    <small>We typically respond within 4 hours</small></p>
                </div>
            </div>

            <!-- Response Times -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-stopwatch"></i> Response Times</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge badge-danger">Critical</span>
                        <span>Within 1 hour</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge badge-warning">High</span>
                        <span>Within 4 hours</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge badge-info">Medium</span>
                        <span>Within 24 hours</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="badge badge-secondary">Low</span>
                        <span>Within 48 hours</span>
                    </div>
                </div>
            </div>

            <!-- Before Contacting -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lightbulb"></i> Before Contacting Support</h3>
                </div>
                <div class="card-body">
                    <p>To help us assist you faster, please:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Check the <a href="{{ route('help.faq') }}">FAQ</a> first</li>
                        <li><i class="fas fa-check text-success"></i> Review the <a href="{{ route('help.user-guide') }}">User Guide</a></li>
                        <li><i class="fas fa-check text-success"></i> Try refreshing your browser</li>
                        <li><i class="fas fa-check text-success"></i> Note any error messages</li>
                        <li><i class="fas fa-check text-success"></i> Have your account details ready</li>
                    </ul>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Emergency Contact</h3>
                </div>
                <div class="card-body">
                    <p><strong>For critical system outages or security issues:</strong></p>
                    <p class="text-center">
                        <strong class="h4">Emergency: (555) 911-HELP</strong><br>
                        <small class="text-muted">Available 24/7 for critical issues only</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.badge {
    font-size: 0.8em;
    min-width: 60px;
}
.d-flex {
    display: flex;
}
.justify-content-between {
    justify-content: space-between;
}
.form-group label {
    font-weight: 600;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Custom file input
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // Form submission
    $('form').on('submit', function(e) {
        e.preventDefault();

        // Basic validation
        var name = $('#name').val();
        var email = $('#email').val();
        var category = $('#category').val();
        var subject = $('#subject').val();
        var description = $('#description').val();

        if (!name || !email || !category || !subject || !description) {
            alert('Please fill in all required fields.');
            return;
        }

        // Simulate form submission
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();

        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        $submitBtn.prop('disabled', true);

        setTimeout(function() {
            alert('Support request submitted successfully! You will receive a confirmation email shortly.');
            $('form')[0].reset();
            $('.custom-file-label').removeClass('selected').html('Choose files...');
            $submitBtn.html(originalText);
            $submitBtn.prop('disabled', false);
        }, 2000);
    });

    // Priority level color coding
    $('#priority').on('change', function() {
        var priority = $(this).val();
        $(this).removeClass('border-success border-warning border-danger border-dark');

        switch(priority) {
            case 'low':
                $(this).addClass('border-success');
                break;
            case 'medium':
                $(this).addClass('border-warning');
                break;
            case 'high':
                $(this).addClass('border-danger');
                break;
            case 'critical':
                $(this).addClass('border-dark');
                break;
        }
    });

    // Initialize priority color
    $('#priority').trigger('change');
});
</script>
@stop
