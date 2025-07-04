@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">CSRF Token Test</div>
                <div class="card-body">
                    <h5>Current CSRF Token Information:</h5>
                    <ul>
                        <li><strong>Meta Tag Token:</strong> <code id="meta-token">{{ csrf_token() }}</code></li>
                        <li><strong>Form Token:</strong> <code id="form-token">{{ csrf_token() }}</code></li>
                        <li><strong>Session ID:</strong> <code>{{ session()->getId() }}</code></li>
                    </ul>

                    <hr>

                    <h5>Test Form (No JavaScript):</h5>
                    <form method="POST" action="{{ route('admin.csrf.test.submit') }}" id="basicForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="test_input">Test Input:</label>
                            <input type="text" name="test_input" id="test_input" class="form-control" value="Basic test">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Basic Test</button>
                    </form>

                    <hr>

                    <h5>Test Form (With Token Refresh):</h5>
                    <form method="POST" action="{{ route('admin.csrf.test.submit') }}" id="refreshForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="test_input2">Test Input:</label>
                            <input type="text" name="test_input" id="test_input2" class="form-control" value="Refresh test">
                        </div>
                        <button type="submit" class="btn btn-success">Submit With Token Refresh</button>
                    </form>

                    <hr>

                    <h5>AJAX Test:</h5>
                    <button type="button" class="btn btn-info" id="ajaxTest">Test AJAX Request</button>

                    <hr>

                    <h5>Test Results:</h5>
                    <div id="results" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('CSRF Test page loaded');
    console.log('Meta CSRF token:', $('meta[name="csrf-token"]').attr('content'));

    // Update display
    $('#meta-token').text($('meta[name="csrf-token"]').attr('content'));
    $('#form-token').text($('input[name="_token"]').first().val());

    // Form with token refresh
    $('#refreshForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Refresh form submitted - getting fresh token...');

        $.get('{{ route("admin.csrf.token") }}')
            .done(function(data) {
                console.log('Fresh token received:', data.token);
                // Update token in form
                $('#refreshForm input[name="_token"]').val(data.token);
                // Update meta tag
                $('meta[name="csrf-token"]').attr('content', data.token);

                // Submit via AJAX to see response
                $.ajax({
                    url: '{{ route("admin.csrf.test.submit") }}',
                    method: 'POST',
                    data: $('#refreshForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': data.token
                    }
                })
                .done(function(response) {
                    $('#results').html('<div class="alert alert-success">Refresh Form Success: ' + JSON.stringify(response, null, 2) + '</div>');
                })
                .fail(function(xhr) {
                    $('#results').html('<div class="alert alert-danger">Refresh Form Failed: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
                });
            })
            .fail(function(xhr) {
                console.error('Failed to get fresh token:', xhr);
                $('#results').html('<div class="alert alert-danger">Token refresh failed: ' + xhr.status + '</div>');
            });
    });

    // AJAX test button
    $('#ajaxTest').on('click', function() {
        console.log('AJAX test clicked');
        $.ajax({
            url: '{{ route("admin.csrf.test.submit") }}',
            method: 'POST',
            data: {
                test_input: 'AJAX test',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            $('#results').html('<div class="alert alert-success">AJAX Success: ' + JSON.stringify(response, null, 2) + '</div>');
        })
        .fail(function(xhr) {
            $('#results').html('<div class="alert alert-danger">AJAX Failed: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
        });
    });

    // Refresh token display every 5 seconds
    setInterval(function() {
        $.get('{{ route("admin.csrf.token") }}')
            .done(function(data) {
                $('#meta-token').text(data.token);
                console.log('Token refreshed:', data.token);
            });
    }, 5000);
});
</script>
@endsection
