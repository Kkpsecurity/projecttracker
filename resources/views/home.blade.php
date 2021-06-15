@extends('layouts.app')

<?php
  $clients = $content['clients'];
?>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2>Projects</h2>
            <span><a href="#" data-toggle="modal" data-target="#create_project" class="btn btn-success"><i class="fa fa-plus"></i> Create a New Project</a></span>
        </div>
        <div class="col-md-12 pt-3 m-t-20">
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
                    @if(count($clients) > 0)
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{ $client->corporate_name }}</td>
                                <td>{{ $client->client_name }}</td>
                                <td><a href="{{ url('home/detail', $client->id) }}"><strong>{{ $client->project_name }}</strong></a></td>
                                <td>{{ $client->quick_status  }}</td>
                                <td style="width: 180px; text-align: right;">{{ $client->created_at->format('M d Y H:i:s')  }}</td>
                                <td style="width: 180px; text-align: right;">{{ $client->updated_at->format('M d Y H:i:s')  }}</td>
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
<div class="modal fade" id="create_project" tabindex="-1" role="dialog" aria-labelledby="CreateProjectModal" aria-hidden="true">
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
                <form action="{{ route('home.process') }}" id="project-form" role="form">
                   @csrf
                   <div class="form-group">
                       <label for="corporate_name">Company</label>
                       <select class="form-control" is="corporate_name" name="corporate_name" required>
                           <option value="">Select a Company</option>
                           <option>CIS</option>
                           <option>S2</option>
                       </select>
                   </div>

                   <div class="form-group">
                       <label for="corporate_name">Quick Status</label>
                       <div class="col-md-12">
                           <?php $statuses = ['New Lead', 'Proposal Sent', 'Contracting Now', 'Active', 'Closed']; ?>
                           @foreach($statuses as $status)
                               <div class="form-check form-check-inline">
                                   <input class="form-check-input" type="radio" name="quick_status" id="{{$status}}" value="{{$status}}">
                                   <label class="form-check-label" for="{{$status}}">{{$status}}</label>
                               </div>
                           @endforeach
                       </div>
                   </div>

                   <div class="form-group">
                       <label for="client_name">Client</label>
                       <input type="client_name" name="client_name" class="form-control" id="client_name" placeholder="Enter a Client Name">
                   </div>

                   <div class="form-group">
                       <label for="project_name">Project Name</label>
                       <input type="project_name" name="project_name" class="form-control" id="project_name" placeholder="Enter a Project Name">
                   </div>

                   <div class="form-group">
                       <label for="status">Status</label>
                       <textarea id="status" name="status" class="form-control" rows="4"></textarea>
                   </div>

                   <div class="form-group">
                       <label for="poc">Project P.O.C</label>
                       <textarea id="poc" name="poc" class="form-control" rows="4"></textarea>
                   </div>

                   <div class="form-group">
                       <label for="description">Description</label>
                       <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                   </div>
               </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="create_project-trigger">Save changes</button>
            </div>
        </div>


    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {

        const CreateTrigger = $('#create_project-trigger');
        const ProjectForm = $('form#project-form');
        ProjectForm.show();

        CreateTrigger.on('click', function() {
            const Message = $('div.message-console');

            ProjectForm.slideUp(400);
            $('.modal-footer').fadeOut(400);


            $.ajax({
                type: "POST",
                url: ProjectForm.attr('action'),
                data:  ProjectForm.serialize(),
                dataType: 'json',
                success: function(result) {
                    if(result.success === true) {
                        Message.html(result.message);
                        Message.addClass('alert alert-success');
                        Message.fadeIn(400);
                        setTimeout(function() {
                          //  window.location.href = '{{ url('home') }}'
                        }, 3000);
                    } else {
                        alert('RESULT ERROR: ' + result.message);
                    }
                },
                error: function(result)
                {
                    alert('ERROR: ' + result.message);
                },
                always: function(result)
                {
                    alert('Some thing went wrong, But we are not sure what. This error has been logged');
                }
            });


        });

    });
</script>


@endsection















