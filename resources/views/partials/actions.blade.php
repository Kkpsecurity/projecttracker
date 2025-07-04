<a href="{{ route('admin.hb837', $record->id) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
<a href="{{ route('admin.hb837.edit', $record->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
<form action="{{ route('admin.hb837', $record->id) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm"
        onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
</form>
