<table class="table table-light table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Company Name</th>
            <th>Phone</th>
            <th class="text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($owners as $owner)
            <tr>
                <td>{{ $owner->id }}</td>
                <td>{{ $owner->name }}</td>
                <td>{{ $owner->email }}</td>
                <td>{{ $owner->company_name }}</td>
                <td>{{ $owner->phone }}</td>
                <td class="text-right">
                    <form action="{{ route('admin.owners.destroy', $owner->id) }}" method="POST">
                        <a class="btn btn-primary" href="{{ route('admin.owners.edit', $owner->id) }}">Edit</a>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this owner?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No Owner records found</td>
            </tr>
        @endforelse
    </tbody>
</table>

@if ($owners->isNotEmpty())
    {!! $owners->links() !!}
@endif
