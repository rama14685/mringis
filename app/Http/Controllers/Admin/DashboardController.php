<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frame;
use App\Models\PhotoboxSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSessions = PhotoboxSession::count();
        $printedSessions = PhotoboxSession::where('status', 'used')->whereNotNull('printed_at')->count();
        $activeSessions = PhotoboxSession::where('status', 'active')->count();
        $totalRevenue = PhotoboxSession::where('status', 'used')->whereNotNull('printed_at')->sum('price');

        $recentSessions = PhotoboxSession::with(['frame', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $totalFrames = Frame::count();

        return view('admin.dashboard', compact(
            'totalSessions',
            'printedSessions',
            'activeSessions',
            'totalRevenue',
            'recentSessions',
            'totalFrames'
        ));
    }

    public function generateToken(Request $request)
    {
        $adminId = auth()->id();

        // Generate unique 5-char token
        do {
            $token = strtolower(Str::random(5));
        } while (PhotoboxSession::where('token', $token)->exists());

        $session = PhotoboxSession::create([
            'token' => $token,
            'status' => 'active',
            'price' => $request->input('price', 10000),
            'created_by' => $adminId,
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'session_id' => $session->id,
            'message' => 'Token berhasil dibuat: ' . strtoupper($token),
        ]);
    }

    public function expireToken(Request $request, PhotoboxSession $session)
    {
        if ($session->status === 'active') {
            $session->update(['status' => 'expired']);
        }

        return back()->with('success', 'Token telah dinonaktifkan.');
    }
}
