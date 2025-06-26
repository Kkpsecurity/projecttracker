@extends('layouts.admin')

@section('title', 'ProTrack Projects')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">ProTrack Projects</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">ProTrack Projects</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    @php 
        $clients = $content['clients']; 
        $active_tab = Request()->segment(4) ?? 'opp';
        $tabs = [
            'opp' => [
                'icon' => 'fa fa-file',
                'name' => 'Opportunities Pipeline',
                'class' => 'btn-info'
            ],
            'active' => [
                'icon' => 'fa fa-check',
                'name' => 'Active Projects',
                'class' => 'btn-success'
            ],
            'closed' => [
                'icon' => 'fa fa-ban',
                'name' => 'Closed Projects',
                'class' => 'btn-secondary'
            ],
            'completed' => [
                'icon' => 'fa fa-star',
                'name' => 'Completed Projects',
                'class' => 'btn-primary'
            ],
        ];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="{{ $tabs[$active_tab]['icon'] }}"></i>
                                {{ $tabs[$active_tab]['name'] }}
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create_project">
                                <i class="fas fa-plus"></i> Create New Project
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($tabs as $key => $tab)
                                <li class="nav-item">
                                    <a href="{{ route('admin.home.tabs', ['tab' => $key]) }}" 
                                       class="nav-link {{ $active_tab == $key ? 'active' : '' }}">
                                        <i class="{{ $tab['icon'] }}"></i>
                                        {{ $tab['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Projects Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Corporate Name</th>
                                    <th>Client Name</th>
                                    <th>Project Name</th>
                                    <th>Status</th>
                                    <th>Quick Status</th>
                                    <th>Updated</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td><strong>{{ $client->corporate_name }}</strong></td>
                                        <td>{{ $client->client_name }}</td>
                                        <td>{{ $client->project_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $client->status == 'Active' ? 'success' : 'secondary' }}">
                                                {{ $client->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $client->quick_status }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $client->updated_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.home.detail', $client->id) }}" 
                                                   class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.home.detail', $client->id) }}" 
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.home.detail.delete', $client->id) }}" 
                                                   class="btn btn-danger btn-sm btn-delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <br>
                                            No projects found for this category.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($clients->hasPages())
                    <div class="card-footer">
                        {{ $clients->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Project Modal -->
    <div class="modal fade" id="create_project" tabindex="-1" role="dialog" aria-labelledby="createProjectLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.home.process_new') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProjectLabel">
                            <i class="fas fa-plus-circle"></i> Create New Project
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="corporate_name">Corporate Name <span class="text-danger">*</span></label>
                                    <input type="text" name="corporate_name" id="corporate_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_name">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" name="client_name" id="client_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="project_name">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" name="project_name" id="project_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="New Lead">New Lead</option>
                                        <option value="Proposal Sent">Proposal Sent</option>
                                        <option value="Contracting Now">Contracting Now</option>
                                        <option value="Active">Active</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quick_status">Quick Status</label>
                            <input type="text" name="quick_status" id="quick_status" class="form-control" 
                                   placeholder="Brief status description">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Project
                        </button>
                    </div>
                </form>
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
        
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .btn-group-sm > .btn {
            margin: 0 1px;
        }
    </style>
@stop

@section('custom_js')
    <script>
        $(document).ready(function() {
            // Auto-focus on first input when modal opens
            $('#create_project').on('shown.bs.modal', function () {
                $('#corporate_name').focus();
            });
            
            // Handle form submission
            $('#create_project form').on('submit', function(e) {
                let hasErrors = false;
                
                // Simple validation
                $(this).find('[required]').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        hasErrors = true;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    toastr.error('Please fill in all required fields.');
                }
            });
            
            // Remove validation styling on input
            $('#create_project input, #create_project select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
@stop
