<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ConsultantFile extends Model
{
    protected $fillable = [
        'consultant_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }

    // Accessors
    public function getFileSizeHumanAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('admin.consultants.files.download', $this->id);
    }

    // Methods
    public function getFile()
    {
        return Storage::disk('local')->get($this->file_path);
    }

    public function deleteFile(): bool
    {
        if (Storage::disk('local')->exists($this->file_path)) {
            return Storage::disk('local')->delete($this->file_path);
        }
        return true;
    }
}
