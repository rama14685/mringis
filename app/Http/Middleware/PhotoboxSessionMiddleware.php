<?php

namespace App\Http\Middleware;

use App\Models\PhotoboxSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotoboxSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tokenValue = session('photobox_token');

        if (!$tokenValue) {
            return redirect()->route('photobox.index')->with('error', 'Silakan masukkan token sesi terlebih dahulu.');
        }

        $session = PhotoboxSession::where('token', $tokenValue)
            ->where('status', 'active')
            ->first();

        if (!$session) {
            session()->forget('photobox_token');
            return redirect()->route('photobox.index')->with('error', 'Token tidak valid atau sudah tidak aktif.');
        }

        // Make the session available to request
        $request->attributes->set('photobox_session', $session);

        return $next($request);
    }
}
