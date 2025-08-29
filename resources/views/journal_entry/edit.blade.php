@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="font-bold text-lg mb-2">Journal Entry Edit</h2>

                <form id="journal-entry-form" action="{{ route('journal_entry.update', $journalEntry->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Header --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 text-base">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Source</label>
                            <input type="text" name="source" class="w-full rounded-md border px-3 py-2"
                                value="{{ old('source', $journalEntry->source) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                            <input type="date" name="tanggal" class="w-full rounded-md border px-3 py-2"
                                value="{{ old('tanggal', $journalEntry->tanggal) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Comment</label>
                            <input type="text" name="comment" class="w-full rounded-md border px-3 py-2"
                                value="{{ old('comment', $journalEntry->comment) }}">
                        </div>
                    </div>

                    {{-- Table --}}
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
                                @php
                                    $totalDebit = 0;
                                    $totalKredit = 0;
                                @endphp

                                @foreach ($journalEntry->details as $i => $detail)
                                    @php
                                        $totalDebit += $detail->debits;
                                        $totalKredit += $detail->credits;
                                    @endphp

                                    <tr class="item-row" data-index="{{ $i }}">
                                        <td class="border px-2 py-1">
                                            <select class="item-select w-full border rounded"
                                                name="items[{{ $i }}][kode_akun]"
                                                data-index="{{ $i }}">
                                                <option value="{{ $detail->kode_akun }}" selected>
                                                    {{ $detail->kode_akun }} -
                                                    {{ $detail->chartOfAccount->nama_akun ?? '-' }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="hidden" name="items[{{ $i }}][departemen_akun_id]"
                                                value="{{ $detail->departemen_akun_id }}" />
                                            <input type="text" name="items[{{ $i }}][debits]"
                                                class="money-input debit-input w-full border rounded px-2 py-1 text-right"
                                                value="{{ number_format($detail->debits, 0, ',', '.') }}" />
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="text" name="items[{{ $i }}][credits]"
                                                class="money-input credit-input w-full border rounded px-2 py-1 text-right"
                                                value="{{ number_format($detail->credits, 0, ',', '.') }}" />
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="text" name="items[{{ $i }}][comment]"
                                                class="w-full border rounded px-2 py-1" value="{{ $detail->comment }}" />
                                        </td>
                                        <td class="border px-2 py-1 text-center">
                                            <button type="button"
                                                class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">X</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot class="bg-gray-100 font-bold text-gray-800">
                                <tr>
                                    <td class="border px-4 py-2 text-right">TOTAL</td>
                                    <td class="border px-4 py-2 text-right" id="total-debit">
                                        {{ number_format($totalDebit, 0, ',', '.') }}
                                    </td>
                                    <td class="border px-4 py-2 text-right" id="total-credit">
                                        {{ number_format($totalKredit, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Tombol Add Row --}}
                    <div class="mb-6">
                        <button type="button" id="add-row"
                            class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            + Tambah Row
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex justify-end gap-4">
                        <button type="button" onclick="history.go(-1)"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md">
                            Batal
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

@push('scripts')
    <script>
        function parseNumber(value) {
            if (!value) return 0;
            return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
        }

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        }

        function updateTotals() {
            let totalDebit = 0,
                totalCredit = 0;

            document.querySelectorAll('.debit-input').forEach(input => {
                totalDebit += parseNumber(input.value);
            });
            document.querySelectorAll('.credit-input').forEach(input => {
                totalCredit += parseNumber(input.value);
            });

            document.getElementById('total-debit').innerText = formatNumber(totalDebit);
            document.getElementById('total-credit').innerText = formatNumber(totalCredit);
        }

        function generateRow() {
            return `
            <tr class="item-row">
                <td class="border px-2 py-1">
                    <select class="item-select w-full border rounded" name="items[][kode_akun]"></select>
                </td>
                <td class="border px-2 py-1">
                    <input type="hidden" name="items[][departemen_akun_id]" class="departemen-akun">
                    <input type="text" name="items[][debits]"
                           class="money-input debit-input w-full border rounded px-2 py-1 text-right" value="0"/>
                </td>
                <td class="border px-2 py-1">
                    <input type="text" name="items[][credits]"
                           class="money-input credit-input w-full border rounded px-2 py-1 text-right" value="0"/>
                </td>
                <td class="border px-2 py-1">
                    <input type="text" name="items[][comment]" class="w-full border rounded px-2 py-1"/>
                </td>
                <td class="border px-2 py-1 text-center">
                    <button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">X</button>
                </td>
            </tr>
        `;
        }

        function reindexRows() {
            $('#item-table-body tr').each(function(i, row) {
                $(row).attr('data-index', i);
                $(row).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d*\]/, `[${i}]`));
                    }
                });
            });
        }

        function attachSelect2($select) {
            $select.select2({
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
                            results.push({
                                id: item.id,
                                text: `${item.kode_akun} - ${item.nama_akun}`,
                                kode_akun: item.kode_akun,
                                departemen_akun_id: null
                            });
                            if (item.daftar_departemen) {
                                item.daftar_departemen.forEach(dept => {
                                    results.push({
                                        id: `d-${dept.id}`,
                                        text: `${item.kode_akun} - ${item.nama_akun} ${dept.deskripsi}`,
                                        kode_akun: item.kode_akun,
                                        departemen_akun_id: dept.id
                                    });
                                });
                            }
                        });
                        return {
                            results
                        };
                    }
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                $(this).closest('tr').find('.departemen-akun').val(data.departemen_akun_id ?? '');
            });
        }

        $(document).ready(function() {
            $('.item-select').each(function() {
                attachSelect2($(this));
            });

            $('#add-row').click(function() {
                $('#item-table-body').append(generateRow());
                reindexRows();
                updateTotals();
                attachSelect2($('#item-table-body tr:last .item-select'));
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                reindexRows();
                updateTotals();
            });

            $(document).on('input', '.money-input', function() {
                const raw = this.value.replace(/[^0-9]/g, '');
                this.value = raw === '' ? '' : new Intl.NumberFormat('id-ID').format(raw);
                updateTotals();
            });

            $('#journal-entry-form').on('submit', function() {
                document.querySelectorAll('.money-input').forEach(input => {
                    const raw = input.value.replace(/\./g, '');
                    input.value = raw === '' ? '' : parseFloat(raw);
                });
            });

            updateTotals();
        });
    </script>
@endpush
