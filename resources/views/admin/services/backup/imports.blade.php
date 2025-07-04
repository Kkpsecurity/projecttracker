<div class="container">
    <h1 class="mb-4">📥 Import Audits</h1>
    <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-dark">#</th>
                    <th class="text-dark">User</th>
                    <th class="text-dark">Summary</th>
                    <th class="text-success">+ New</th>
                    <th class="text-dark">~ Updated</th>
                    <th class="text-dark">× Skipped</th>
                    <th class="text-dark">Started At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($importAudits as $audit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $audit->user->name ?? 'System' }}</td>
                        <td>
                            <span class="text-muted small">
                                Processed:
                                {{ ($audit->changes['imported'] + $audit->changes['updated'] + $audit->changes['skipped']) ?? 0 }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                {{ $audit->changes['imported'] ?? 0 }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ $audit->changes['updated'] ?? 0 }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $audit->changes['skipped'] ?? 0 }}
                            </span>
                        </td>
                        <td><i class="fa fa-calendar me-1"></i>{{ $audit->created_at ? $audit->created_at->format('Y-m-d H:i') : '-' }}</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No import audits found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($importAudits, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $importAudits->links() }}
        </div>
    @endif
</div>
