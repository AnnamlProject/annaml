@extends('layouts.app')

@section('content')

    <div class="max-w-full mx-auto bg-white shadow-md rounded-xl p-8 mt-6">

        <form method="POST"
            action="{{ isset($sales_taxes) ? route('sales_taxes.update', $sales_taxes->id) : route('sales_taxes.store') }}">
            @csrf
            @if (isset($sales_taxes))
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
            <h3 class="font-bold text-lg mb-3">Sales-Taxes Create</h3>


            <div class="grid grid-cols-4 gap-4 text-sm">
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                    <input type="text" id="name" name="name"
                        class="w-1/8 border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('name', $sales_taxes->name ?? '') }}">
                </div>
                <div class="mb-5">
                    <label for="rate" class="block text-sm font-medium text-gray-700 mb-1">Dalam Persen(%)</label>
                    <input type="text" id="rate" name="rate"
                        class="w-1/8 border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh : 10" value="{{ old('rate', $sales_taxes->rate ?? '') }}">
                </div>
                <div class="mb-5">
                    <label for="purchase_account_id" class="block text-sm font-medium text-gray-700 mb-1">Purchase
                        Account</label>
                    <select name="purchase_account_id" id="purchase_account_id"
                        class="account-select w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                        <option value="">-- Pilih Account --</option>
                        @foreach ($account as $g)
                            <option value="{{ $g->id }}"
                                {{ isset($sales_taxes) && $sales_taxes->purchase_account_id == $g->id ? 'selected' : '' }}>
                                {{ $g->kode_akun }} - {{ $g->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="sales_account_id" class="block text-sm font-medium text-gray-700 mb-1">Sales Account</label>
                    <select name="sales_account_id" id="sales_account_id"
                        class="account-select w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50">
                        <option value="">-- Pilih Account --</option>
                        @foreach ($account as $g)
                            <option value="{{ $g->id }}"
                                {{ isset($sales_taxes) && $sales_taxes->sales_account_id == $g->id ? 'selected' : '' }}>
                                {{ $g->kode_akun }} - {{ $g->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>




                <div>
                    <label for="active" class="block text-sm font-medium text-gray-700 mb-1">Active</label>
                    <select name="active" id="active"
                        class="w-1/8 border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end">
                <a href="{{ route('sales_taxes.index') }}"
                    class="mr-3 inline-block px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                    Batal
                </a>
                <button type="submit"
                    class="inline-block px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                    {{ isset($sales_taxes) ? '💾 Update' : '✅ Simpan' }}
                </button>
            </div>
        </form>
    </div>
@endsection
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- Select2 JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.account-select').select2({
            placeholder: "Cari akun...",
            allowClear: true
        });
    });
</script>
