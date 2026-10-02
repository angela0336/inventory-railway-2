<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberVoucher;
use App\Models\PointHistory;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class MembershipService
{
    const POINT_PER_RUPIAH = 1000; // Rp1.000 = 1 poin

    public function register(array $data): Member
    {
        return DB::transaction(function () use ($data) {
            $member = Member::create($data);
            $member->update([
                'member_code' => 'FTH-' . now()->format('ym') . '-' . str_pad($member->id, 4, '0', STR_PAD_LEFT),
            ]);

            $this->giveAutoVoucher($member, 'welcome');
            return $member->fresh();
        });
    }

    public function giveAutoVoucher(Member $member, string $trigger): ?MemberVoucher
    {
        $voucher = Voucher::where('auto_trigger', $trigger)->where('is_active', true)->first();
        if (!$voucher) return null;

        return $member->vouchers()->create([
            'voucher_id' => $voucher->id,
            'expires_at' => now()->addDays($voucher->valid_days),
        ]);
    }

    /** Poin: tanpa minimum, minimal 1 poin per transaksi. */
    public function calculatePoints(Member $member, int $amount): int
    {
        return max(1, (int) floor(($amount / self::POINT_PER_RUPIAH) * $member->multiplier()));
    }

    public function discountFor(Voucher $voucher, int $subtotal): int
    {
        if ($subtotal < $voucher->min_purchase) {
            throw new RuntimeException("Minimal belanja voucher ini Rp" . number_format($voucher->min_purchase, 0, ',', '.'));
        }

        $discount = $voucher->type === 'percent'
            ? (int) floor($subtotal * $voucher->value / 100)
            : $voucher->value;

        return min($discount, $subtotal);
    }

    public function checkout(Member $member, int $subtotal, ?int $memberVoucherId = null): Transaction
    {
        return DB::transaction(function () use ($member, $subtotal, $memberVoucherId) {
            $member = Member::lockForUpdate()->find($member->id);
            $discount = 0;
            $mv = null;

            if ($memberVoucherId) {
                $mv = MemberVoucher::with('voucher')
                    ->where('member_id', $member->id)
                    ->lockForUpdate()
                    ->findOrFail($memberVoucherId);

                if (!$mv->isUsable()) {
                    throw new RuntimeException('Voucher tidak valid atau sudah kedaluwarsa.');
                }

                $discount = $this->discountFor($mv->voucher, $subtotal);
                $mv->update(['status' => 'used', 'used_at' => now()]);
            }

            $total  = $subtotal - $discount;
            $points = $this->calculatePoints($member, $total);

            $trx = Transaction::create([
                'member_id'         => $member->id,
                'invoice_no'        => 'INV-' . now()->format('ymd') . '-' . strtoupper(Str::random(5)),
                'subtotal'          => $subtotal,
                'discount'          => $discount,
                'total'             => $total,
                'member_voucher_id' => $mv?->id,
                'points_earned'     => $points,
]);

            $member->increment('points_balance', $points);
            $member->increment('lifetime_points', $points);

            PointHistory::create([
                'member_id'      => $member->id,
                'transaction_id' => $trx->id,
                'type'           => 'earn',
                'points'         => $points,
                'note'           => $trx->invoice_no,
            ]);

            $this->refreshTier($member->fresh());
            return $trx;
        });
    }

    public function redeemVoucher(Member $member, Voucher $voucher): MemberVoucher
    {
        return DB::transaction(function () use ($member, $voucher) {
            $member = Member::lockForUpdate()->find($member->id);

            if (!$voucher->points_cost || !$voucher->is_active) {
                throw new RuntimeException('Voucher ini tidak bisa ditukar dengan poin.');
            }
            if ($member->points_balance < $voucher->points_cost) {
                throw new RuntimeException('Poin tidak cukup.');
            }

            $member->decrement('points_balance', $voucher->points_cost);

            PointHistory::create([
                'member_id' => $member->id,
                'type'      => 'redeem',
                'points'    => -$voucher->points_cost,
                'note'      => 'Tukar voucher ' . $voucher->name,
            ]);

            return $member->vouchers()->create([
                'voucher_id' => $voucher->id,
                'expires_at' => now()->addDays($voucher->valid_days),
            ]);
        });
    }

    protected function refreshTier(Member $member): void
    {
        $tier = 'silver';
        foreach (Member::TIERS as $name => $cfg) {
            if ($member->lifetime_points >= $cfg['min']) {
                $tier = $name;
            }
        }
        if ($tier !== $member->tier) {
            $member->update(['tier' => $tier]);
        }
    }
}