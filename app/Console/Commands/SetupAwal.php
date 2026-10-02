<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupAwal extends Command
{
    protected $signature = 'app:setup-awal';

    protected $description = 'Migrasi database, isi voucher awal, dan buat akun kasir dari environment';

    public function handle(): int
    {
        $this->call('migrate', ['--force' => true]);

        if (Voucher::count() === 0) {
            $this->call('db:seed', ['--class' => 'VoucherSeeder', '--force' => true]);
        }

        $email = env('KASIR_EMAIL');
        $password = env('KASIR_PASSWORD');

        if ($email && $password && ! User::where('email', $email)->exists()) {
            $user = User::create([
                'name' => env('KASIR_NAME', 'Kasir'),
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $this->info("Akun kasir {$email} dibuat.");
        }

        return self::SUCCESS;
    }
}
