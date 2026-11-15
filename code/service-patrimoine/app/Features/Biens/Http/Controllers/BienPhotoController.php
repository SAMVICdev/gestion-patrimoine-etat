<?php

namespace App\Features\Biens\Http\Controllers;

use App\Features\Biens\Models\Bien;
use App\Features\Biens\Models\BienPhoto;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BienPhotoController extends Controller
{
    public function store(Request $request, Bien $bien): RedirectResponse
    {
        $this->authorizeBien($request->user(), $bien);
        abort_unless($request->user()->canManageAssets(), 403);

        $data = $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'legendes' => ['nullable', 'array', 'max:5'],
            'legendes.*' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($request->file('photos') as $index => $file) {
            $path = $file->store("biens/{$bien->id}/photos", 'local');
            if (! $path) {
                continue;
            }

            BienPhoto::query()->create([
                'bien_id' => $bien->id,
                'path' => $path,
                'nom_original' => $file->getClientOriginalName(),
                'legende' => $data['legendes'][$index] ?? null,
                'ajoute_par' => $request->user()->id,
            ]);
        }

        return back()->with('success', 'Les photos ont été ajoutées au bien.');
    }

    public function destroy(Request $request, Bien $bien, BienPhoto $photo): RedirectResponse
    {
        $this->authorizeBien($request->user(), $bien);
        abort_unless($request->user()->canManageAssets(), 403);
        abort_unless($photo->bien_id === $bien->id, 404);

        Storage::disk('local')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'La photo a été supprimée.');
    }

    public function file(Request $request, Bien $bien, BienPhoto $photo): StreamedResponse
    {
        $this->authorizeBien($request->user(), $bien);
        abort_unless($photo->bien_id === $bien->id, 404);
        abort_unless(Storage::disk('local')->exists($photo->path), 404);

        return Storage::disk('local')->response($photo->path);
    }

    private function authorizeBien(User $user, Bien $bien): void
    {
        abort_if(! $user->isNationalAdministrator() && $user->service_id !== $bien->service_id, 404);
    }
}
