<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\PhotoboxSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoboxController extends Controller
{
    /**
     * Fase 0: Halaman input token
     */
    public function index()
    {
        // Clear any existing photobox session from PHP session
        session()->forget(['photobox_token', 'photobox_frame_id']);
        return view('photobox.index');
    }

    /**
     * Validasi token yang dimasukkan user
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'size:5'],
        ]);

        $token = strtolower(trim($request->token));

        $photoSession = PhotoboxSession::where('token', $token)
            ->where('status', 'active')
            ->first();

        if (!$photoSession) {
            return back()->withErrors([
                'token' => 'Token tidak valid atau sudah tidak aktif. Silakan hubungi staff.',
            ])->withInput();
        }

        // Store token in PHP session
        session(['photobox_token' => $token]);

        return redirect()->route('photobox.select-frame')
            ->with('success', 'Token valid! Silakan pilih frame foto Anda.');
    }

    /**
     * Fase 1: Pilih frame (Timer 3 menit)
     */
    public function selectFrame(Request $request)
    {
        $photoSession = $request->attributes->get('photobox_session');
        $frames = Frame::where('is_active', true)->get();

        return view('photobox.select-frame', compact('frames', 'photoSession'));
    }

    /**
     * Simpan pilihan frame & mulai sesi foto
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'frame_id' => ['required', 'exists:frames,id'],
        ]);

        $token = session('photobox_token');
        $photoSession = PhotoboxSession::where('token', $token)->where('status', 'active')->firstOrFail();

        $frame = Frame::findOrFail($request->frame_id);

        $photoSession->update([
            'frame_id' => $frame->id,
            'price' => $frame->price,
        ]);

        session(['photobox_frame_id' => $frame->id]);

        return redirect()->route('photobox.session');
    }

    /**
     * Fase 2: Sesi foto dengan kamera (Timer 7 menit)
     */
    public function photoSession(Request $request)
    {
        $token = session('photobox_token');
        $photoSession = PhotoboxSession::where('token', $token)
            ->where('status', 'active')
            ->with('frame')
            ->firstOrFail();

        if (!$photoSession->frame_id) {
            return redirect()->route('photobox.select-frame')
                ->with('error', 'Silakan pilih frame terlebih dahulu.');
        }

        $frame = $photoSession->frame;

        return view('photobox.session', compact('photoSession', 'frame'));
    }

    /**
     * Simpan kolase sementara dari sesi foto (AJAX)
     */
    public function saveCollage(Request $request)
    {
        $request->validate([
            'slot_images' => ['required', 'array'],
            'slot_images.*' => ['required', 'string'], // base64 data URLs
        ]);

        $token = session('photobox_token');
        $photoSession = PhotoboxSession::where('token', $token)->where('status', 'active')->firstOrFail();

        // Store slot images as base64 in session temporarily
        session(['photobox_slot_images' => $request->slot_images]);

        return response()->json(['success' => true, 'message' => 'Foto disimpan sementara.']);
    }

    /**
     * Fase 3: Edit kolase (filter/efek)
     */
    public function editCollage(Request $request)
    {
        $token = session('photobox_token');
        $photoSession = PhotoboxSession::where('token', $token)
            ->where('status', 'active')
            ->with('frame')
            ->firstOrFail();

        $slotImages = session('photobox_slot_images', []);
        $frame = $photoSession->frame;

        if (empty($slotImages)) {
            return redirect()->route('photobox.session')
                ->with('error', 'Belum ada foto yang diambil. Silakan foto terlebih dahulu.');
        }

        return view('photobox.edit', compact('photoSession', 'frame', 'slotImages'));
    }

    /**
     * Fase 4: Cetak & simpan hasil akhir
     */
    public function print(Request $request)
    {
        $request->validate([
            'result_image' => ['required', 'string'], // base64 data URL
        ]);

        $token = session('photobox_token');
        $photoSession = PhotoboxSession::where('token', $token)->where('status', 'active')->firstOrFail();

        // Decode base64 and save to storage
        $imageData = $request->result_image;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData);

        $filename = 'results/' . $token . '_' . time() . '.png';
        Storage::disk('public')->put($filename, $decodedImage);

        // Save slot images permanently
        $slotImages = session('photobox_slot_images', []);
        $savedSlots = [];
        foreach ($slotImages as $index => $slotBase64) {
            $slotData = str_replace('data:image/jpeg;base64,', '', $slotBase64);
            $slotData = str_replace('data:image/png;base64,', '', $slotData);
            $slotData = str_replace(' ', '+', $slotData);
            $slotDecoded = base64_decode($slotData);
            $slotFilename = 'results/slots/' . $token . '_slot' . $index . '_' . time() . '.jpg';
            Storage::disk('public')->put($slotFilename, $slotDecoded);
            $savedSlots[] = $slotFilename;
        }

        // Update session as used/printed
        $photoSession->update([
            'status' => 'used',
            'result_image' => $filename,
            'slot_images' => $savedSlots,
            'printed_at' => now(),
        ]);

        // Clear PHP session data
        session()->forget(['photobox_token', 'photobox_frame_id', 'photobox_slot_images']);

        return redirect()->route('photobox.success')->with([
            'success_token' => $token,
            'result_image_url' => Storage::url($filename),
        ]);
    }

    /**
     * Halaman sukses setelah cetak
     */
    public function success(Request $request)
    {
        $resultImageUrl = session('result_image_url');
        $successToken = session('success_token');

        return view('photobox.success', compact('resultImageUrl', 'successToken'));
    }
}
