<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CreateNationalAdministrator extends Command
{
    protected $signature = 'auth:create-national-administrator {email} {name}';

    protected $description = 'Create the first national administrator account';

    public function handle(): int
    {
        $email = mb_strtolower($this->argument('email'));
        if (User::query()->where('role', 'administrateur_national')->exists()) {
            $this->components->error('Un administrateur national existe déjà.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->components->error('Cette adresse e-mail est déjà utilisée.');

            return self::FAILURE;
        }

        $password = $this->secret('Mot de passe (12 caractères, majuscule, minuscule et chiffre)');
        validator(['password' => $password], [
            'password' => ['required', Password::min(12)->letters()->mixedCase()->numbers()],
        ])->validate();

        User::query()->create([
            'name' => $this->argument('name'),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'administrateur_national',
        ]);

        $this->components->info('Administrateur national créé.');

        return self::SUCCESS;
    }
}
