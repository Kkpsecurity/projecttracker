@extends('layouts.app')

<?php
  $client = $content['client'];
?>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="message-console"></div>
            <form action="{{ route('home.detail.process') }}" role="form" id="project-form">
                @csrf
                <input type="hidden" id="id" name="id" value="{{$client->id}}">

                <div class="form-group">
                    <label for="corporate_name">Company</label>
                    <select class="form-control" id="corporate_name" name="corporate_name" required>
                        <option value="">Select a Company</option>
                        <option {{ ($client->corporate_name == 'CIS' ? 'selected' : '') }}>CIS</option>
                        <option {{ ($client->corporate_name == 'S2' ? 'selected' : '') }}>S2</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="corporate_name">Quick Status</label>
                    <div class="col-md-12">
                        <?php $statuses = ['New Lead', 'Proposal Sent', 'Contracting Now', 'Active', 'Closed']; ?>
                        @foreach($statuses as $status)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quick_status" id="{{$status}}" value="{{$status}}"
                                    {{ ($client->quick_status == $status ? 'checked' : '') }}>
                                <label class="form-check-label" for="{{$status}}">{{$status}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="client_name">Client</label>
                    <input type="text" name="client_name" class="form-control" id="client_name" value="{{ $client->client_name }}" placeholder="Enter a Client Name">
                </div>

                <div class="form-group">
                    <label for="project_name">Project Name</label>
                    <input type="text" name="project_name" class="form-control" id="project_name" value="{{ $client->project_name }}"  placeholder="Enter a Project Name">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <textarea id="status" name="status" class="form-control" rows="4">{{ $client->status }}</textarea>
                </div>

                <div class="form-group">
                    <label for="poc">Project P.O.C</label>
                    <textarea id="poc" name="poc" class="form-control" rows="4">{{ $client->poc }}</textarea>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ $client->description }}</textarea>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 text-left">
                            <button type="button" class="btn btn-outline-danger" id="cancel">Delete Project</button>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary" id="create_project-trigger">Save Changes</button>
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

        const CreateTrigger = $('#create_project-trigger');
        const DeleteTrigger = $('#cancel');
        const ProjectForm = $('form#project-form');
        ProjectForm.show();

        CreateTrigger.on('click', function(e) {
            e.preventDefault();
            const Message = $('div.message-console');
            ProjectForm.slideUp(400);

            $.ajax({
                type: "POST",
                url: ProjectForm.attr('action'),
                data: ProjectForm.serialize(),
                dataType: 'json',
                success: function(result) {
                    if(result.success === true) {
                        Message.html(result.message);
                        Message.addClass('alert alert-success');
                        Message.fadeIn(400);
                        setTimeout(function() {
                            Message.fadeOut(400);
                            Message.html();
                            Message.removeClass('alert alert-success');
                            ProjectForm.slideDown(400);
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

        DeleteTrigger.on('click', function(e) {
            e.preventDefault();
            var txt;
            var r = confirm('Are you sure you want to delete this project!');
            if (r == true) {
                window.location.href = '{{ route('home.detail.delete', $client->id) }}'
            } else {
                alert('Action Canceled');
            }
        });
    });
</script>


@endsection















