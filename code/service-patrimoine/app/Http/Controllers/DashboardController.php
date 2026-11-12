<?php

namespace App\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $scope = static function (Builder $query) use ($user): void {
            if (! $user->isNationalAdministrator()) {
                $query->where('service_id', $user->service_id ?? 0);
            }
        };
        $stateCounts = Bien::query()
            ->tap($scope)
            ->select('etat', DB::raw('count(*) as total'))
            ->groupBy('etat')
            ->pluck('total', 'etat');

        return view('dashboard', [
            'totalBiens' => Bien::query()->tap($scope)->count(),
            'totalValeur' => Bien::query()->tap($scope)->sum('valeur_acquisition'),
            'totalCategories' => Categorie::query()->whereHas('biens', $scope)->count(),
            'totalServices' => Service::query()
                ->when(! $user->isNationalAdministrator(), fn (Builder $query) => $query->whereKey($user->service_id ?? 0))
                ->count(),
            'stateCounts' => $stateCounts,
            'categories' => Categorie::query()
                ->withCount(['biens' => $scope])
                ->orderByDesc('biens_count')
                ->limit(5)
                ->get(),
            'recentBiens' => Bien::query()->tap($scope)->with(['categorie', 'service'])->latest()->limit(5)->get(),
        ]);
    }
}
