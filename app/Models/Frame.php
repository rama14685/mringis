<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Frame extends Model
{
    protected $fillable = [
        'name',
        'description',
        'overlay_image',
        'thumbnail',
        'slot_count',
        'slot_layout',
        'layout_coordinates',
        'price',
        'is_active',
    ];

    protected $casts = [
        'slot_layout'         => 'array',
        'layout_coordinates'  => 'array',
        'is_active'           => 'boolean',
    ];

    public function getOverlayImageUrlAttribute(): ?string
    {
        if (!$this->overlay_image) {
            return null;
        }
        return Storage::url($this->overlay_image);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }
        return Storage::url($this->thumbnail);
    }

    /**
     * Kembalikan array slot dalam format persentase (0-100) yang siap dipakai CSS.
     * Mendukung dua skema penyimpanan:
     *   1. layout_coordinates (baru): {ref_w, ref_h, slots:[{x,y,w,h}]} — koordinat piksel absolut
     *   2. slot_layout (lama):        [{x,y,w,h}] — sudah dalam persentase
     *
     * Selalu mengembalikan array [{x%,y%,w%,h%}]
     */
    public function getResolvedSlotsAttribute(): array
    {
        // ── Skema baru: layout_coordinates ───────────────────
        if (!empty($this->layout_coordinates) && !empty($this->layout_coordinates['slots'])) {
            $lc    = $this->layout_coordinates;
            $refW  = max(1, (int) ($lc['ref_w'] ?? 600));
            $refH  = max(1, (int) ($lc['ref_h'] ?? 600));

            return array_map(function ($slot) use ($refW, $refH) {
                return [
                    'x' => round(($slot['x'] / $refW) * 100, 4),
                    'y' => round(($slot['y'] / $refH) * 100, 4),
                    'w' => round(($slot['w'] / $refW) * 100, 4),
                    'h' => round(($slot['h'] / $refH) * 100, 4),
                ];
            }, $lc['slots']);
        }

        // ── Skema lama: slot_layout (persentase langsung) ────
        if (!empty($this->slot_layout)) {
            return $this->slot_layout;
        }

        // ── Fallback: default grid ────────────────────────────
        return static::defaultLayouts()[$this->slot_count] ?? static::defaultLayouts()[4];
    }

    /**
     * Aspek rasio (width / height) dari canvas frame.
     * Digunakan untuk mempertahankan proporsi container di UI.
     */
    public function getAspectRatioAttribute(): float
    {
        if (!empty($this->layout_coordinates)) {
            $refW = (int) ($this->layout_coordinates['ref_w'] ?? 600);
            $refH = (int) ($this->layout_coordinates['ref_h'] ?? 600);
            return $refH > 0 ? $refW / $refH : 1.0;
        }
        return 1.0; // default square
    }

    public function photoboxSessions()
    {
        return $this->hasMany(PhotoboxSession::class);
    }

    /**
     * Jumlah slot yang resolved (dari layout_coordinates atau slot_count)
     */
    public function getResolvedSlotCountAttribute(): int
    {
        if (!empty($this->layout_coordinates['slots'])) {
            return count($this->layout_coordinates['slots']);
        }
        return (int) $this->slot_count;
    }

    /**
     * Default slot layouts keyed by slot count (persentase, backward compat)
     */
    public static function defaultLayouts(): array
    {
        return [
            1 => [
                ['x' => 5, 'y' => 5, 'w' => 90, 'h' => 90],
            ],
            2 => [
                ['x' => 5, 'y' => 5,  'w' => 90, 'h' => 44],
                ['x' => 5, 'y' => 51, 'w' => 90, 'h' => 44],
            ],
            4 => [
                ['x' => 5,  'y' => 5,  'w' => 43, 'h' => 43],
                ['x' => 52, 'y' => 5,  'w' => 43, 'h' => 43],
                ['x' => 5,  'y' => 52, 'w' => 43, 'h' => 43],
                ['x' => 52, 'y' => 52, 'w' => 43, 'h' => 43],
            ],
            6 => [
                ['x' => 5,  'y' => 5,  'w' => 28, 'h' => 43],
                ['x' => 36, 'y' => 5,  'w' => 28, 'h' => 43],
                ['x' => 67, 'y' => 5,  'w' => 28, 'h' => 43],
                ['x' => 5,  'y' => 52, 'w' => 28, 'h' => 43],
                ['x' => 36, 'y' => 52, 'w' => 28, 'h' => 43],
                ['x' => 67, 'y' => 52, 'w' => 28, 'h' => 43],
            ],
        ];
    }
}
