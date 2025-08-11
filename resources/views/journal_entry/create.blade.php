@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <form id="journal-entry-form" action="{{ route('journal_entry.store') }}" method="POST">
                    @csrf

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

                    <!-- Header -->


                    <!-- Form Inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 text-base">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Source</label>
                            <input type="text" name="source" class="w-full rounded-md border border-gray-300 px-3 py-2"
                                value="{{ old('source') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="w-full rounded-md border border-gray-300 px-3 py-2" value="{{ old('tanggal') }}"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Comment</label>
                            <input type="text" name="comment" class="w-full rounded-md border border-gray-300 px-3 py-2"
                                value="{{ old('comment') }}">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto overflow-y-auto max-h-[450px] mb-6 border rounded">
                        <table class="min-w-full table-auto border-collapse text-base text-left bg-white">
                            <thead class="bg-gray-100 text-gray-700 font-semibold sticky top-0 z-10">
                                <tr>
                                    <th class="border px-4 py-3 text-center w-[30%]">Accounts</th>
                                    <th class="border px-4 py-3 text-center w-[10%]">Debits</th>
                                    <th class="border px-4 py-3 text-center w-[10%]">Credits</th>
                                    <th class="border px-4 py-3 text-center w-[45%]">Comment</th>
                                    <th class="border px-4 py-3 text-center w-[5%]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="item-table-body" class="bg-white">

                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="{{ route('journal_entry.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-medium rounded-md">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- File Modal -->
    <div id="fileModal"
        class="fixed inset-0 z-50 hidden bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-file-alt mr-2 text-blue-400"></i> File Aksi
            </h3>
            <div class="space-y-3 text-sm text-gray-700">
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700">Import File Excel:</label>
                    <input type="file" name="file" class="block w-full text-sm border rounded px-2 py-1" required>
                    <button type="submit" class="bg-green-500 text-white w-full py-1 rounded hover:bg-green-600 text-sm">
                        <i class="fas fa-file-upload mr-1"></i> Import
                    </button>
                </form>
            </div>
            <div class="mt-4 text-right">
                <button onclick="document.getElementById('fileModal').classList.add('hidden')"
                    class="text-sm text-gray-500 hover:text-gray-700">Tutup</button>
            </div>
        </div>
    </div>



@endsection

@push('scripts')
    <!-- Include Select2 CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- JS for dropdown toggle -->
    <script>
        // Ambil data saat ganti tanggal untuk preview list
        $('#tanggal').on('change', function() {
            let tanggal = $(this).val();

            $.ajax({
                url: '/journal-entry/auto-data',
                type: 'GET',
                data: {
                    tanggal: tanggal
                },
                success: function(res) {
                    if (res.entries.length > 0) {
                        let html = '<ul>';
                        res.entries.forEach(e => {
                            html +=
                                `<li>Account: ${e.kode_akun}, Debit: ${e.total_debit}, Credit: ${e.total_credit}</li>`;
                        });
                        html += '</ul>';
                        $('#result').html(html);
                    } else {
                        $('#result').html('<p>Tidak ada data untuk tanggal ini</p>');
                    }
                }
            });
        });

        let rowIndex = 0;

        function generateRow(index) {
            return `
        <tr class="item-row" data-index="${index}">
            <td class="border px-2 py-1">
                <select class="item-select w-full border rounded" data-index="${index}"></select>
            </td>
            <td class="border px-2 py-1">
                <input type="hidden" name="items[${index}][kode_akun]" class="kode_akun-${index}" />
                <input type="hidden" name="items[${index}][departemen_akun_id]" class="departemen-akun-${index}" />
                <input type="text" name="items[${index}][debits]" class="money-input w-full border rounded px-2 py-1 text-right debit-${index}" inputmode="numeric" />
            </td>
            <td class="border px-2 py-1">
                <input type="text" name="items[${index}][credits]" class="money-input w-full border rounded px-2 py-1 text-right credit-${index}" inputmode="numeric" />
            </td>
            <td class="border px-2 py-1">
                <input type="text" name="items[${index}][comment]" class="w-full border rounded px-2 py-1" />
            </td>
            <td class="border px-2 py-1 text-center">
                <button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" data-index="${index}">X</button>
            </td>
        </tr>`;
        }

        function attachSelect2(index) {
            $(`select[data-index="${index}"]`).select2({
                placeholder: 'Cari Akun...',
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

                            if (item.daftar_departemen && item.daftar_departemen.length > 0) {
                                item.daftar_departemen.forEach(dept => {
                                    results.push({
                                        id: `d-${dept.id}`,
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
                    return data.text;
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                $(`.kode_akun-${index}`).val(data.kode_akun);
                $(`.departemen-akun-${index}`).val(data.departemen_akun_id ?? '');

                // Ambil data debit & credit dari server berdasarkan tanggal
                $.ajax({
                    url: '/journal-entry/auto-data',
                    type: 'GET',
                    data: {
                        tanggal: $('#tanggal').val()
                    },
                    success: function(res) {
                        if (res.entries && res.entries.length > 0) {
                            const found = res.entries.find(item => item.kode_akun === data.kode_akun);
                            if (found) {
                                $(`.debit-${index}`).val(new Intl.NumberFormat('id-ID').format(found
                                    .total_debit || 0));
                                $(`.credit-${index}`).val(new Intl.NumberFormat('id-ID').format(found
                                    .total_credit || 0));
                            }
                        }
                    }
                });
            });
        }

        function addRow() {
            const newRow = generateRow(rowIndex);
            $('#item-table-body').append(newRow);
            attachSelect2(rowIndex);
            rowIndex++;
        }

        function formatNumberInput(input) {
            const raw = input.value.replace(/[^0-9]/g, '');
            input.value = raw === '' ? '' : new Intl.NumberFormat('id-ID').format(raw);
        }

        function unformatAllCurrencyInputs() {
            document.querySelectorAll('.money-input').forEach(input => {
                const raw = input.value.replace(/\./g, '');
                input.value = raw === '' ? '' : parseFloat(raw);
            });
        }

        $(document).ready(function() {
            for (let i = 0; i < 30; i++) addRow();

            $('#add-row').on('click', function() {
                addRow();
            });

            $(document).on('click', '.remove-row', function() {
                const index = $(this).data('index');
                $(`tr[data-index="${index}"]`).remove();
            });

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('money-input')) {
                    formatNumberInput(e.target);
                }
            });

            document.getElementById('journal-entry-form').addEventListener('submit', function() {
                unformatAllCurrencyInputs();
            });
        });
    </script>
@endpush
