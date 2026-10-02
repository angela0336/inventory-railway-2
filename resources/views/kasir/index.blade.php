<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kasir Kebab Fetih</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('notfound'))
                <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
                    {{ session('notfound') }}
                    <a href="{{ route('member.create') }}" class="underline font-semibold">Daftarkan member baru</a>
                </div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-800 rounded">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            {{-- Cari member --}}
            <div class="bg-white p-6 rounded shadow">
                <form method="GET" action="{{ route('kasir.index') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $q }}" autofocus
                           placeholder="Nomor HP atau kode member (FTH-...)"
                           class="flex-1 border-gray-300 rounded">
                    <button class="px-4 py-2 bg-gray-800 text-white rounded">Cari</button>
                    <a href="{{ route('member.create') }}" class="px-4 py-2 bg-orange-500 text-white rounded">+ Member</a>
                </form>
            </div>

            @if ($member)
                {{-- Info member --}}
                <div class="bg-white p-6 rounded shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-xl font-bold">{{ $member->name }}</div>
                            <div class="text-gray-500">{{ $member->member_code }} · {{ $member->phone }}</div>
                            <div class="mt-2 flex gap-3 items-center">
                                <a href="{{ route('member.card', $member->card_token) }}" target="_blank"
                                   class="text-sm text-orange-600 underline">Lihat kartu member</a>
                                <a href="{{ $member->whatsappUrl() }}" target="_blank"
                                    class="px-3 py-1 bg-green-500 text-white text-sm rounded">Kirim ke WhatsApp</a>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded bg-orange-100 text-orange-700 font-semibold uppercase">{{ $member->tier }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="text-2xl font-bold">{{ $member->points_balance }}</div>
                            <div class="text-sm text-gray-500">Poin tersedia</div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="text-2xl font-bold">{{ $member->vouchers->count() }}</div>
                            <div class="text-sm text-gray-500">Voucher aktif</div>
                        </div>
                    </div>
                </div>

                {{-- Form transaksi --}}
                <form method="POST" action="{{ route('kasir.checkout') }}" class="bg-white p-6 rounded shadow space-y-4">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $member->id }}">

                    <div>
                        <label class="block font-medium mb-1">Total belanja (Rp)</label>
                        <input type="number" name="subtotal" min="1" value="{{ old('subtotal') }}" required
                               class="w-full border-gray-300 rounded">
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Voucher</label>
                        <select name="member_voucher_id" class="w-full border-gray-300 rounded">
                            <option value="">Tanpa voucher</option>
                            @foreach ($member->vouchers as $mv)
                                <option value="{{ $mv->id }}">
                                    {{ $mv->voucher->name }}
                                    @if ($mv->voucher->min_purchase > 0)
                                        (min. Rp{{ number_format($mv->voucher->min_purchase, 0, ',', '.') }})
                                    @endif
                                    · s/d {{ $mv->expires_at->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="w-full py-3 bg-green-600 text-white font-semibold rounded">Simpan Transaksi</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>