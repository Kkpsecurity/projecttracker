@extends('layouts.app')

@php
    $client = $content['client'];
@endphp

@section('styles')
    <style>
        /* General Tab Styles */
        .nav-tabs {
            border-bottom: 2px solid #ddd;
        }

        .nav-tabs .nav-link {
            color: #555;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 4px 4px 0 0;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }

        .nav-tabs .nav-link:hover {
            background: #e9ecef;
            color: #000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav-tabs .nav-link.active {
            background: #333;
            color: #fff;
            border-color: #333;
            cursor: default;
        }

        /* Tab Content Styles */
        .tab-content {
            border: 1px solid #ddd;
            border-radius: 0 0 4px 4px;
            padding: 20px;
            background: #fff;
            margin-top: -1px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .tab-pane h3 {
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 40px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            color: #555;
        }

        .form-control {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-control::placeholder {
            color: #aaa;
            font-style: italic;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: scale(1.02);
        }

        button[type="submit"]:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Row Styling for Side-by-Side Fields */
        .form-group .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .form-group .row>div {
            flex: 0 0 48%;
            /* Adjust for spacing between fields */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-tabs .nav-link {
                font-size: 12px;
                padding: 8px 10px;
            }

            .form-group .row>div {
                flex: 0 0 100%;
                /* Stack fields on smaller screens */
            }
        }
    </style>
@endsection


@section('content')
    <div class="container bg-light shadow-sm p-3 mb-5 rounded">
        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
                <div class="message-console"></div>

                <form action="{{ route('admin.home.detail.update') }}" role="form" id="project-form" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="id" name="id" value="{{ $client->id }}">

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="corporate_name">Company</label>
                        </div>
                        <div class="col-9">
                            <select class="form-control" id="corporate_name" name="corporate_name" required>
                                <option value="">{{ __('Select a Company') }}</option>
                                <option {{ $client->corporate_name == 'CIS' ? 'selected' : '' }}>CIS</option>
                                <option {{ $client->corporate_name == 'S2' ? 'selected' : '' }}>S2</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 p-2">
                        <div class="form-group">
                            <label for="quick_status">Quick Status</label>
                            <div class="col-md-12">
                                @php
                                    $quickStatuses = [
                                        'New Lead',
                                        'Proposal Sent',
                                        'Contracting Now',
                                        'Active',
                                        'Closed',
                                        'Completed',
                                    ];
                                @endphp
                                @foreach ($quickStatuses as $qstatus)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input mr-2" type="radio" name="quick_status"
                                            id="{{ strtolower(str_replace(' ', '_', $qstatus)) }}"
                                            value="{{ $qstatus }}"
                                            {{ $client->quick_status == $qstatus ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="{{ strtolower(str_replace(' ', '_', $qstatus)) }}">{{ $qstatus }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="client_name">Client</label>
                        </div>
                        <div class="col-9"><input type="text" name="client_name" class="form-control" id="client_name"
                                value="{{ $client->client_name }}" placeholder="Enter a Client Name">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="project_name">Project Name</label>
                        </div>
                        <div class="col-9">
                            <input type="text" name="project_name" class="form-control" id="project_name"
                                value="{{ $client->project_name }}" placeholder="Enter a Project Name">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="status">Status</label>
                        </div>
                        <div class="col-9">
                            <textarea id="status" name="status" class="form-control" rows="4">{{ $client->status }}</textarea>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="poc">Project P.O.C</label>
                        </div>
                        <div class="col-9">
                            <textarea id="poc" name="poc" class="form-control" rows="4">{{ $client->poc }}</textarea>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="description">Description</label>
                        </div>
                        <div class="col-9">
                            <textarea id="description" name="description" class="form-control" rows="4">{{ $client->description }}</textarea>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-12">
                            <h3>Project Value</h3>
                        </div>

                        <!-- Total Projected Services -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_services_total">Total Projected Services</label>
                                <input type="text" value="{{ $client->project_services_total }}"
                                    name="project_services_total" class="form-control" id="project_services_total"
                                    placeholder="$0.00">
                            </div>
                        </div>

                        <!-- Total Projected Expenses -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_expenses_total">Total Projected Expenses</label>
                                <input type="text" value="{{ $client->project_expenses_total }}"
                                    name="project_expenses_total" class="form-control" id="project_expenses_total"
                                    placeholder="$0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="final_services_total">Total Final Expenses</label>
                        </div>

                        <div class="col-9">
                            <input type="text" value="{{ $client->final_services_total }}"
                                name="final_services_total" class="form-control" id="final_services_total"
                                placeholder="$0.00">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3">
                            <label for="final_billing_total">Total Final Billing</label>
                        </div>
                        <div class="col-9">
                            <input type="text" value="{{ $client->final_billing_total }}" name="final_billing_total"
                                class="form-control" id="final_billing_total" placeholder="$0.00">
                        </div>
                    </div>

                    <h3>{{ __('Project Files') }}</h3>
                    <hr>

                    <div class="form-group">
                        @if ($client->file1)
                            <a href="{{ url('admin/home/detach/file1/' . $client->id) }}"
                                class="btn btn-sm btn-danger mr-1"><i class="fa fa-trash"></i></a>
                            <a href="{{ url('admin/home/download/' . $client->file1) }}">{{ $client->file1 }}</a>
                        @else
                            <input type="file" name="file1" class="form-control" id="file1" />
                        @endif
                    </div>

                    <div class="form-group">
                        @if ($client->file2)
                            <a href="{{ url('admin/home/detach/file2/' . $client->id) }}"
                                class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                            <a href="{{ url('admin./home/download/' . $client->file2) }}">{{ $client->file2 }}</a>
                        @else
                            <input type="file" name="file2" class="form-control" id="file2" />
                        @endif
                    </div>

                    <div class="form-group">
                        @if ($client->file3)
                            <a href="{{ url('admin/home/detach/file3/' . $client->id) }}"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a href="{{ url('admin/home/download/' . $client->file3) }}">
                                {{ $client->file3 }}
                            </a>
                        @else
                            <input type="file" name="file3" class="form-control" id="file3" />
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <button type="button" class="btn btn-outline-danger" id="cancel">Delete
                                    Project
                                </button>
                            </div>

                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary" id="create_project-trigger">Save
                                    Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Get references to the create button, the delete button, the form, and the message console
            const CreateTrigger = $('#create_project-trigger');
            const DeleteTrigger = $('#cancel');
            const ProjectForm = $('form#project-form');
            const Message = $('div.message-console');

            // Show the form
            ProjectForm.show();

            // When the form is submitted, send the form data via AJAX
            // ProjectForm.on('submit', function(e) {
            //     e.preventDefault();
            //     ProjectForm.slideUp(400);

            //     $.ajax({
            //         type: "POST",
            //         url: ProjectForm.attr('action'),
            //         data: ProjectForm.serialize(),
            //         dataType: 'json',
            //         success: function(result) {

            //             // If the AJAX request succeeds, show a success message
            //             if(result.success === true) {
            //                 Message.html(result.message);
            //                 Message.addClass('alert alert-success');
            //                 Message.fadeIn(400);
            //                 setTimeout(function() {
            //                     Message.fadeOut(400);
            //                     Message.html('');
            //                     Message.removeClass('alert alert-success');
            //                     ProjectForm.slideDown(400);
            //                 }, 3000);

            //             // If the AJAX request fails, show an error message
            //             } else {
            //                 alert('RESULT ERROR: ' + result.message);
            //             }
            //         },
            //         error: function(result) {

            //             // If there is an AJAX error, show an error message
            //             alert('ERROR: ' + result.message);
            //         }
            //     });
            // });

            // When the quick status select box is changed, update the hidden input value
            $('#quick_status').on('change', function() {
                $('#quick_status_hidden').val($(this).val());
            });

            // When the delete button is clicked, ask for confirmation before deleting the project
            DeleteTrigger.on('click', function(e) {
                e.preventDefault();
                var txt;
                var r = confirm('Are you sure you want to delete this project?');
                if (r == true) {
                    window.location.href = '{{ route('admin.home.detail.delete', $client->id) }}';
                } else {
                    alert('Action Canceled');
                }
            });
        });
    </script>
@endsection
