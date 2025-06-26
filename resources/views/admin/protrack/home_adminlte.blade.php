@extends('layouts.admin')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">ProTrack Projects</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Projects</li>
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
                'icon' => 'fa fa-file-alt',
                'name' => 'Opportunities Pipeline',
                'color' => 'info',
            ],
            'active' => [
                'icon' => 'fa fa-play-circle',
                'name' => 'Active Projects',
                'color' => 'success',
            ],
            'closed' => [
                'icon' => 'fa fa-times-circle',
                'name' => 'Closed Projects',
                'color' => 'danger',
            ],
            'completed' => [
                'icon' => 'fa fa-check-circle',
                'name' => 'Completed Projects',
                'color' => 'primary',
            ],
        ];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3 class="card-title">
                                <i class="{{ $tabs[$active_tab]['icon'] }}"></i>
                                {{ $tabs[$active_tab]['name'] }}
                            </h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create_project">
                                <i class="fas fa-plus"></i> Create New Project
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            @foreach($tabs as $key => $tab)
                                <li class="nav-item">
                                    <a href="{{ route('admin.home.tabs', ['tab' => $key]) }}" 
                                       class="nav-link {{ $active_tab == $key ? 'active' : '' }}">
                                        <i class="{{ $tab['icon'] }}"></i> {{ $tab['name'] }}
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
                                    <th width="5%">#</th>
                                    <th width="20%">Corporate Name</th>
                                    <th width="20%">Client Name</th>
                                    <th width="25%">Project Name</th>
                                    <th width="15%">Status</th>
                                    <th width="10%">Created</th>
                                    <th width="5%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td>
                                            <strong>{{ $client->corporate_name }}</strong>
                                        </td>
                                        <td>{{ $client->client_name }}</td>
                                        <td>
                                            <a href="{{ route('admin.home.detail', $client->id) }}" 
                                               class="text-primary">
                                                {{ $client->project_name }}
                                            </a>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = '';
                                                switch(strtolower($client->quick_status ?? $client->status)) {
                                                    case 'active':
                                                        $statusClass = 'success';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'primary';
                                                        break;
                                                    case 'closed':
                                                        $statusClass = 'danger';
                                                        break;
                                                    default:
                                                        $statusClass = 'info';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">
                                                {{ $client->quick_status ?? $client->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $client->created_at->format('M j, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.home.detail', $client->id) }}" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   data-toggle="tooltip" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.home.detail.delete', $client->id) }}" 
                                                   class="btn btn-sm btn-outline-danger btn-delete"
                                                   data-toggle="tooltip" title="Delete Project">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h4>No projects found</h4>
                                                <p>No projects match the current filter criteria.</p>
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create_project">
                                                    <i class="fas fa-plus"></i> Create Your First Project
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($clients->hasPages())
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} 
                                    of {{ $clients->total() }} results
                                </div>
                            </div>
                            <div class="col-sm-6">
                                {{ $clients->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Project Modal -->
    <div class="modal fade" id="create_project" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.home.process_new') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <i class="fas fa-plus"></i> Create New Project
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="corporate_name">Corporate Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="corporate_name" name="corporate_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_name">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="client_name" name="client_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="project_name">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_name" name="project_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quick_status">Quick Status</label>
                                    <select class="form-control" id="quick_status" name="quick_status">
                                        <option value="">Select Quick Status</option>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('custom_js')
<script>
$(document).ready(function() {
    // Auto-sync status fields
    $('#status').change(function() {
        if (!$('#quick_status').val()) {
            $('#quick_status').val($(this).val());
        }
    });
    
    // Confirm project deletion
    $('.btn-delete').click(function(e) {
        if (!confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@stop
