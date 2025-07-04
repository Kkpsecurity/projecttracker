@extends('layouts.app')
@section('content')
    <div class="tabs">
        <ul class="nav nav-tabs">
            <!-- Change tab titles -->
            <li class="nav-item">
                <a class="nav-link active" href="#current" data-bs-toggle="tab">Current Assignments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#completed" data-bs-toggle="tab">Completed Projects</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="current" class="tab-pane fade show active">
                <table class="table table-striped table-dark">
                    <thead>
                        <tr>
                            <!-- Remove ID column, add "Date of Scheduled Inspection" -->
                            <th>Property Name</th>
                            <th>Date of Scheduled Inspection</th>
                            <th>Contact</th>
                            <!-- ...other columns... -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currentAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->property_name }}</td>
                                <td>{{ $assignment->scheduled_inspection_date }}</td>
                                <td>{{ $assignment->contact }}</td>
                                <!-- ...existing code... without Delete button -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="completed" class="tab-pane fade">
                <table class="table table-striped table-dark">
                    <thead>
                        <tr>
                            <!-- Remove ID column, add "Date of Scheduled Inspection" -->
                            <th>Property Name</th>
                            <th>Date of Scheduled Inspection</th>
                            <th>Contact</th>
                            <!-- ...other columns... -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedProjects->where('report_status', 'Complete') as $project)
                            <tr>
                                <td>{{ $project->property_name }}</td>
                                <td>{{ $project->scheduled_inspection_date }}</td>
                                <td>{{ $project->contact }}</td>
                                <!-- ...existing columns... excluding Delete button -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
