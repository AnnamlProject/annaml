@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @php
                $themeColor = \App\Setting::get('theme_color', '#4F46E5');
            @endphp
            <div class="bg-white shadow-lg rounded-xl p-6 border-t-4" style="border-color:{{ $themeColor }}">
                <div id="tabs" class="type-section">
                    <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600" role="tablist">
                        <li><a href="#select_item" class="tab-link active">Proces Payment</a></li>
                        <li><a href="#journal_report" class="tab-link">Journal report</a></li>
                    </ul>
                </div>
                <form method="POST"
                    action="{{ isset($payment) ? route('payment.update', $payment->id) : route('payment.store') }}">
                    @csrf
                    @if (isset($payment))
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 p-4 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h4 class="font-semibold text-lg text-gray-800 mt-8 mb-4 border-l-4 border-blue-500 pl-2">
                        Payment Create
                    </h4>
                    <div class="tab-content" id="select_item">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Nama payment_asset -->
                            <div>
                                <label class="block font-medium mb-1">Payment Method</label>
                                <select id="jenis_pembayaran_id" name="jenis_pembayaran_id"
                                    class="w-full border rounded px-2 py-1 text-sm" required>
                                    <option value="">-- Payment Method --</option>
                                    @foreach ($jenis_pembayaran as $level)
                                        <option value="{{ $level->id }}"
                                            {{ old('jenis_pembayaran_id', $payment->jenis_pembayaran_id ?? '') == $level->id ? 'selected' : '' }}>
                                            {{ $level->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Kolom Kanan: Account (otomatis terisi, 1 saja) --}}
                            <div id="pm-account-panel"
                                class="{{ old('jenis_pembayaran_id', $payment->jenis_pembayaran_id ?? '') ? '' : 'hidden' }}">
                                <label class="block font-medium mb-1">Account</label>
                                <select id="pm-account-id" name="payment_method_account_id"
                                    class="w-full border rounded px-2 py-1 text-sm">
                                    <option value="">-- Pilih Account --</option>
                                </select>
                            </div>

                            {{-- from account --}}

                            {{-- source --}}
                            <div>
                                <label for="source" class="block text-gray-700 font-medium mb-1">Source
                                </label>
                                <input type="text" id="name" name="source" placeholder="Masukkan source" required
                                    value="{{ old('source', $payment->source ?? '') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('source')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vendor" class="block text-gray-700 font-medium mb-1">Vendor
                                </label>
                                <select name="vendor_id" id="vendor_id"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($vendor as $level)
                                        <option value="{{ $level->id }}"
                                            {{ old('vendor_id', $payment->vendor_id ?? '') == $level->id ? 'selected' : '' }}>
                                            {{ $level->nama_vendors }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="payment_date" class="block text-gray-700 font-medium mb-1">Payment Date
                                </label>
                                <input type="date" id="name" name="payment_date" required
                                    value="{{ old('payment_date', $payment->payment_date ?? '') }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('payment_date')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Order Items Table -->
                        <div class="mt-10">
                            <h3 class="text-lg font-semibold mb-4">Order Items</h3>

                            <!-- Tabel untuk Type: Invoice -->
                            <div id="invoice-section" class="hidden mt-8">
                                <h3 class="text-lg font-semibold mb-3">Invoice & Prepayment</h3>
                                <table class="w-full border text-sm">
                                    <thead>
                                        <tr class="bg-gray-100 text-center">
                                            <th class="border px-2 py-1">Tanggal</th>
                                            <th class="border px-2 py-1">Tipe</th>
                                            <th class="border px-2 py-1">Nomor</th>
                                            <th class="border px-2 py-1">Original Amount</th>
                                            <th class="border px-2 py-1">Amount Owing</th>
                                            <th class="border px-2 py-1">Payment Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice-body">
                                        <tr>
                                            <td colspan="6" class="text-center py-2 text-gray-500">Pilih vendor untuk
                                                menampilkan data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Tabel untuk Type: Other -->
                            <div id="other-table" class="hidden mt-6">
                                <h3 class="text-lg font-semibold mb-4">Other Payment Table</h3>
                                <table class="w-full border">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border px-2 py-1">Account</th>
                                            <th class="border px-2 py-1">Description</th>
                                            <th class="border px-2 py-1">Amount</th>
                                            <th class="border px-2 py-1">Tax</th>
                                            <th class="border px-2 py-1">Allocation</th>
                                        </tr>
                                    </thead>
                                    <tbody id="other-body">
                                        @for ($i = 0; $i < 10; $i++)
                                            <tr>
                                                <td class="border px-2 py-1">
                                                    <select name="other_details[{{ $i }}][account]"
                                                        class="w-full border">
                                                        <option value="">-- Select Account --</option>
                                                        @foreach ($account as $a)
                                                            <option value="{{ $a->id }}">{{ $a->kode_akun }} -
                                                                {{ $a->nama_akun }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="border px-2 py-1"><input type="text"
                                                        name="other_details[{{ $i }}][description]"
                                                        class="w-full border"></td>
                                                <td class="border px-2 py-1"><input type="number" step="0.01"
                                                        name="other_details[{{ $i }}][amount]"
                                                        class="w-full border text-right"></td>
                                                <td class="border px-2 py-1"><input type="number" step="0.01"
                                                        name="other_details[{{ $i }}][tax]"
                                                        class="w-full border text-right"></td>
                                                <td class="border px-2 py-1"><input type="text"
                                                        name="other_details[{{ $i }}][allocation]"
                                                        class="w-full border"></td>
                                            </tr>
                                        @endfor
                                    </tbody>

                                </table>
                                <button type="button" id="add-other-row"
                                    class="mt-2 bg-green-600 text-white px-4 py-1 rounded">+ Add Row</button>
                            </div>

                            <!-- Comment -->
                            <div class="mb-4 md:col-span-2 mt-6">
                                <label for="comment" class="block text-gray-700 font-medium mb-1">Comment</label>
                                <textarea id="comment" name="comment" rows="3"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comment', $payment->comment ?? '') }}</textarea>
                                @error('comment')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="journal_report" class="tab-content hidden mt-6">
                        <h2 class="text-lg font-semibold mb-4">Journal Report</h2>
                        <table class="w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border text-left px-2 py-1">Account</th>
                                    <th class="border text-right px-2 py-1">Debit</th>
                                    <th class="border text-right px-2 py-1">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="journal-body">
                                <tr>
                                    <td colspan="3" class="text-center py-2 text-gray-500">Tidak ada journal</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50 font-semibold">
                                <tr>
                                    <td class="border px-2 py-1 text-right">Total</td>
                                    <td class="border px-2 py-1 text-right total-debit">0.00</td>
                                    <td class="border px-2 py-1 text-right total-credit">0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="{{ route('payment.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition">
                            {{ isset($payment) ? 'Update' : 'Process' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- JQUERY & SELECT2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- FORMAT ANGKA -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.number-format');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, '');
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                });
            });
        });
    </script>

    <!-- TAB SWITCHING -->
    <script>
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                this.classList.add('active');
                const target = document.querySelector(this.getAttribute('href'));
                target.classList.remove('hidden');
            });
        });
    </script>

    <!-- LOAD INVOICE & PREPAYMENT BERDASARKAN VENDOR -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#vendor_id').change(function() {
                let vendorId = $(this).val();
                if (!vendorId) return;

                $('#invoice-section').removeClass('hidden');
                $('#invoice-body').html(
                    '<tr><td colspan="6" class="text-center py-2">Loading...</td></tr>');

                $.get(`/vendor/${vendorId}/invoices-prepayments`, function(response) {
                    let rows = '';

                    // PURCHASE INVOICES
                    response.invoices.forEach(i => {
                        rows += `
                <tr class="text-right invoice-row"
                    data-invoice-id="${i.id}"
                    data-header-account-id="${i.header_account_id}"
                    data-header-account-code="${i.header_account_code}"
                    data-header-account-name="${i.header_account_name}">
                    <td class="border px-2 py-1 text-center">${i.date_invoice ?? ''}</td>
                    <td class="border px-2 py-1 text-center">Invoice</td>
                    <td class="border px-2 py-1 text-left">${i.invoice_number ?? ''}</td>
                    <td class="border px-2 py-1">${Number(i.original_amount ?? 0).toLocaleString()}</td>
                    <td class="border px-2 py-1">${Number(i.amount_owing ?? 0).toLocaleString()}</td>
                    <td class="border px-2 py-1">
                        <input type="number" step="0.01" name="payment_amount[${i.id}]"
                            class="w-full border text-right px-2 py-1 payment-input">
                    </td>
                </tr>`;
                    });

                    // PREPAYMENTS
                    response.prepayments.forEach(p => {
                        rows += `
                <tr class="text-right bg-yellow-50">
                    <td class="border px-2 py-1 text-center">${p.tanggal_prepayment ?? ''}</td>
                    <td class="border px-2 py-1 text-center">Prepayment</td>
                    <td class="border px-2 py-1 text-left">${p.reference ?? ''}</td>
                    <td class="border px-2 py-1">${Number(p.amount ?? 0).toLocaleString()}</td>
                    <td class="border px-2 py-1">0.00</td>
                    <td class="border px-2 py-1">
                        <input type="number" step="0.01" name="prepayment_allocations[${p.id}]"
                            class="w-full border text-right px-2 py-1">
                    </td>
                </tr>`;
                    });

                    $('#invoice-body').html(rows ||
                        '<tr><td colspan="6" class="text-center py-2 text-gray-500">Tidak ada invoice atau prepayment untuk vendor ini</td></tr>'
                    );
                }).fail(() => {
                    $('#invoice-body').html(
                        '<tr><td colspan="6" class="text-center text-red-600 py-2">Gagal memuat data vendor.</td></tr>'
                    );
                });
            });
        });
    </script>

    <!-- PAYMENT METHOD => ACCOUNT -->
    <script>
        (function() {
            const $pmSelect = $('#jenis_pembayaran_id');
            const $panel = $('#pm-account-panel');
            const $disp = $('#pm-account-display');
            const $hiddenId = $('#pm-account-id');

            function clearAccount() {
                $disp.val('');
                $hiddenId.val('');
                $panel.addClass('hidden');
            }

            function loadPMAccounts(pmId) {
                if (!pmId) {
                    clearAccount();
                    return;
                }

                $.getJSON("{{ route('payment-methods.accounts', ['id' => 'PM_ID']) }}".replace('PM_ID', pmId))
                    .done(function(res) {
                        const $select = $('#pm-account-id');
                        $select.empty().append('<option value="">-- Pilih Account --</option>');

                        (res.accounts || []).forEach(function(a) {
                            const text = `${a.kode_akun || '-'} - ${a.nama_akun || '-'}`;
                            $select.append(
                                `<option value="${a.detail_id}" data-kode="${a.kode_akun}">${text}</option>`
                            );
                        });

                        const oldVal =
                            "{{ old('account_detail_coa_id', $payment->account_detail_coa_id ?? '') }}";
                        if (oldVal) $select.val(oldVal);
                        $panel.removeClass('hidden');
                    })
                    .fail(function() {
                        clearAccount();
                        alert('Gagal memuat account dari Payment Method.');
                    });
            }

            $pmSelect.on('change', function() {
                loadPMAccounts($(this).val());
            });

            const initial = $pmSelect.val();
            if (initial) loadPMAccounts(initial);
        })();
    </script>

    <!-- JOURNAL PREVIEW -->
    <script>
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        // function logDebug(label, value) {
        //     console.log(`%c[DEBUG] ${label}:`, 'color: #16a34a; font-weight: bold;', value);
        // }

        // Tampilkan pesan debugging di halaman
        function displayDebugMessage(msg) {
            let debugBox = document.getElementById('debug-box');
            if (!debugBox) {
                const container = document.createElement('div');
                container.id = 'debug-box';
                container.className = 'mt-4 p-3 border border-yellow-400 bg-yellow-50 text-sm text-gray-800 rounded';
                document.querySelector('#journal_report').appendChild(container);
                debugBox = container;
            }
            // debugBox.innerHTML = `<strong>🧩 Debug Info:</strong><br>${msg}`;
        }

        function generateJournalPreview() {
            // logDebug('generateJournalPreview', 'Dipanggil!');
            const journalBody = document.querySelector('.journal-body');
            journalBody.innerHTML = '';

            let rows = [];
            let totalDebit = 0,
                totalCredit = 0;

            // 1️⃣ Ambil akun payment (Credit)
            const pmAccountName = $('#pm-account-id option:selected').text();
            const pmAccountCode = $('#pm-account-id option:selected').data('kode') || '';
            // logDebug('pmAccountName', pmAccountName);
            // logDebug('pmAccountCode', pmAccountCode);

            // 2️⃣ Cari invoice yang diisi jumlah pembayaran
            let selectedInvoice = null;
            let amount = 0;

            $('.payment-input').each(function() {
                const val = parseFloat($(this).val()) || 0;
                if (val > 0) {
                    selectedInvoice = $(this).closest('.invoice-row');
                    amount = val;
                }
            });

            // logDebug('selectedInvoice', selectedInvoice ? selectedInvoice.data() : null);
            // logDebug('amount', amount);

            // 3️⃣ Jika belum ada invoice / amount, tampilkan pesan
            if (!selectedInvoice || amount <= 0) {
                journalBody.innerHTML =
                    `<tr><td colspan="3" class="text-center py-2 text-gray-500">Tidak ada journal (belum isi payment amount)</td></tr>`;
                displayDebugMessage('Belum ada invoice dengan payment amount > 0.');
                return;
            }

            // 4️⃣ Ambil akun header dari data atribut invoice
            const headerAccountName = selectedInvoice.data('header-account-name');
            const headerAccountCode = selectedInvoice.data('header-account-code');

            // logDebug('headerAccountName', headerAccountName);
            // logDebug('headerAccountCode', headerAccountCode);

            // if (!headerAccountCode || !headerAccountName) {
            //     displayDebugMessage(
            //         '⚠️ Data header_account belum tersedia dari invoice (pastikan API vendor mengembalikannya).');
            //     return;
            // }

            // 5️⃣ Buat baris journal
            rows.push({
                accountCode: headerAccountCode,
                account: headerAccountName,
                debit: amount,
                credit: 0
            });
            totalDebit += amount;

            rows.push({
                account: pmAccountName,
                debit: 0,
                credit: amount
            });
            totalCredit += amount;

            // logDebug('rows', rows);

            // 6️⃣ Render ke tabel
            if (rows.length === 0) {
                journalBody.innerHTML =
                    `<tr><td colspan="3" class="text-center py-2 text-gray-500">Tidak ada journal</td></tr>`;
            } else {
                rows.forEach(r => {
                    journalBody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="border px-2 py-1">${r.accountCode || ''} - ${r.account || ''}</td>
                    <td class="border px-2 py-1 text-right">${formatNumber(r.debit)}</td>
                    <td class="border px-2 py-1 text-right">${formatNumber(r.credit)}</td>
                </tr>
            `);
                });
            }

            // 7️⃣ Total
            document.querySelector('.total-debit').textContent = formatNumber(totalDebit);
            document.querySelector('.total-credit').textContent = formatNumber(totalCredit);

            //         displayDebugMessage(`
        //     ✅ generateJournalPreview() berhasil dijalankan.<br>
        //     <b>PM Account:</b> ${pmAccountCode || '-'} - ${pmAccountName || '-'}<br>
        //     <b>Header Account:</b> ${headerAccountCode || '-'} - ${headerAccountName || '-'}<br>
        //     <b>Amount:</b> ${amount.toLocaleString('id-ID')}
        // `);
        }

        // 🔁 Trigger otomatis
        document.addEventListener('DOMContentLoaded', function() {
            // Pakai event delegation biar tetap nyala walau invoice-body di-refresh
            $(document).on('input change', '#pm-account-id, .payment-input', generateJournalPreview);

            // Jalankan awal
            generateJournalPreview();
        });
    </script>


    <!-- SELECT2 VENDOR -->
    <script>
        $(document).ready(function() {
            $('#vendor_id').select2({
                placeholder: "-- Vendor --",
                ajax: {
                    url: '{{ route('vendors.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(vendor) {
                                return {
                                    id: vendor.id,
                                    text: vendor.nama_vendors
                                };
                            })
                        };
                    },
                    cache: true
                },
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <!-- ❌ BAGIAN TYPE (TIDAK DIPAKAI LAGI) -->
    {{-- 
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    if (!typeSelect) return; // dicegah error
    const invoiceTable = document.getElementById('invoice-table');
    const otherTable = document.getElementById('other-table');

    function toggleTable(value) {
        if (value === 'Invoice') {
            invoiceTable.classList.remove('hidden');
            otherTable.classList.add('hidden');
        } else if (value === 'Other') {
            invoiceTable.classList.add('hidden');
            otherTable.classList.remove('hidden');
        } else {
            invoiceTable.classList.add('hidden');
            otherTable.classList.add('hidden');
        }
    }

    typeSelect.addEventListener('change', function() {
        toggleTable(this.value);
    });
    toggleTable(typeSelect.value);
});
</script>
--}}
@endsection
