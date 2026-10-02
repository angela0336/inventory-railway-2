<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Member Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('member.store') }}" class="bg-white p-6 rounded shadow space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="p-4 bg-red-100 text-red-800 rounded">
                        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                    </div>
                @endif

                <div>
                    <label class="block font-medium mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block font-medium mb-1">Nomor HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block font-medium mb-1">Email (opsional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block font-medium mb-1">Tanggal lahir (untuk voucher ulang tahun)</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border-gray-300 rounded">
                </div>

                <button class="w-full py-3 bg-orange-500 text-white font-semibold rounded">Daftarkan Member</button>
            </form>
        </div>
    </div>
</x-app-layout>