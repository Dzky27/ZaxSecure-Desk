<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentCategory;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $incidents = Incident::with(['category', 'reporter', 'assignedAdmin'])->latest()->get();

        $total = $incidents->count();
        $resolved = $incidents->whereIn('normalized_status', ['resolved'])->count();
        $inProgress = $incidents->whereIn('normalized_status', ['investigating', 'in_progress'])->count();
        $pending = $incidents->whereIn('normalized_status', ['pending'])->count();
        $critical = $incidents->where('normalized_priority', 'critical')->count();

        $resolutionRate = $total > 0 ? round(($resolved / $total) * 100) : 0;

        $priorityBreakdown = [
            'critical' => $critical,
            'high' => $incidents->where('normalized_priority', 'high')->count(),
            'medium' => $incidents->where('normalized_priority', 'medium')->count(),
            'low' => $incidents->where('normalized_priority', 'low')->count(),
        ];

        $categoryBreakdown = IncidentCategory::withCount('incidents')
            ->orderByDesc('incidents_count')
            ->get();

        return view('admin.reports', compact(
            'incidents',
            'total',
            'resolved',
            'inProgress',
            'pending',
            'critical',
            'resolutionRate',
            'priorityBreakdown',
            'categoryBreakdown'
        ));
    }
}
