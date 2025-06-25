<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;

class HB837File extends Model
{
    protected $table = 'hb837_files';

    protected $fillable = [
        'hb837_id',
        'user_id',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'file_type',
    ];

    /**
     * Relationship with HB837.
     * @return BelongsTo
     */
    public function hb837(): BelongsTo
    {
        return $this->belongsTo(HB837::class);
    }

    /**
     * Relationship with User.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full file path.
     * @return string
     */
    public function getFilePathAttribute(): string
    {
        return storage_path('app/' . $this->attributes['file_path']);
    }

    /**
     * Get the file size in a human-readable format.
     * @return string|null
     */
    public function getFileSizeAttribute(): ?string
    {
        if (File::exists($this->attributes['file_path'])) {
            return $this->humanReadableFileSize(File::size(storage_path('app/' . $this->attributes['file_path'])));
        }

        return null;
    }

    /**
     * Get the file type.
     * @return string|null
     */
    public function getFileTypeAttribute(): ?string
    {
        if (File::exists($this->attributes['file_path'])) {
            return File::mimeType(storage_path('app/' . $this->attributes['file_path']));
        }

        return null;
    }

    /**
     * Convert bytes to a human-readable file size.
     * @param int $bytes
     * @return string
     */
    private function humanReadableFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}
