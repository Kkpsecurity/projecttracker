<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HB837File extends Model
{
    protected $fillable = [
        'hb837_id',
        'uploaded_by',
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'file_category',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function hb837(): BelongsTo
    {
        return $this->belongsTo(HB837::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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
        return route('hb837.files.download', $this->id);
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
