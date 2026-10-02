<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Voucher;
use App\Services\MembershipService;
use RuntimeException;

class MemberCardController extends Controller
{
    // Kartu member (publik, diakses lewat kode member dari QR / link)
   public function show(string $token)
{
    $member = Member::where('card_token', $token)->firstOrFail();

    $vouchers = $member->vouchers()
        ->where('status', 'available')
        ->whereDate('expires_at', '>=', now())
        ->with('voucher')
        ->get();

    $rewards = Voucher::where('is_active', true)
        ->whereNotNull('points_cost')
        ->orderBy('points_cost')
        ->get();

    $histories = $member->pointHistories()->latest()->limit(10)->get();

    return view('member.card', compact('member', 'vouchers', 'rewards', 'histories'));
}

    // Tukar poin dengan voucher
    public function redeem(string $token, Voucher $voucher, MembershipService $svc)
{
    $member = Member::where('card_token', $token)->firstOrFail();

    try {
        $svc->redeemVoucher($member, $voucher);
    } catch (RuntimeException $e) {
        return back()->withErrors(['redeem' => $e->getMessage()]);
    }

    return back()->with('success', "Voucher {$voucher->name} berhasil ditukar.");
}
}