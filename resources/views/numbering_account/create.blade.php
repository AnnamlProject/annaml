@extends('layouts.app')

@section('content')
    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm hover:shadow-md rounded-2xl p-8 space-y-6 transition">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Pengaturan Numbering Account</h2>

                <form method="POST"
                    action="{{ isset($numberingAccount) ? route('numbering_account.update', $numberingAccount->id) : route('numbering_account.store') }}">
                    @csrf
                    @if (isset($numberingAccount))
                        @method('PUT')
                    @endif

                    <!-- Jumlah Digit Akun -->
                    <div>
                        <label for="digit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jumlah Digit Akun
                        </label>
                        <select id="digit" name="digit"
                            class="block w-1/6 mt-1 rounded-md shadow-sm 
                            border-gray-600 
                            bg-blue-100 text-blue-900 
                            dark:bg-blue-900 dark:text-white 
                            dark:border-blue-600 
                            focus:ring focus:ring-indigo-200"
                            required>
                            @for ($i = 5; $i <= 8; $i++)
                                <option value="{{ $i }}"
                                    {{ isset($numberingAccount) && $numberingAccount->digit == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>


                    <!-- Tabel Grup Akun -->
                    <div class="mt-8 overflow-x-auto rounded-lg shadow border border-gray-300 dark:border-gray-700">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-indigo-50 dark:bg-gray-700 text-indigo-700 dark:text-gray-200 font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border">Nama Grup</th>
                                    <th class="px-4 py-3 border">Nomor Akun Awal</th>
                                    <th class="px-4 py-3 border">Nomor Akun Akhir</th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $groups = ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'];
                                @endphp
                                @foreach ($groups as $index => $group)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <input type="text" name="nama_grup[]" value="{{ $group }}" readonly
                                                class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="nomor_akun_awal[]"
                                                class="form-control awal w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2"
                                                readonly>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="nomor_akun_akhir[]"
                                                class="form-control akhir w-full bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2"
                                                readonly>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-8 flex flex-wrap gap-4">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            {{ isset($numberingAccount) ? 'Update' : 'Create' }} Numbering Account
                        </button>
                        <a href="{{ route('numbering_account.index') }}"
                            class="inline-flex items-center px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Otomatis -->
    <script>
        document.getElementById('digit').addEventListener('change', function() {
            const digit = parseInt(this.value);
            const awalInputs = document.querySelectorAll('.awal');
            const akhirInputs = document.querySelectorAll('.akhir');
            const baseGroup = [1, 2, 3, 4, 5];

            baseGroup.forEach((base, i) => {
                const start = base * Math.pow(10, digit - 1);
                const end = ((base + 1) * Math.pow(10, digit - 1)) - 1;

                awalInputs[i].value = start;
                akhirInputs[i].value = end;
            });
        });

        // Trigger saat load
        document.getElementById('digit').dispatchEvent(new Event('change'));
    </script>
@endsection
