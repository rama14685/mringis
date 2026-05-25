<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoboxSession;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = PhotoboxSession::with(['frame', 'creator'])
            ->where('status', 'used')
            ->whereNotNull('printed_at');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('printed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('printed_at', '<=', $request->date_to);
        }

        // Filter by frame
        if ($request->filled('frame_id')) {
            $query->where('frame_id', $request->frame_id);
        }

        $sessions = $query->latest('printed_at')->paginate(15)->withQueryString();

        $totalRevenue = $query->sum('price');
        $totalPrinted = $query->count();

        // Monthly summary
        $monthlyStats = PhotoboxSession::selectRaw('
            YEAR(printed_at) as year,
            MONTH(printed_at) as month,
            COUNT(*) as total_sessions,
            SUM(price) as total_revenue
        ')
        ->where('status', 'used')
        ->whereNotNull('printed_at')
        ->groupByRaw('YEAR(printed_at), MONTH(printed_at)')
        ->orderByRaw('YEAR(printed_at) DESC, MONTH(printed_at) DESC')
        ->limit(12)
        ->get();

        $frames = \App\Models\Frame::all();

        return view('admin.reports.index', compact(
            'sessions',
            'totalRevenue',
            'totalPrinted',
            'monthlyStats',
            'frames'
        ));
    }
}
