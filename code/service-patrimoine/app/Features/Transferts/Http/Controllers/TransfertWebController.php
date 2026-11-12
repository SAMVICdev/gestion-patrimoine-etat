<?php

namespace App\Features\Transferts\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Services\Models\Service;
use App\Features\Transferts\Models\Transfert;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class TransfertWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transfert::query()
            ->with(['bien', 'serviceOrigine.ministere', 'serviceDestination.ministere', 'demandeur', 'decideur'])
            ->latest();

        if (! $request->user()->isNationalAdministrator()) {
            $serviceId = $request->user()->service_id;
            $query->where(function ($query) use ($serviceId): void {
                if ($serviceId) {
                    $query->where('service_origine_id', $serviceId)
                        ->orWhere('service_destination_id', $serviceId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            });
        }

        return view('transferts.index', ['transferts' => $query->paginate(15)]);
    }

    public function create(Request $request): View
    {
        $services = Service::query()
            ->with('ministere')
            ->when(
                ! $request->user()->isNationalAdministrator(),
                fn ($query) => $query->whereKey($request->user()->service_id),
            )
            ->orderBy('nom')
            ->get();

        $biens = Bien::query()
            ->with('service')
            ->when(
                ! $request->user()->isNationalAdministrator(),
                fn ($query) => $query->where('service_id', $request->user()->service_id),
            )
            ->orderBy('numero_inventaire')
            ->get();

        return view('transferts.create', [
            'servicesOrigine' => $services,
            'servicesDestination' => Service::query()->with('ministere')->orderBy('nom')->get(),
            'biens' => $biens,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bien_id' => ['required', 'integer', 'exists:biens,id'],
            'service_destination_id' => ['required', 'integer', 'exists:services,id'],
            'localisation_destination' => ['required', 'string', 'max:255'],
            'motif' => ['required', 'string', 'max:5000'],
            'date_effet' => ['nullable', 'date', 'after_or_equal:today'],
            'justificatif' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $user = $request->user();
        abort_if(! $user->isNationalAdministrator() && ! $user->service_id, 403);
        $justificatif = null;

        try {
            $transfert = DB::transaction(function () use ($request, $data, $user, &$justificatif): Transfert {
                $bien = Bien::query()->lockForUpdate()->findOrFail($data['bien_id']);
                abort_if(! $user->isNationalAdministrator() && $bien->service_id !== $user->service_id, 403);
                abort_if(
                    $bien->service_id === (int) $data['service_destination_id'],
                    422,
                    'Le service de destination doit être différent du service d’origine.',
                );
                abort_if(
                    Transfert::query()->where('bien_id', $bien->id)->where('statut', 'en_attente')->exists(),
                    409,
                    'Un transfert est déjà en attente pour ce bien.',
                );

                $justificatif = $request->file('justificatif')?->store('justificatifs/transferts', 'local');
                if ($request->hasFile('justificatif') && ! $justificatif) {
                    throw new \RuntimeException('Impossible d’enregistrer le justificatif du transfert.');
                }

                return Transfert::create([
                    'bien_id' => $bien->id,
                    'service_origine_id' => $bien->service_id,
                    'service_destination_id' => $data['service_destination_id'],
                    'localisation_origine' => $bien->localisation,
                    'localisation_destination' => $data['localisation_destination'],
                    'motif' => $data['motif'],
                    'justificatif' => $justificatif,
                    'statut' => 'en_attente',
                    'demande_par' => $user->id,
                    'date_demande' => now(),
                    'date_effet' => $data['date_effet'] ?? null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($justificatif) {
                Storage::disk('local')->delete($justificatif);
            }

            throw $exception;
        }

        return redirect()->route('transferts.show', $transfert)->with('success', 'La demande de transfert a été soumise pour validation.');
    }

    public function show(Request $request, Transfert $transfert): View
    {
        $this->authorizeService($request, $transfert);

        return view('transferts.show', [
            'transfert' => $transfert->load(['bien', 'serviceOrigine.ministere', 'serviceDestination.ministere', 'demandeur', 'decideur']),
        ]);
    }

    public function approve(Request $request, Transfert $transfert): RedirectResponse
    {
        $data = $request->validate([
            'date_effet' => ['nullable', 'date', 'after_or_equal:today'],
            'commentaire_decision' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $transfert, $data): void {
            $transfert = Transfert::query()->lockForUpdate()->findOrFail($transfert->id);
            abort_unless($transfert->statut === 'en_attente', 409, 'Cette demande a déjà été traitée.');

            $bien = Bien::query()->lockForUpdate()->findOrFail($transfert->bien_id);
            abort_unless(
                $bien->service_id === $transfert->service_origine_id
                    && $bien->localisation === $transfert->localisation_origine,
                409,
                'Le bien a changé depuis la demande. Créez une nouvelle demande de transfert.',
            );

            $transfert->update([
                'statut' => 'approuve',
                'decision_par' => $request->user()->id,
                'commentaire_decision' => $data['commentaire_decision'] ?? null,
                'date_decision' => now(),
                'date_effet' => $data['date_effet'] ?? $transfert->date_effet ?? now()->toDateString(),
            ]);

            $bien->update([
                'service_id' => $transfert->service_destination_id,
                'localisation' => $transfert->localisation_destination,
            ]);
        });

        return redirect()->route('transferts.show', $transfert)->with('success', 'Le transfert a été approuvé et l’affectation du bien a été mise à jour.');
    }

    public function reject(Request $request, Transfert $transfert): RedirectResponse
    {
        $data = $request->validate([
            'commentaire_decision' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $transfert, $data): void {
            $transfert = Transfert::query()->lockForUpdate()->findOrFail($transfert->id);
            abort_unless($transfert->statut === 'en_attente', 409, 'Cette demande a déjà été traitée.');

            $transfert->update([
                'statut' => 'rejete',
                'decision_par' => $request->user()->id,
                'commentaire_decision' => $data['commentaire_decision'],
                'date_decision' => now(),
            ]);
        });

        return redirect()->route('transferts.show', $transfert)->with('success', 'La demande de transfert a été rejetée.');
    }

    public function downloadEvidence(Request $request, Transfert $transfert): StreamedResponse
    {
        $this->authorizeService($request, $transfert);
        abort_unless($transfert->justificatif && Storage::disk('local')->exists($transfert->justificatif), 404);

        return Storage::disk('local')->download($transfert->justificatif);
    }

    private function authorizeService(Request $request, Transfert $transfert): void
    {
        $user = $request->user();
        abort_if(
            ! $user->isNationalAdministrator()
                && (! $user->service_id
                    || ($transfert->service_origine_id !== $user->service_id
                        && $transfert->service_destination_id !== $user->service_id)),
            403,
        );
    }
}
