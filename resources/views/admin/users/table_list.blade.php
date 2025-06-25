@extends('layouts.app')

@section('content')
    <div class="container bg-light p-3">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Users</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ url('admin/users/create') }}">Create New User</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-warning" href="{{ url('admin/users/' . $user->id) }}/edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-sm btn-danger" href="#"
                                        onclick="confirmDelete({{ $user->id }})">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <form id="delete-user-form-{{ $user->id }}"
                                        action="{{ url('admin/users/delete/' . $user->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                event.preventDefault();
                document.getElementById('delete-user-form-' + id).submit();
            }
        }
    </script>
@endsection
