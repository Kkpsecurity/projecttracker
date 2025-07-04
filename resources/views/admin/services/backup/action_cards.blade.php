<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0"><i class="fas fa-save me-2 text-primary"></i>Create Backup</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border-primary border-start border-3">
                    <i class="fa fa-info-circle me-2 text-primary"></i>
                    Generate a secure backup of critical HB837-related tables
                </div>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex align-items-center border-0 px-0">
                        <i class="fa fa-check-circle text-success me-3 ms-3"></i>
                        <div>
                            <strong>HB837 Projects</strong>
                            <div class="text-muted small">Core project data including timelines</div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center border-0 px-0">
                        <i class="fa fa-check-circle text-success me-3 ms-3"></i>
                        <div>
                            <strong>Consultants</strong>
                            <div class="text-muted small">Assigned personnel data</div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center border-0 px-0">
                        <i class="fa fa-check-circle text-success me-3 ms-3"></i>
                        <div>
                            <strong>Project Files</strong>
                            <div class="text-muted small">All project attachments</div>
                        </div>
                    </li>
                </ul>

                <button class="btn btn-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#backupModal">
                    <i class="fa fa-database me-2"></i>Create New Backup
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-history me-2 text-info"></i>Recent Activity</h5>
                <form action="{{ route('admin.hb837.backup.toggle_cron') }}" method="POST"
                    class="d-flex align-items-center">
                    @csrf
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="enabled" value="0">
                        <input type="checkbox" class="form-check-input" name="enabled" value="1"
                            onchange="this.form.submit()" id="cronToggle"
                            {{ cache('backup_cron_enabled', true) ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 text-muted small" for="cronToggle">Auto Backup</label>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if (cache('backup_cron_enabled', true))
                    <div class="alert alert-light border-info border-start border-3 mb-4">
                        <div class="d-flex">
                            <i class="fa fa-circle-check text-info me-2 mt-1"></i>
                            <div>
                                <strong>Auto Backup Active</strong>
                                <div class="text-muted small">
                                    Runs daily at <code>{{ config('backup.cron_time_at') }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="timeline">
                    @php
                        $recentEvents = \App\Models\ImportAudit::where('type', 'backup')->latest()->take(3)->get();
                    @endphp

                    @forelse ($recentEvents as $event)
                        <div class="timeline-item">
                            <div class="timeline-icon bg-info text-white">
                                <i class="fa fa-database"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $event->user->name ?? 'System' }}</strong>
                                    <span class="text-muted small">
                                        {{ $event->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Created backup via {{ ucfirst($event->changes['trigger'] ?? 'unknown method') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fa fa-inbox fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No recent activity found</p>
                        </div>
                    @endforelse
                </div>

                <a href="#" class="btn btn-outline-light text-dark w-100 mt-3">
                    <i class="fa fa-list me-1"></i>View All Activity
                </a>
            </div>
        </div>
    </div>
</div>
