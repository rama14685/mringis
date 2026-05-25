<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrameController extends Controller
{
    public function index()
    {
        $frames = Frame::latest()->paginate(10);
        return view('admin.frames.index', compact('frames'));
    }

    public function create()
    {
        $slotOptions = [1, 2, 4, 6];
        return view('admin.frames.create', compact('slotOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'overlay_image'     => ['nullable', 'image', 'mimes:png', 'max:5120'],
            'slot_count'        => ['required', 'integer', 'min:1', 'max:12'],
            'price'             => ['required', 'integer', 'min:0'],
            'is_active'         => ['boolean'],
            'layout_coordinates'=> ['nullable', 'string'], // JSON string dari editor
        ]);

        // Parse & validasi layout_coordinates JSON
        $layoutCoordinates = null;
        if (!empty($validated['layout_coordinates'])) {
            $decoded = json_decode($validated['layout_coordinates'], true);
            if (is_array($decoded) && isset($decoded['slots']) && count($decoded['slots']) > 0) {
                $layoutCoordinates = $decoded;
                // Sync slot_count dari jumlah slot yang didefinisikan
                $validated['slot_count'] = count($decoded['slots']);
            }
        }

        // Build slot_layout fallback jika tidak ada layout_coordinates
        $slotLayout = $layoutCoordinates
            ? null
            : (Frame::defaultLayouts()[$validated['slot_count']] ?? []);

        $overlayPath = null;
        if ($request->hasFile('overlay_image')) {
            $overlayPath = $request->file('overlay_image')->store('frames/overlays', 'public');
        }

        Frame::create([
            'name'               => $validated['name'],
            'description'        => $validated['description'] ?? null,
            'overlay_image'      => $overlayPath,
            'thumbnail'          => $overlayPath,
            'slot_count'         => $validated['slot_count'],
            'slot_layout'        => $slotLayout,
            'layout_coordinates' => $layoutCoordinates,
            'price'              => $validated['price'],
            'is_active'          => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.frames.index')
            ->with('success', 'Frame berhasil ditambahkan!');
    }

    public function show(Frame $frame)
    {
        return view('admin.frames.show', compact('frame'));
    }

    public function edit(Frame $frame)
    {
        $slotOptions = [1, 2, 4, 6];
        return view('admin.frames.edit', compact('frame', 'slotOptions'));
    }

    public function update(Request $request, Frame $frame)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'overlay_image'     => ['nullable', 'image', 'mimes:png', 'max:5120'],
            'slot_count'        => ['required', 'integer', 'min:1', 'max:12'],
            'price'             => ['required', 'integer', 'min:0'],
            'is_active'         => ['boolean'],
            'layout_coordinates'=> ['nullable', 'string'],
        ]);

        // Parse layout_coordinates
        $layoutCoordinates = null;
        if (!empty($validated['layout_coordinates'])) {
            $decoded = json_decode($validated['layout_coordinates'], true);
            if (is_array($decoded) && isset($decoded['slots']) && count($decoded['slots']) > 0) {
                $layoutCoordinates = $decoded;
                $validated['slot_count'] = count($decoded['slots']);
            }
        }

        $slotLayout = $layoutCoordinates
            ? null
            : (Frame::defaultLayouts()[$validated['slot_count']] ?? $frame->slot_layout);

        $data = [
            'name'               => $validated['name'],
            'description'        => $validated['description'] ?? null,
            'slot_count'         => $validated['slot_count'],
            'slot_layout'        => $slotLayout,
            'layout_coordinates' => $layoutCoordinates,
            'price'              => $validated['price'],
            'is_active'          => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('overlay_image')) {
            if ($frame->overlay_image) {
                Storage::disk('public')->delete($frame->overlay_image);
            }
            $data['overlay_image'] = $request->file('overlay_image')->store('frames/overlays', 'public');
            $data['thumbnail']     = $data['overlay_image'];
        }

        $frame->update($data);

        return redirect()->route('admin.frames.index')
            ->with('success', 'Frame berhasil diperbarui!');
    }

    public function destroy(Frame $frame)
    {
        if ($frame->overlay_image) {
            Storage::disk('public')->delete($frame->overlay_image);
        }

        $frame->delete();

        return redirect()->route('admin.frames.index')
            ->with('success', 'Frame berhasil dihapus!');
    }
}
