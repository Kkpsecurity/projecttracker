<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="text-white mb-0">
            <i class="fa fa-map-marked-alt me-2"></i>
            Property Map Visualization
        </h3>
        <p class="text-white-50 mb-0">
            @if ($selectedPlotId && isset($selectedPlot))
                <strong>{{ $selectedPlot->plot_name }}</strong> 
                <span class="badge bg-{{ $selectedPlot->plot_type === 'custom' ? 'success' : ($selectedPlot->plot_type === 'prospect' ? 'info' : 'warning') }}">
                    {{ ucfirst($selectedPlot->plot_type) }}
                </span>
            @elseif ($selectedMacroClient)
                <strong>{{ Str::title($selectedMacroClient) }}</strong>
                <span class="badge bg-info">Existing Client</span>
            @else
                Select a plot or existing client to view properties
            @endif
        </p>
    </div>

    <div class="col-md-6">
        <div class="d-flex flex-column gap-2">
            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="{{ route('admin.mapplots.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createPlotModal">
                    <i class="fa fa-plus"></i> New Plot
                </button>
                
                @if ($selectedPlotId && isset($selectedPlot))
                    <button type="button" class="btn btn-info btn-sm" id="edit-plot-btn" data-plot-id="{{ $selectedPlot->id }}">
                        <i class="fa fa-edit"></i> Edit Plot
                    </button>
                    
                    <button type="button" class="btn btn-outline-success btn-sm" id="export-data" title="Export Plot Data">
                        <i class="fa fa-download"></i> Export
                    </button>
                    
                    @if ($selectedPlot->plot_type === 'custom')
                        <button type="button" class="btn btn-warning btn-sm" id="convert-to-client-btn" data-plot-id="{{ $selectedPlot->id }}">
                            <i class="fa fa-arrow-up"></i> Convert to Client
                        </button>
                    @endif
                    
                    <form action="{{ route('admin.mapplots.destroy', $selectedPlot->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                onclick="return confirm('Are you sure you want to delete this plot and all its addresses?')">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
            </div>

            <!-- Selection Controls -->
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label text-white-50 small">Custom Plots & Prospects</label>
                    <select id="plot-select" class="form-select form-select-sm">
                        <option value="">Choose a plot...</option>
                        @foreach ($plots->groupBy('plot_type') as $type => $plotGroup)
                            <optgroup label="{{ ucfirst($type) }} Plots">
                                @foreach ($plotGroup as $plot)
                                    <option value="{{ $plot->id }}" 
                                            {{ $selectedPlotId == $plot->id ? 'selected' : '' }}
                                            data-type="{{ $plot->plot_type }}">
                                        {{ $plot->plot_name }}
                                        @if($plot->client_contact_name)
                                            ({{ $plot->client_contact_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label text-white-50 small">Existing Property Management Clients</label>
                    <select id="macro-client-select" class="form-select form-select-sm">
                        <option value="">Choose a client...</option>
                        @foreach ($macroClients as $client)
                            <option value="{{ $client }}" 
                                    {{ request()->get('macro_client') == $client ? 'selected' : '' }}>
                                {{ Str::title($client) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Address Input (only show when plot is selected) -->
            @if ($selectedPlotId)
                <div class="mt-2">
                    <div class="input-group input-group-sm">
                        <input type="text" id="address-input" class="form-control" 
                               placeholder="Enter address to add to this plot...">
                        <button id="add-address-btn" class="btn btn-outline-light" type="button">
                            <i class="fa fa-plus"></i> Add Address
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
