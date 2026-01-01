<div class="row justify-content-between align-items-start mb-3">
            <div class="col-md-6">
                <h4 class="text-white">
                    Google Maps:
                    @if ($selectedPlotId && isset($selectedPlot))
                        <strong>{{ $selectedPlot->plot_name }}</strong>
                    @elseif ($selectedMacroClient)
                        <strong>{{ Str::ucfirst($selectedMacroClient) }}</strong>
                    @else
                        <strong>Plots</strong>
                    @endif
                </h4>
            </div>

            <div class="col-md-6 text-md-end">
                <div class="btn-group" role="group" aria-label="Plot tools">
                    <a href="{{ route('admin.mapplots.index') }}" class="btn btn-primary m-1">Back</a>
                    <button type="button" class="btn btn-success m-1" data-bs-toggle="modal"
                        data-bs-target="#createPlotModal">Create a New Plot</button>

                    @if ($selectedPlotId && isset($selectedPlot))
                        <form action="{{ route('admin.mapplots.destroy', $selectedPlot->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger m-1"
                                onclick="return confirm('Are you sure you want to delete this plot?')">
                                <i class="fa fa-trash"></i> Delete Plot
                            </button>
                        </form>
                    @endif
                </div>

                @if ($selectedPlotId)
                    <div class="input-group address-forms mt-2">
                        <input type="text" 
                               id="address-input" 
                               class="form-control" 
                               placeholder="Start typing an address..."
                               autocomplete="off"
                               title="Search for addresses using Google Places"
                               aria-label="Address search with autocomplete">
                        <button id="add-address-btn" 
                                class="btn btn-secondary" 
                                type="button"
                                title="Add selected address to plot">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">
                        <i class="fa fa-info-circle"></i> 
                        Type to search addresses with Google Places autocomplete
                    </small>
                @endif

                <!-- Plot Selection -->
                <select id="plot-select" class="form-select mt-2">
                    <option value="">Select a Plot</option>
                    @foreach ($plots as $plot)
                        <option value="{{ $plot->id }}" {{ $selectedPlotId == $plot->id ? 'selected' : '' }}>
                            {{ $plot->plot_name }}
                        </option>
                    @endforeach
                </select>

                <!-- Macro Client Selection -->
                <select id="macro-client-select" class="form-select mt-2">
                    <option value="">Select Macro Address Group</option>
                    @foreach ($macroClients as $client)
                        <option value="{{ $client->macro_client }}"
                            {{ request()->get('macro_client') == $client->macro_client ? 'selected' : '' }}>
                            {{ Str::ucfirst($client->macro_client) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
