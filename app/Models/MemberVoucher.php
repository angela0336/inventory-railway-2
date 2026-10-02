<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberVoucher extends Model
{
    protected $guarded = [];
    protected $casts = ['expires_at' => 'date', 'used_at' => 'datetime'];

    public function voucher() { return $this->belongsTo(Voucher::class); }
    public function member()  { return $this->belongsTo(Member::class); }

    public function isUsable(): bool
    {
        return $this->status === 'available' && $this->expires_at->endOfDay()->isFuture();
    }
}