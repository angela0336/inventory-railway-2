<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::insert([
            ['code' => 'WELCOME15', 'name' => 'Diskon 15% Member Baru', 'type' => 'percent', 'value' => 15,
             'points_cost' => null, 'min_purchase' => 0, 'valid_days' => 30, 'auto_trigger' => 'welcome', 'is_active' => 1],
            ['code' => 'BDAY20', 'name' => 'Diskon 20% Ulang Tahun', 'type' => 'percent', 'value' => 20,
             'points_cost' => null, 'min_purchase' => 0, 'valid_days' => 14, 'auto_trigger' => 'birthday', 'is_active' => 1],
            ['code' => 'HEMAT5K', 'name' => 'Potongan Rp5.000', 'type' => 'fixed', 'value' => 5000,
             'points_cost' => 50, 'min_purchase' => 20000, 'valid_days' => 30, 'auto_trigger' => null, 'is_active' => 1],
            ['code' => 'FREEKEBAB', 'name' => 'Gratis 1 Kebab', 'type' => 'free_item', 'value' => 20000,
             'points_cost' => 150, 'min_purchase' => 0, 'valid_days' => 30, 'auto_trigger' => null, 'is_active' => 1],
        ]);
    }
}