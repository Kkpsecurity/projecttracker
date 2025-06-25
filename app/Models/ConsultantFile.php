<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultantFile extends Model
{
    protected $table = 'consultant_files';

    protected $fillable = [
        'consultant_id',
        'file_type',
        'original_filename',
        'file_path',
        'file_size',
    ];

    /**
     * Relationship: Each file belongs to one consultant.
     */
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class, 'consultant_id');
    }
}
