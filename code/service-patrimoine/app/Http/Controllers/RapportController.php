<?php

namespace App\Http\Controllers;

use App\Features\Audit\Models\JournalAudit;
use App\Features\Biens\Models\Bien;
use App\Features\Categories\Models\Categorie;
use App\Features\Ministeres\Models\Ministere;
use App\Features\Transferts\Models\HistoriqueEtat;
use App\Features\Transferts\Models\Transfert;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RapportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $scope = static function (Builder $query) use ($user): void {
            if (! $user->isNationalAdministrator()) {
                $query->where('service_id', $user->service_id ?? 0);
            }
        };
        $ministeres = Ministere::query()
            ->when(
                ! $user->isNationalAdministrator(),
                fn (Builder $query) => $query->whereHas('services', fn (Builder $services) => $services->whereKey($user->service_id ?? 0)),
            )
            ->withCount([
                'services' => fn (Builder $query) => $query->when(! $user->isNationalAdministrator(), fn (Builder $query) => $query->whereKey($user->service_id ?? 0)),
                'biens' => fn (Builder $query) => $query->when(
                    ! $user->isNationalAdministrator(),
                    fn (Builder $query) => $query->whereHas('service', fn (Builder $services) => $services->whereKey($user->service_id ?? 0)),
                ),
            ])
            ->get();

        $valeurResiduelle = 0.0;
        Bien::query()
            ->tap($scope)
            ->with('categorie:id,nom,duree_vie_annees')
            ->get(['id', 'valeur_acquisition', 'date_acquisition', 'created_at', 'categorie_id'])
            ->each(function (Bien $bien) use (&$valeurResiduelle): void {
                $valeurResiduelle += $bien->valeurResiduelle() ?? 0.0;
            });

        return view('rapports.index', [
            'totalBiens' => Bien::query()->tap($scope)->count(),
            'valeurTotale' => Bien::query()->tap($scope)->sum('valeur_acquisition'),
            'valeurResiduelle' => $valeurResiduelle,
            'etats' => Bien::query()->tap($scope)->select('etat', DB::raw('count(*) as total'))->groupBy('etat')->orderBy('etat')->get(),
            'ministeres' => $ministeres,
            'categories' => Categorie::query()->withCount(['biens' => $scope])->orderByDesc('biens_count')->limit(10)->get(),
            'transfertsEnAttente' => Transfert::query()
                ->where('statut', 'en_attente')
                ->when(! $user->isNationalAdministrator(), function (Builder $query) use ($user): void {
                    $query->where(function (Builder $query) use ($user): void {
                        if ($user->service_id) {
                            $query->where('service_origine_id', $user->service_id)
                                ->orWhere('service_destination_id', $user->service_id);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                    });
                })
                ->count(),
            'auditRecent' => $user->isNationalAdministrator()
                ? JournalAudit::query()->with('user')->latest('created_at')->limit(10)->get()
                : collect(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'format' => ['required', 'in:csv,xlsx,pdf'],
        ]);
        $user = $request->user();

        if ($request->input('format') === 'xlsx') {
            return $this->exportXlsx($user);
        }

        if ($request->input('format') === 'pdf') {
            return $this->exportPdf($user);
        }

        $name = 'registre-patrimonial-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($user): void {
            $stream = fopen('php://output', 'w');
            if ($stream === false) {
                throw new \RuntimeException('Impossible d’ouvrir le flux du rapport.');
            }

            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Numéro inventaire', 'Désignation', 'Catégorie', 'Ministère', 'Service', 'Localisation', 'État', 'Date acquisition', 'Valeur (FCFA)', 'Responsable']);

            Bien::query()
                ->when(! $user->isNationalAdministrator(), fn (Builder $query) => $query->where('service_id', $user->service_id ?? 0))
                ->with(['categorie', 'service.ministere'])
                ->orderBy('numero_inventaire')
                ->chunk(500, function ($biens) use ($stream): void {
                    foreach ($biens as $bien) {
                        $values = [
                            $bien->numero_inventaire,
                            $bien->designation,
                            $bien->categorie->nom,
                            $bien->service->ministere?->nom ?? '',
                            $bien->service->nom,
                            $bien->localisation,
                            $bien->etat,
                            $bien->date_acquisition?->toDateString() ?? '',
                            $bien->valeur_acquisition ?? '',
                            $bien->responsable ?? '',
                        ];

                        fputcsv($stream, array_map(self::safeCsvValue(...), $values));
                    }
                });

            fclose($stream);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function exportXlsx(User $user): StreamedResponse
    {
        $name = 'registre-patrimonial-'.now()->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($user): void {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Registre patrimoine');

            $sheet->fromArray([[
                'Numéro inventaire', 'Désignation', 'Catégorie', 'Ministère',
                'Service', 'Localisation', 'État', 'Date acquisition',
                'Valeur (FCFA)', 'Responsable',
            ]], null, 'A1');
            $sheet->getStyle('A1:J1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006A4A']],
            ]);

            $row = 2;
            Bien::query()
                ->when(! $user->isNationalAdministrator(), fn (Builder $query) => $query->where('service_id', $user->service_id ?? 0))
                ->with(['categorie', 'service.ministere'])
                ->orderBy('numero_inventaire')
                ->chunk(500, function ($biens) use ($sheet, &$row): void {
                    foreach ($biens as $bien) {
                        $sheet->fromArray([[
                            $bien->numero_inventaire,
                            $bien->designation,
                            $bien->categorie->nom,
                            $bien->service->ministere?->nom ?? '',
                            $bien->service->nom,
                            $bien->localisation,
                            $bien->etat,
                            $bien->date_acquisition?->toDateString() ?? '',
                            $bien->valeur_acquisition !== null ? (float) $bien->valeur_acquisition : '',
                            $bien->responsable ?? '',
                        ]], null, 'A'.$row);
                        $row++;
                    }
                });

            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new Xlsx($spreadsheet))->save('php://output');
        }, $name, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportPdf(User $user)
    {
        $biens = Bien::query()
            ->when(! $user->isNationalAdministrator(), fn (Builder $query) => $query->where('service_id', $user->service_id ?? 0))
            ->with(['categorie', 'service.ministere'])
            ->orderBy('numero_inventaire')
            ->get()
            ->map(fn (Bien $bien) => [
                'numero_inventaire' => $bien->numero_inventaire,
                'designation' => $bien->designation,
                'categorie' => $bien->categorie->nom,
                'ministere' => $bien->service->ministere?->nom ?? '',
                'service' => $bien->service->nom,
                'localisation' => $bien->localisation,
                'etat' => $bien->etat,
                'date_acquisition' => $bien->date_acquisition?->toDateString() ?? '',
                'valeur_acquisition' => $bien->valeur_acquisition,
                'responsable' => $bien->responsable ?? '',
            ])
            ->all();

        $blasonPath = public_path('images/blason_rt.png');
        $blason = 'data:image/png;base64,'.base64_encode(
            is_file($blasonPath) ? file_get_contents($blasonPath) : ''
        );

        return Pdf::loadView('rapports.pdf', [
            'biens' => $biens,
            'blason' => $blason,
            'totalValeur' => collect($biens)->sum(fn ($bien) => (float) ($bien['valeur_acquisition'] ?? 0)),
        ])->setPaper('a4', 'landscape')->download('registre-patrimonial-'.now()->format('Y-m-d').'.pdf');
    }

    public function audit(Request $request): View
    {
        $filters = $request->validate([
            'action' => ['sometimes', 'string', 'max:100'],
            'q' => ['sometimes', 'string', 'max:100'],
        ]);

        $entries = JournalAudit::query()->with('user')
            ->when(isset($filters['action']), fn ($query) => $query->where('action', $filters['action']))
            ->when(isset($filters['q']), function ($query) use ($filters): void {
                $query->where(function ($query) use ($filters): void {
                    $query->where('type_objet', 'like', '%'.$filters['q'].'%')
                        ->orWhere('objet_id', 'like', '%'.$filters['q'].'%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$filters['q'].'%'));
                });
            })
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('rapports.audit', ['entries' => $entries]);
    }

    public function downloadEvidence(JournalAudit $audit): StreamedResponse
    {
        abort_unless($audit->type_objet === HistoriqueEtat::class && is_array($audit->apres), 404);
        $path = $audit->apres['justificatif'] ?? null;
        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    private static function safeCsvValue(string|int|float|null $value): string|int|float
    {
        if (! is_string($value)) {
            return $value ?? '';
        }

        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }
}
