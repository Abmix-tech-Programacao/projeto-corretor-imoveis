<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'propertyCount' => Property::count(),
            'publishedCount' => Property::where('is_published', true)->count(),
            'leadCount' => Lead::count(),
            'recentLeads' => Lead::query()->with('property')->latest()->take(7)->get(),
            'recentProperties' => Property::query()->latest()->take(5)->get(),
        ]);
    }
}
