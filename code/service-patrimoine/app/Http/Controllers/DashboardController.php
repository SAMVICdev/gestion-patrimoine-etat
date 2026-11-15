<?php

namespace App\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Services\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public const WIDGETS = [
        'welcome',
        'metrics',
        'actions',
        'states',
        'categories',
        'depreciation',
        'recent',
    ];

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

        $preferences = $user->preferences ?? [];
        $order = array_values(array_intersect(
            (array) ($preferences['widgets'] ?? []),
            self::WIDGETS,
        ));
        $order = array_merge($order, array_diff(self::WIDGETS, $order));
        $hidden = array_values(array_intersect(
            (array) ($preferences['hidden'] ?? []),
            self::WIDGETS,
        ));

        $biens = Bien::query()
            ->tap($scope)
            ->with('categorie:id,nom,duree_vie_annees')
            ->get(['id', 'valeur_acquisition', 'date_acquisition', 'created_at', 'categorie_id']);

        $depreciation = ['total' => 0.0, 'par_categorie' => []];
        foreach ($biens as $bien) {
            $residuel = $bien->valeurResiduelle();
            if ($residuel === null) {
                continue;
            }
            $depreciation['total'] += $residuel;
            $nom = $bien->categorie?->nom ?? 'Sans catégorie';
            $depreciation['par_categorie'][$nom] = ($depreciation['par_categorie'][$nom] ?? 0) + $residuel;
        }
        arsort($depreciation['par_categorie']);
        $depreciation['par_categorie'] = array_slice($depreciation['par_categorie'], 0, 8, true);

        return view('dashboard', [
            'totalBiens' => Bien::query()->tap($scope)->count(),
            'totalValeur' => Bien::query()->tap($scope)->sum('valeur_acquisition'),
            'valeurResiduelle' => $depreciation['total'],
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
            'widgets' => $order,
            'hiddenWidgets' => $hidden,
            'depreciation' => $depreciation,
        ]);
    }

    public function savePreferences(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'widgets' => ['required', 'array'],
            'widgets.*' => ['string', 'in:'.implode(',', self::WIDGETS)],
            'hidden' => ['sometimes', 'array'],
            'hidden.*' => ['string', 'in:'.implode(',', self::WIDGETS)],
        ]);

        $user = $request->user();
        $user->preferences = array_merge($user->preferences ?? [], [
            'widgets' => array_values(array_unique($data['widgets'])),
            'hidden' => array_values(array_unique($data['hidden'] ?? [])),
        ]);
        $user->save();

        return back()->with('success', 'Préférences du tableau de bord enregistrées.');
    }
}
