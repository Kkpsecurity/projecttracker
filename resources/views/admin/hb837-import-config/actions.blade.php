<div class="btn-group" role="group">
    <a href="{{ route('admin.hb837-import-config.edit', $field) }}" 
       class="btn btn-sm btn-primary" title="Edit Field Mapping">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.hb837-import-config.destroy', $field) }}" 
          method="POST" class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this field mapping?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Delete Field Mapping">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
