<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(): View
    {
        $leads = Lead::query()->with('property')->latest()->paginate(30);

        return view('admin.leads.index', [
            'leads' => $leads,
        ]);
    }
}
