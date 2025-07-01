@extends('adminlte::page')

@section('title', 'Documentation - Help Center')

@section('content_header')
    <h1>
        <i class="fas fa-file-alt"></i> System Documentation
        <small>Technical documentation and system information</small>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('help.index') }}">Help Center</a></li>
            <li class="breadcrumb-item active">Documentation</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- System Information -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> System Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>System Name</strong></td>
                                    <td>KKP Security Project Tracker</td>
                                </tr>
                                <tr>
                                    <td><strong>Version</strong></td>
                                    <td>2.0.1</td>
                                </tr>
                                <tr>
                                    <td><strong>Framework</strong></td>
                                    <td>Laravel 10.x</td>
                                </tr>
                                <tr>
                                    <td><strong>Database</strong></td>
                                    <td>SQLite / MySQL</td>
                                </tr>
                                <tr>
                                    <td><strong>Admin Interface</strong></td>
                                    <td>AdminLTE 3.x</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Last Updated</strong></td>
                                    <td>{{ date('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Browser Support</strong></td>
                                    <td>Chrome, Firefox, Edge, Safari</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile Support</strong></td>
                                    <td>Responsive Design</td>
                                </tr>
                                <tr>
                                    <td><strong>API Version</strong></td>
                                    <td>v1.0</td>
                                </tr>
                                <tr>
                                    <td><strong>Security</strong></td>
                                    <td>SSL/TLS, CSRF Protection</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- System Architecture -->
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sitemap"></i> System Architecture</h3>
                </div>
                <div class="card-body">
                    <h5>Technology Stack:</h5>
                    <ul>
                        <li><strong>Backend:</strong> PHP 8.1+ with Laravel Framework</li>
                        <li><strong>Frontend:</strong> Bootstrap 4 with AdminLTE Theme</li>
                        <li><strong>Database:</strong> SQLite (development) / MySQL (production)</li>
                        <li><strong>JavaScript:</strong> jQuery, DataTables, Charts.js</li>
                        <li><strong>Maps:</strong> Google Maps API Integration</li>
                    </ul>

                    <h5>Key Components:</h5>
                    <ul>
                        <li><strong>Authentication:</strong> Laravel Sanctum</li>
                        <li><strong>Authorization:</strong> Laravel Gates & Policies</li>
                        <li><strong>File Storage:</strong> Local/Cloud Storage</li>
                        <li><strong>Export/Import:</strong> Laravel Excel</li>
                        <li><strong>Data Tables:</strong> Server-side Processing</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Database Schema -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-database"></i> Database Schema</h3>
                </div>
                <div class="card-body">
                    <h5>Main Tables:</h5>
                    <ul>
                        <li><strong>users:</strong> User accounts and authentication</li>
                        <li><strong>hb837_projects:</strong> Project management data</li>
                        <li><strong>consultants:</strong> Consultant information</li>
                        <li><strong>plots:</strong> Geographic plot data</li>
                        <li><strong>activity_logs:</strong> System activity tracking</li>
                    </ul>

                    <h5>Relationships:</h5>
                    <ul>
                        <li><strong>Users → Projects:</strong> One-to-Many</li>
                        <li><strong>Projects → Consultants:</strong> Many-to-Many</li>
                        <li><strong>Projects → Plots:</strong> One-to-Many</li>
                        <li><strong>Users → Activity Logs:</strong> One-to-Many</li>
                    </ul>

                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt"></i> <strong>Security:</strong> All sensitive data is encrypted at rest and in transit.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Security Features -->
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shield-alt"></i> Security Features</h3>
                </div>
                <div class="card-body">
                    <h5>Authentication & Authorization:</h5>
                    <ul>
                        <li><strong>Secure Login:</strong> Bcrypt password hashing</li>
                        <li><strong>Session Management:</strong> Secure session handling</li>
                        <li><strong>Role-based Access:</strong> User roles and permissions</li>
                        <li><strong>CSRF Protection:</strong> Cross-site request forgery protection</li>
                    </ul>

                    <h5>Data Protection:</h5>
                    <ul>
                        <li><strong>SQL Injection Protection:</strong> Eloquent ORM with prepared statements</li>
                        <li><strong>XSS Prevention:</strong> Input sanitization and output escaping</li>
                        <li><strong>File Upload Security:</strong> File type validation and scanning</li>
                        <li><strong>Rate Limiting:</strong> API and form submission limits</li>
                    </ul>

                    <h5>Audit & Monitoring:</h5>
                    <ul>
                        <li><strong>Activity Logging:</strong> Complete user action logging</li>
                        <li><strong>Failed Login Tracking:</strong> Security breach monitoring</li>
                        <li><strong>Data Change Tracking:</strong> Audit trail for all data modifications</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- API Documentation -->
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code"></i> API Documentation</h3>
                </div>
                <div class="card-body">
                    <h5>API Endpoints:</h5>
                    <ul>
                        <li><strong>Authentication:</strong> /api/auth/*</li>
                        <li><strong>Projects:</strong> /api/projects/*</li>
                        <li><strong>Consultants:</strong> /api/consultants/*</li>
                        <li><strong>Maps:</strong> /api/maps/*</li>
                        <li><strong>Reports:</strong> /api/reports/*</li>
                    </ul>

                    <h5>Response Format:</h5>
                    <pre class="bg-light p-2 rounded">
{
  "status": "success|error",
  "message": "Response message",
  "data": {...},
  "meta": {
    "pagination": {...}
  }
}
                    </pre>

                    <h5>Authentication:</h5>
                    <p>API uses Bearer token authentication. Include the token in the Authorization header:</p>
                    <pre class="bg-light p-2 rounded">
Authorization: Bearer your_api_token
                    </pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Log -->
    <div class="row">
        <div class="col-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Change Log</h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-inverse">
                        <div class="time-label">
                            <span class="bg-success">Version 2.0.1</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-success"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> {{ date('M j, Y') }}</span>
                                <h3 class="timeline-header">Help Center Implementation</h3>
                                <div class="timeline-body">
                                    <ul>
                                        <li>Added comprehensive Help Center with user guides</li>
                                        <li>Implemented FAQ system with search functionality</li>
                                        <li>Created contact support system</li>
                                        <li>Added getting started guide for new users</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="time-label">
                            <span class="bg-primary">Version 2.0.0</span>
                        </div>
                        <div>
                            <i class="fas fa-star bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> Dec 15, 2024</span>
                                <h3 class="timeline-header">Major System Upgrade</h3>
                                <div class="timeline-body">
                                    <ul>
                                        <li>Enhanced HB837 project management system</li>
                                        <li>Improved consultant records management</li>
                                        <li>Advanced mapping and plotting capabilities</li>
                                        <li>Smart import/export functionality</li>
                                        <li>Enhanced security and performance</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="time-label">
                            <span class="bg-warning">Version 1.5.3</span>
                        </div>
                        <div>
                            <i class="fas fa-bug bg-warning"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> Nov 30, 2024</span>
                                <h3 class="timeline-header">Bug Fixes & Improvements</h3>
                                <div class="timeline-body">
                                    <ul>
                                        <li>Fixed DataTables AJAX error handling</li>
                                        <li>Improved SQL query performance</li>
                                        <li>Enhanced user interface responsiveness</li>
                                        <li>Fixed JavaScript syntax errors</li>
                                    </ul>
                                </div>
                            </div>
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
.timeline-inverse .timeline-item .timeline-header {
    color: #333;
}
pre {
    font-size: 0.85em;
}
.table td {
    padding: 8px;
}
.alert {
    margin-top: 15px;
}
</style>
@stop
