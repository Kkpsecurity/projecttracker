@extends('layouts.app')

@section('content')
    <div class="container bg-light p-4 rounded shadow">
        <div class="row mb-4">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2 class="text-primary">Edit User</h2>
                <a class="btn btn-secondary" href="{{ url('admin/users') }}">Back</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>There were some problems with your input:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('admin/users/' . $user->id) }}" method="POST" class="p-3 bg-white rounded border">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="name" class="form-label"><strong>Name:</strong></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}"
                    placeholder="Name">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><strong>Email:</strong></label>
                <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}"
                    placeholder="Email">
            </div>

            <div class="mb-3">
                <div class="form-check mb-2">
                    <input type="checkbox" id="enablePassword" class="form-check-input">
                    <label for="enablePassword" class="form-check-label">Change Password</label>
                </div>
                <label for="password" class="form-label"><strong>Password:</strong></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" disabled>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('enablePassword').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            passwordField.disabled = !this.checked;
            if (!this.checked) {
                passwordField.value = ''; // Clear password when disabled
            }
        });
    </script>
@endsection
