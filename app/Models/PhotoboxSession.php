<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PhotoboxSession extends Model
{
    protected $fillable = [
        'token',
        'status',
        'frame_id',
        'result_image',
        'slot_images',
        'printed_at',
        'price',
        'created_by',
    ];

    protected $casts = [
        'slot_images' => 'array',
        'printed_at' => 'datetime',
    ];

    public function frame()
    {
        return $this->belongsTo(Frame::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getResultImageUrlAttribute(): ?string
    {
        if (!$this->result_image) {
            return null;
        }
        return Storage::url($this->result_image);
    }

    public function isPrinted(): bool
    {
        return $this->status === 'used' && $this->printed_at !== null;
    }
}
