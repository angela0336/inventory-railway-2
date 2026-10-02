<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Member extends Model
{
    protected $guarded = [];
    protected $casts = ['birth_date' => 'date'];
    protected static function booted(): void
{
    static::creating(function (Member $m) {
        $m->card_token ??= Str::random(40);
    });
}

    const TIERS = [
        'silver' => ['min' => 0,    'multiplier' => 1.0],
        'gold'   => ['min' => 500,  'multiplier' => 1.5],
        'sultan' => ['min' => 1500, 'multiplier' => 2.0],
    ];

    public function transactions()   { return $this->hasMany(Transaction::class); }
    public function pointHistories() { return $this->hasMany(PointHistory::class); }
    public function vouchers()       { return $this->hasMany(MemberVoucher::class); }
    public function whatsappUrl(): string
{
    // buang semua karakter non-angka, lalu ubah awalan 0 menjadi 62
    $phone = preg_replace('/\D/', '', $this->phone);
    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }

    $link = route('member.card', $this->card_token);

    $text = "Halo {$this->name}, selamat datang di member Kebab Fetih! "
          . "Ini kartu member digitalmu (cek poin, voucher, dan tukar poin): {$link}";

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($text);
}

    public function multiplier(): float
    {
        return self::TIERS[$this->tier]['multiplier'];
    }
}