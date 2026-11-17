<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IncidentUpdate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('auth.login');
        }

        $all = $user->incidents()->with('category')->latest()->get();

        $stats = [
            'total_incidents' => $all->count(),
            'open' => $all->whereIn('status', ['pending', 'Pending', 'investigating', 'in_progress', 'Diproses'])->count(),
            'processing' => $all->whereIn('status', ['investigating', 'in_progress', 'Diproses'])->count(),
            'resolved' => $all->whereIn('status', ['resolved', 'Selesai'])->count(),
        ];

        $recent = $all->take(6)->map(function ($i) {
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
            ];
        });

        $recentUpdates = IncidentUpdate::with(['user', 'incident'])
            ->whereIn('incident_id', $all->pluck('id'))
            ->where('visibility', 'public')
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recent', 'recentUpdates'))->with('user', $user);
    }
}
