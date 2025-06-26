<div class="table-responsive">
    <table class="table table-hover table-striped" id="consultants-table">
        <thead class="table-dark">
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th style="width: 140px;">FCP Expiration</th>
                <th style="width: 140px;">Light Meter</th>
                <th style="width: 110px;">Bonus Rate</th>
                <th style="width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consultants as $consultant)
                <tr>
                    <td>
                        <span class="badge bg-secondary">#{{ $consultant->id }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <strong class="text-dark">{{ $consultant->first_name }} {{ $consultant->last_name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($consultant->email)
                            <a href="mailto:{{ $consultant->email }}" class="text-primary text-decoration-none">
                                <i class="fas fa-envelope me-1"></i>{{ $consultant->email }}
                            </a>
                        @else
                            <span class="text-muted fst-italic">No email</span>
                        @endif
                    </td>
                    <td>
                        @if($consultant->dba_company_name)
                            <span class="text-dark">{{ $consultant->dba_company_name }}</span>
                        @else
                            <span class="text-muted fst-italic">No company</span>
                        @endif
                    </td>
                    <td>
                        @if($consultant->fcp_expiration_date)
                            <div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($consultant->fcp_expiration_date)->format('M d, Y') }}</small>
                                <br>
                                @if(\Carbon\Carbon::parse($consultant->fcp_expiration_date)->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif(\Carbon\Carbon::parse($consultant->fcp_expiration_date)->diffInDays() <= 30)
                                    <span class="badge bg-warning">Expiring Soon</span>
                                @else
                                    <span class="badge bg-success">Valid</span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted fst-italic">Not set</span>
                        @endif
                    </td>
                    <td>
                        @if($consultant->assigned_light_meter)
                            <div>
                                <span class="badge bg-info">{{ $consultant->assigned_light_meter }}</span>
                                @if($consultant->lm_nist_expiration_date)
                                    <br>
                                    @if(\Carbon\Carbon::parse($consultant->lm_nist_expiration_date)->isPast())
                                        <span class="badge bg-danger">NIST Expired</span>
                                    @else
                                        <span class="badge bg-success">NIST Valid</span>
                                    @endif
                                @endif
                            </div>
                        @else
                            <span class="text-muted fst-italic">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @if($consultant->subcontractor_bonus_rate)
                            <span class="badge bg-success fs-6">${{ number_format($consultant->subcontractor_bonus_rate, 2) }}</span>
                        @else
                            <span class="text-muted fst-italic">Not set</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.consultants.edit', $consultant->id) }}" 
                               class="btn btn-outline-primary btn-sm" 
                               title="Edit Consultant">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.consultants.destroy', $consultant) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-outline-danger btn-sm" 
                                        title="Delete Consultant" 
                                        onclick="return confirm('Are you sure you want to delete this consultant?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-user-tie fa-3x mb-3 d-block"></i>
                            <h5>No consultants found</h5>
                            <p class="mb-0">Start by adding your first consultant.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($consultants->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $consultants->links() }}
    </div>
@endif

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group-sm > .btn {
    padding: 0.375rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.25rem;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
}
</style>
