<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Incident;
use App\Models\IncidentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = $this->stats($request);

        $recent = Incident::with(['category', 'reporter', 'assignedAdmin'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($i) {
                return [
                    'model_id' => $i->id,
                    'id' => $i->ticket_number,
                    'title' => $i->title,
                    'category' => $i->category ? $i->category->name : '-',
                    'priority' => $i->normalized_priority,
                    'priority_label' => $i->priority_badge,
                    'status' => $i->normalized_status,
                    'status_label' => $i->status_badge,
                    'date' => ($i->occurred_at ?? $i->created_at)?->format('d M Y'),
                    'reporter' => $i->reporter ? $i->reporter->name : '-',
                    'assigned' => $i->assignedAdmin ? $i->assignedAdmin->name : 'Belum ditugaskan',
                ];
            });

        // Trend + category data for Chart.js (real database values).
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $monthly = Incident::select(
            DB::raw("SUBSTR(COALESCE(occurred_at, created_at), 1, 4) as year"),
            DB::raw("SUBSTR(COALESCE(occurred_at, created_at), 6, 2) as month_num"),
            DB::raw('COUNT(*) as total')
        )
            ->where(DB::raw('COALESCE(occurred_at, created_at)'), '>=', $sixMonthsAgo)
            ->groupBy('year', 'month_num')
            ->orderBy('year', 'asc')
            ->orderBy('month_num', 'asc')
            ->get();

        $indonesianMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $rows = [];
        for ($m = 5; $m >= 0; $m--) {
            $targetDate = now()->subMonths($m);
            $yearKey = $targetDate->format('Y');
            $monthKey = $targetDate->format('m');
            $row = $monthly->first(fn ($r) => (string) $r->year === $yearKey && str_pad((string) $r->month_num, 2, '0', STR_PAD_LEFT) === $monthKey);
            $monthIndex = (int) $targetDate->format('n');
            $rows[] = [
                'label' => ($indonesianMonths[$monthIndex] ?? $targetDate->format('M')) . ' ' . $targetDate->format('y'),
                'value' => (int) ($row->total ?? 0),
            ];
        }
        $trendLabels = array_column($rows, 'label');
        $trendValues = array_column($rows, 'value');

        $categoryData = IncidentCategory::where('is_active', true)->withCount('incidents')->get()->map(function ($c) {
            return ['label' => $c->name, 'value' => (int) $c->incidents_count];
        });

        $categoryLabels = $categoryData->pluck('label')->toArray();
        $categoryValues = $categoryData->pluck('value')->toArray();

        $prioritySummary = [
            'critical' => Incident::whereIn('priority', ['critical', 'Critical'])->count(),
            'high' => Incident::whereIn('priority', ['high', 'High'])->count(),
            'medium' => Incident::whereIn('priority', ['medium', 'Medium'])->count(),
            'low' => Incident::whereIn('priority', ['low', 'Low'])->count(),
        ];

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent',
            'trendLabels',
            'trendValues',
            'categoryLabels',
            'categoryValues',
            'prioritySummary',
            'recentActivities'
        ))->with('user', $request->user());
    }

    /**
     * Derive the five dashboard statistic cards from real records.
     */
    protected function stats(Request $request): array
    {
        return [
            'total_incidents' => Incident::count(),
            'pending' => Incident::whereIn('status', ['pending', 'Pending'])->count(),
            'processing' => Incident::whereIn('status', ['investigating', 'in_progress', 'Diproses'])->count(),
            'resolved' => Incident::whereIn('status', ['resolved', 'Selesai'])->count(),
            'categories' => IncidentCategory::where('is_active', true)->count(),
        ];
    }
}
