<?php

namespace App\Features\Inventaires\Http\Controllers;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Features\Inventaires\Models\CampagneInventaire;
use App\Features\Inventaires\Models\ConstatInventaire;
use App\Features\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampagneInventaireWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = CampagneInventaire::query()->with(['service', 'createur'])->withCount('constats')->latest();

        if (! $request->user()->isNationalAdministrator()) {
            $serviceId = $request->user()->service_id;
            $query->where('service_id', $serviceId ?? 0);
        }

        return view('inventaires.index', ['campagnes' => $query->paginate(15)]);
    }

    public function create(Request $request): View
    {
        $services = Service::query()
            ->when(
                $request->user()->role !== 'administrateur_national' && $request->user()->service_id,
                fn ($query) => $query->whereKey($request->user()->service_id),
            )
            ->orderBy('nom')
            ->get();

        return view('inventaires.create', ['services' => $services]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ]);
        $user = $request->user();
        abort_if(! $user->isNationalAdministrator() && ! $user->service_id, 403);
        $serviceId = $user->role !== 'administrateur_national' && $user->service_id
            ? $user->service_id
            : ($data['service_id'] ?? null);

        $campagne = CampagneInventaire::create([
            ...$data,
            'service_id' => $serviceId,
            'statut' => 'en_cours',
            'cree_par' => $user->id,
            'date_debut' => now(),
        ]);

        return redirect()->route('inventaires.show', $campagne)->with('success', 'La campagne d’inventaire a démarré.');
    }

    public function show(Request $request, CampagneInventaire $inventaire): View
    {
        $this->authorizeService($request, $inventaire);
        $inventaire->load(['service', 'createur', 'constats.bien', 'constats.agent']);
        $query = Bien::query()->with(['categorie', 'service']);

        if ($inventaire->service_id) {
            $query->where('service_id', $inventaire->service_id);
        }

        return view('inventaires.show', [
            'campagne' => $inventaire,
            'biens' => $query->orderBy('numero_inventaire')->get(),
            'resultats' => ConstatInventaire::RESULTATS,
        ]);
    }

    public function record(Request $request, CampagneInventaire $inventaire): RedirectResponse
    {
        $this->authorizeService($request, $inventaire);
        abort_unless($inventaire->statut === 'en_cours', 409, 'Cette campagne est clôturée.');

        $data = $request->validate([
            'bien_id' => [
                'required',
                'integer',
                Rule::exists('biens', 'id')->where(fn ($query) => $inventaire->service_id
                    ? $query->where('service_id', $inventaire->service_id)
                    : $query),
                Rule::unique('constats_inventaire', 'bien_id')->where('campagne_id', $inventaire->id),
            ],
            'resultat' => ['required', Rule::in(ConstatInventaire::RESULTATS)],
            'commentaire' => ['nullable', 'string', 'max:2000'],
        ]);
        if (in_array($data['resultat'], ['introuvable', 'degrade'], true) && empty($data['commentaire'])) {
            return back()->withErrors([
                'commentaire' => 'Un commentaire est requis pour signaler un bien introuvable ou dégradé.',
            ])->withInput();
        }

        DB::transaction(function () use ($request, $inventaire, $data): void {
            $inventaire = CampagneInventaire::query()->lockForUpdate()->findOrFail($inventaire->id);
            abort_unless($inventaire->statut === 'en_cours', 409, 'Cette campagne est clôturée.');
            $bien = Bien::query()->lockForUpdate()->findOrFail($data['bien_id']);
            if ($inventaire->service_id) {
                abort_unless($bien->service_id === $inventaire->service_id, 422);
            }

            $constat = ConstatInventaire::create([
                ...$data,
                'campagne_id' => $inventaire->id,
                'agent_id' => $request->user()->id,
                'date_constat' => now(),
            ]);

            if ($constat->resultat === 'introuvable') {
                $before = $bien->etat;
                $bien->update(['etat' => 'introuvable']);
                $bien->historiqueEtats()->latest('date_modification')->first()?->update([
                    'commentaire' => $constat->commentaire,
                ]);
                JournalAudit::record('signalement_bien_introuvable', $bien, ['etat' => $before], [
                    'etat' => 'introuvable',
                    'campagne_id' => $inventaire->id,
                ]);
            } elseif ($constat->resultat === 'degrade') {
                if ($bien->etat === 'bon_etat') {
                    $bien->update(['etat' => 'etat_moyen']);
                    $bien->historiqueEtats()->latest('date_modification')->first()?->update([
                        'commentaire' => $constat->commentaire,
                    ]);
                }
            }
        });

        return redirect()->route('inventaires.show', $inventaire)->with('success', 'Le constat d’inventaire a été enregistré.');
    }

    public function close(Request $request, CampagneInventaire $inventaire): RedirectResponse
    {
        $this->authorizeService($request, $inventaire);
        DB::transaction(function () use ($request, $inventaire): void {
            $inventaire = CampagneInventaire::query()->lockForUpdate()->findOrFail($inventaire->id);
            abort_unless($inventaire->statut === 'en_cours', 409, 'Cette campagne est déjà clôturée.');

            $inventaire->update([
                'statut' => 'cloturee',
                'cloture_par' => $request->user()->id,
                'date_cloture' => now(),
            ]);
        });

        return redirect()->route('inventaires.show', $inventaire)->with('success', 'La campagne d’inventaire a été clôturée.');
    }

    private function authorizeService(Request $request, CampagneInventaire $campagne): void
    {
        $user = $request->user();
        abort_if(
            ! $user->isNationalAdministrator()
                && (! $user->service_id || $campagne->service_id !== $user->service_id),
            403,
        );
    }
}
