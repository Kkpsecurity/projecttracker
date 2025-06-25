@extends('layouts.app')

@php $clients = $content['clients']; @endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h2>Projects</h2>
                <span>
                    <a href="#" data-toggle="modal" data-target="#create_project" class="btn btn-success">
                        <i class="fa fa-plus"></i> Create a New Project
                    </a>
                </span>
            </div>

            <div class="col-md-12 pt-3 m-t-20">
                @php
                    $active_tab = Request()->segment(4) ?? 'opp';
                    $tabs = [
                        'opp' => [
                            'icon' => 'fa fa-file',
                            'name' => 'Opportunities Pipeline',
                        ],
                        'active' => [
                            'icon' => 'fa fa-check',
                            'name' => 'Active Projects',
                        ],
                        'closed' => [
                            'icon' => 'fa fa-ban',
                            'name' => 'Closed Projects',
                        ],
                        'completed' => [
                            'icon' => 'fa fa-star',
                            'name' => 'Completed Projects',
                        ],
                    ];
                @endphp

                <div class="card">
                    <div class="card-header">
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
                        </style>

                        @foreach ($tabs as $tab_id => $tab)
                            <a href="{{ url('admin/home/tabs/' . $tab_id) }}"
                                class="lms-tabs btn btn-default {{ $tab_id == $active_tab ? 'active' : '' }}">
                                <i class="fa {{ $tab['icon'] }}"></i> @lang(ucwords($tab['name']))
                            </a>
                        @endforeach
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover shadow" style="background: #fff;">
                    <thead>
                        <tr>
                            <th style="width: 100px;">{{ __('Company') }}</th>
                            <th style="width: 300px;">{{ __('Client') }}</th>
                            <th style="width: 260px;">{{ __('Project Name') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="width: 180px; text-align: right;">{{ __('Entered') }}</th>
                            <th style="width: 180px; text-align: right;">{{ __('Last Update') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($clients) > 0)
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client->corporate_name }}</td>
                                    <td>{{ $client->client_name }}</td>
                                    <td>
                                        <a href="{{ url('admin/home/detail/' . $client->id) }}">
                                            <strong>{{ $client->project_name }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $client->quick_status }}</td>
                                    <td style="width: 180px; text-align: right;">
                                        {{ $client->created_at->format('M d Y H:i:s') }}</td>
                                    <td style="width: 180px; text-align: right;">
                                        {{ $client->updated_at->format('M d Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">No Projects Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <span class="pull-right">{{ $clients->render() }}</span>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <dialog id="create_project" class="modal" aria-labelledby="CreateProjectModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CreateProjectModal">{{ __('Create New Project') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="message-console"></div>
                    <form action="{{ route('admin.home.process_new') }}" id="project-form" role="form">
                        @csrf
                        <div class="form-group">
                            <label for="corporate_name">Company</label>
                            <select class="form-control" name="corporate_name" required>
                                <option value="">Select a Company</option>
                                <option>CIS</option>
                                <option>S2</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quick_status">Quick Status</label>
                            <div class="col-md-12">
                                <?php
                                $quick_statuses = ['New Lead', 'Proposal Sent', 'Contracting Now', 'Active', 'Closed', 'Completed']; ?>
                                @foreach ($quick_statuses as $qstatus)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="quick_status"
                                            id="{{ $qstatus }}" value="{{ $qstatus }}">
                                        <label class="form-check-label"
                                            for="{{ $qstatus }}">{{ $qstatus }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_name">Client</label>
                            <input type="text" name="client_name" class="form-control" id="client_name"
                                placeholder="Enter a Client Name" required>
                        </div>

                        <div class="form-group">
                            <label for="project_name">Project Name</label>
                            <input type="text" name="project_name" class="form-control" id="project_name"
                                placeholder="Enter a Project Name" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <textarea id="status" name="status" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="poc">Project P.O.C</label>
                            <textarea id="poc" name="poc" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required></textarea
                                    <div class="form-group">
                                <label for="project_services_total">Total Projected Services</label>
                                <input type="number" name="project_services_total" class="form-control" id="project_services_total" placeholder="$0.00" required>
                            </div>

                            <div class="form-group">
                                <label for="project_expenses_total">Total Projected Expenses</label>
                                <input type="number" name="project_expenses_total" class="form-control" id="project_expenses_total" placeholder="$0.00" required>
                            </div>

                            <div class="form-group">
                                <label for="final_services_total">Total Final Expenses</label>
                                <input type="number" name="final_services_total" class="form-control" id="final_services_total" placeholder="$0.00" required>
                            </div>


                            <div class="form-group">
                                <label for="final_billing_total">Total Final Billing</label>
                                <input type="number" name="final_billing_total" class="form-control" id="final_billing_total" placeholder="$0.00" required>
                            </div>

                       </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="create_project-trigger">Save changes</button>
                    </div>
                </div>
            </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Get references to the create button, the form, and the message console
            const CreateTrigger = $('#create_project-trigger');
            const ProjectForm = $('form#project-form');
            const Message = $('div.message-console');

            // Show the form
            ProjectForm.show();

            // When the create button is clicked, send the form data via AJAX
            CreateTrigger.on('click', function() {

                // Hide the form and the modal footer
                ProjectForm.slideUp(400);
                $('.modal-footer').fadeOut(400);

                // Send the form data via AJAX
                $.post(ProjectForm.attr('action'), ProjectForm.serialize(), 'json')
                    .done(function(result) {

                        // If the AJAX request succeeds, show a success message
                        if (result.success === true) {
                            Message.html(result.message);
                            Message.addClass('alert alert-success');
                            Message.fadeIn(400);
                            setTimeout(function() {
                                Message.html('');
                                Message.removeClass('alert alert-success');
                                Message.fadeOut(400);
                                ProjectForm.trigger('reset');
                                window.location.reload();
                            }, 3000);

                            // If the AJAX request fails, show an error message and hide the form
                        } else {
                            alert('RESULT ERROR: ' + result.message);
                            ProjectForm.slideUp(400);
                        }
                    })
                    .fail(function(result) {

                        // If there is an AJAX error, log it and show an error message, then show the form again
                        console.error('ERROR:', result);
                        alert(
                            'Something went wrong, but we are not sure what. This error has been logged'
                        );
                        ProjectForm.slideDown(400);
                    });
            });
        });
    </script>
@endsection
