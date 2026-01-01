
@php
    $active_tab = Request()->segment(4) ?? 'Active';

    $tabs = [
        'Active' => [
            'icon' => 'fa fa-file',
            'name' => 'Active',
        ],
        'Quoted' => [
            'icon' => 'fa fa-ban',
            'name' => 'Quoted',
        ],
        'Completed' => [
            'icon' => 'fa fa-check-circle',
            'name' => 'Completed',
        ],
        'Closed' => [
            'icon' => 'fa fa-times-circle',
            'name' => 'Closed',
        ],
    ];

@endphp

@section('styles')
    <style>
        .lms-tabs {
            border-radius: 0;
            background: #ccc;
        }

        .lms-tabs:hover,
        .lms-tabs.active {
            background: #333;
            color: #eee;
        }

        .lms-tabs.active {
            border-bottom: 2px solid #f00;
        }

        .container-lg {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .risk-low {
            background-color: #72b862;
            color: white;
        }

        .risk-moderate {
            background-color: #95f181;
            color: black;
        }

        .risk-elevated {
            background-color: #fae099;
            color: black;
        }

        .risk-high {
            background-color: #f2a36e;
            color: black;
        }

        .risk-severe {
            background-color: #c75845;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="container-lg">
        <div class="row justify-content-center">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h2 style="font-weight: 700; color: aliceblue">HB837 Projects</h2>
                <span>
                    <a href="{{ route('admin.consultants.index') }}" class="btn btn-primary" title="Consultants"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Consultants">
                        <i class="fa fa-users"></i> Consultants
                    </a>

                    <a href="{{ route('admin.hb837.create') }}" class="btn btn-success" title="Create"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Create">
                        <i class="fa fa-plus"></i> Create Record
                    </a>

                    <a href="{{ route('admin.hb837.backup.dashboard') }}" class="btn btn-info" title="Export"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Export">
                        <i class="fa fa-file"></i> Backup & Imports
                    </a>
                </span>
            </div>

            <div class="col-md-12 text-right mt-3 mb-3">
                <form action="{{ route('admin.hb837.tabs', [$active_tab]) }}" method="GET"
                    class="form-inline float-right">
                    <input type="text" name="search" class="form-control " placeholder="Search by Project Name"
                        value="{{ request('search') }}">

                    <!-- Set the number of rows -->
                    <select name="num_rows" class="form-control ml-2 mr-2" onchange="this.form.submit()">
                        <option value="10" {{ request('num_rows') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('num_rows') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('num_rows') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('num_rows') == 100 ? 'selected' : '' }}>100</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Search</button>

                </form>
            </div>

            @include('partials.messages')

            <!-- Tabs for different HB837 statuses -->
            <div class="col-md-12 pt-3 m-t-20">
                <div class="card">
                    <div class="card-header">
                        @foreach ($tabs as $tab_id => $tab)
                            <a href="{{ url('admin/hb837/tabs/' . $tab_id) }}"
                                class="lms-tabs btn btn-default {{ $tab_id == $active_tab ? 'active' : '' }}">
                                <i class="fa {{ $tab['icon'] }}"></i> @lang(ucwords($tab['name']))
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-12 ">
                @include('partials.tables.active_table', ['hb837' => $hb837])
            </div>

            <div class="col-md-12 text-right m-2" style="font-size: 18px; color: #eee;">

                <span class="pull-left">
                    Total Records:<strong> {{ $hb837->total() }}</strong>
                </span>
                <span class="pull-right">{{ $hb837->render() }}</span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // init tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
