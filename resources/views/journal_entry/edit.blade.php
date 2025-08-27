@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="font-bold text-lg mb-2">Journal Entry Edit</h2>

                <form id="journal-entry-form" action="{{ route('journal_entry.update', $journalEntry->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 text-base">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Source</label>
                            <input type="text" name="source" class="w-full rounded-md border border-gray-300 px-3 py-2"
                                value="{{ old('source', $journalEntry->source) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                            <input type="date" name="tanggal" class="w-full rounded-md border border-gray-300 px-3 py-2"
                                value="{{ old('tanggal', $journalEntry->tanggal) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Comment</label>
                            <input type="text" name="comment" class="w-full rounded-md border border-gray-300 px-3 py-2"
                                value="{{ old('comment', $journalEntry->comment) }}">
                        </div>
                    </div>

                    <div class="overflow-x-auto overflow-y-auto max-h-[450px] mb-6 border rounded">
                        <table class="min-w-full table-auto border-collapse text-base text-left bg-white">
                            <thead class="bg-gray-100 text-gray-700 font-semibold sticky top-0 z-10">
                                <tr>
                                    <th class="border px-4 py-3 w-[30%]">Accounts</th>
                                    <th class="border px-4 py-3 w-[10%] text-center">Debits</th>
                                    <th class="border px-4 py-3 w-[10%] text-center">Credits</th>
                                    <th class="border px-4 py-3 w-[45%] text-center">Comment</th>
                                    <th class="border px-4 py-3 w-[5%] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="item-table-body" class="bg-white">
                                @foreach ($journalEntry->details as $i => $item)
                                    <tr class="item-row" data-index="{{ $i }}">
                                        <td class="border px-2 py-1">
                                            <select class="item-select w-full border rounded"
                                                name="details[{{ $i }}][kode_akun]"
                                                data-index="{{ $i }}">
                                                <option value="{{ $item->kode_akun }}" selected>
                                                    @if ($item->departemen_akun_id)
                                                        {{ $item->kode_akun }} -
                                                        {{ $item->departemenAkun->kode ?? '-' }} -
                                                        {{ $item->chartOfAccount->nama_akun ?? 'Akun lama' }} -
                                                        {{ $item->departemenAkun->deskripsi ?? '-' }}
                                                    @else
                                                        {{ $item->kode_akun }} -
                                                        {{ $item->chartOfAccount->nama_akun ?? 'Akun lama' }}
                                                    @endif
                                                </option>
                                            </select>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="hidden" name="items[{{ $i }}][kode_akun]"
                                                class="kode_akun-{{ $i }}" value="{{ $item->kode_akun }}" />
                                            <input type="hidden" name="items[{{ $i }}][departemen_akun_id]"
                                                class="departemen-akun-{{ $i }}"
                                                value="{{ $item->departemen_akun_id }}" />
                                            <input type="text" name="items[{{ $i }}][debits]"
                                                class="money-input w-full border rounded px-2 py-1 text-right"
                                                inputmode="numeric"
                                                value="{{ number_format($item->debits, 0, ',', '.') }}" />
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="text" name="items[{{ $i }}][credits]"
                                                class="money-input w-full border rounded px-2 py-1 text-right"
                                                inputmode="numeric"
                                                value="{{ number_format($item->credits, 0, ',', '.') }}" />
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="text" name="items[{{ $i }}][comment]"
                                                class="w-full border rounded px-2 py-1" value="{{ $item->comment }}" />
                                        </td>
                                        <td class="border px-2 py-1 text-center">
                                            <button type="button"
                                                class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                                data-index="{{ $i }}">X</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 flex justify-end gap-4">
                        <button type="button" onclick="history.go(-1)"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md">
                            <i class="fas fa-arrow-left mr-2"></i> Batal
                        </button>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let rowIndex = {{ count($journalEntry->details) }};

    function generateRow(index) {
        return `...`; // tetap seperti yang Anda punya
    }


    function attachSelect2(index) {
        $(`select[data-index="${index}"]`).select2({
            placeholder: 'Cari Account...',
            ajax: {
                url: '/search-account',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term
                }),
                processResults: data => {
                    let results = [];

                    data.forEach(item => {
                        // Akun utama (tanpa departemen)
                        results.push({
                            id: item.id,
                            text: `${item.kode_akun} - ${item.nama_akun}`,
                            kode_akun: item.kode_akun,
                            departemen_akun_id: null
                        });

                        // Akun dengan departemen
                        if (item.daftar_departemen && item.daftar_departemen.length > 0) {
                            item.daftar_departemen.forEach(dept => {
                                results.push({
                                    id: `d-${dept.id}`, // id unik biar tidak bentrok
                                    text: `${item.kode_akun} - ${item.nama_akun} ${dept.deskripsi}`,
                                    kode_akun: item.kode_akun,
                                    departemen_akun_id: dept.id,
                                    deskripsi_departemen: dept.deskripsi
                                });
                            });
                        }
                    });

                    return {
                        results
                    };
                },

                cache: true
            },
            templateResult: function(data) {
                return data.text; // cukup tampilkan text biasa
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            $(`.kode_akun-${index}`).val(data.kode_akun);

            // Simpan departemen_akun_id kalau ada
            const departemenAkunId = data.departemen_akun_id ?? '';
            $(`.departemen-akun-${index}`).val(departemenAkunId);
        });
    }

    function unformatAllCurrencyInputs() {
        document.querySelectorAll('.money-input').forEach(input => {
            const raw = input.value.replace(/\./g, '');
            input.value = raw === '' ? '' : parseFloat(raw);
        });
    }

    $(document).ready(function() {
        // untuk select2 di data existing
        $('.item-select').each(function() {
            const index = $(this).data('index');
            attachSelect2(index);
        });

        $(document).on('click', '.remove-row', function() {
            const index = $(this).data('index');
            $(`tr[data-index="${index}"]`).remove();
        });

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('money-input')) {
                const raw = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = raw === '' ? '' : new Intl.NumberFormat('id-ID').format(raw);
            }
        });

        document.getElementById('journal-entry-form').addEventListener('submit', function() {
            unformatAllCurrencyInputs();
        });
    });
</script>
