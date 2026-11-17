<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentCategory;

class HomeController extends Controller
{
    public function index()
    {
        $totalIncidents = 0;
        $resolvedIncidents = 0;
        $activeCategories = 0;

        try {
            $totalIncidents = Incident::count();
            $resolvedIncidents = Incident::whereIn('status', ['resolved', 'Selesai'])->count();
            $activeCategories = IncidentCategory::where('is_active', true)->count();
        } catch (\Throwable $e) {
            // Safe fallback if database is not yet initialized
        }

        return view('index', compact('totalIncidents', 'resolvedIncidents', 'activeCategories'));
    }
}
