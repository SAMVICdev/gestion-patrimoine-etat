<?php

namespace App\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stateCounts = Bien::query()
            ->select('etat', DB::raw('count(*) as total'))
            ->groupBy('etat')
            ->pluck('total', 'etat');

        return view('dashboard', [
            'totalBiens' => Bien::count(),
            'totalValeur' => Bien::sum('valeur_acquisition'),
            'totalCategories' => Categorie::count(),
            'totalServices' => Service::count(),
            'stateCounts' => $stateCounts,
            'categories' => Categorie::query()->withCount('biens')->orderByDesc('biens_count')->limit(5)->get(),
            'recentBiens' => Bien::query()->with(['categorie', 'service'])->latest()->limit(5)->get(),
        ]);
    }
}
