<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Member {{ $member->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen py-6">
<div class="max-w-md mx-auto px-4 space-y-4">

    @if (session('success'))
        <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="p-3 bg-red-100 text-red-800 rounded">{{ $errors->first() }}</div>
    @endif

    {{-- Kartu --}}
    <div class="rounded-2xl p-6 text-white shadow-lg bg-gradient-to-br from-orange-500 to-red-700">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-xs tracking-widest opacity-80">KEBAB FETIH</div>
                <div class="text-2xl font-bold mt-1">{{ $member->name }}</div>
                <div class="font-mono mt-1">{{ $member->member_code }}</div>
            </div>
            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-semibold uppercase">{{ $member->tier }}</span>
        </div>

        <div class="mt-5 flex items-end justify-between">
            <div>
                <div class="text-4xl font-extrabold">{{ number_format($member->points_balance) }}</div>
                <div class="text-sm opacity-90">poin</div>
            </div>
            <div class="bg-white p-2 rounded-lg">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->margin(0)->generate(route('member.card', $member->card_token)) !!}
            </div>
        </div>
    </div>

    {{-- Voucher saya --}}
    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="font-bold mb-3">Voucher Saya</h3>
        @forelse ($vouchers as $mv)
            <div class="border border-dashed border-orange-400 rounded-lg p-3 mb-2">
                <div class="font-semibold">{{ $mv->voucher->name }}</div>
                <div class="text-xs text-gray-500">Berlaku s/d {{ $mv->expires_at->format('d M Y') }}</div>
            </div>
        @empty
            <div class="text-gray-500 text-sm">Belum ada voucher aktif.</div>
        @endforelse
    </div>

    {{-- Tukar poin --}}
    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="font-bold mb-3">Tukar Poin</h3>
        @foreach ($rewards as $v)
            <form method="POST" action="{{ route('member.redeem', [$member->card_token, $v->id]) }}"
                  class="flex items-center justify-between border rounded-lg p-3 mb-2">
                @csrf
                <div>
                    <div class="font-semibold">{{ $v->name }}</div>
                    <div class="text-xs text-gray-500">{{ $v->points_cost }} poin</div>
                </div>
                <button @disabled($member->points_balance < $v->points_cost)
                        class="px-3 py-1.5 rounded text-white text-sm {{ $member->points_balance < $v->points_cost ? 'bg-gray-300' : 'bg-orange-500' }}">
                    Tukar
                </button>
            </form>
        @endforeach
    </div>

    {{-- Riwayat poin --}}
    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="font-bold mb-3">Riwayat Poin</h3>
        @forelse ($histories as $h)
            <div class="flex justify-between text-sm py-1 border-b last:border-0">
                <span>{{ $h->note ?? $h->type }} <span class="text-gray-400">· {{ $h->created_at->format('d M') }}</span></span>
                <span class="{{ $h->points >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                    {{ $h->points >= 0 ? '+' : '' }}{{ $h->points }}
                </span>
            </div>
        @empty
            <div class="text-gray-500 text-sm">Belum ada transaksi.</div>
        @endforelse
    </div>
</div>
</body>
</html>