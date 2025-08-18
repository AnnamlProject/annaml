@extends('layouts.app')
@section('content')
    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
                <form action="{{ route('linkedAccountPurchases.store') }}" method="POST">
                    @csrf

                    {{-- Principal Bank Account --}}
                    <div class="mb-4">
                        <label for="principal_bank_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Principal Bank Account <span class="text-red-500">*</span>
                        </label>
                        <select name="principal_bank_account_id" id="principal_bank_account_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->kode_akun }} -
                                    {{ $account->nama_akun }}</option>
                            @endforeach
                        </select>
                        @error('principal_bank_account_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Account Receivable --}}
                    <div class="mb-4">
                        <label for="account_payable_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Account Payable <span class="text-red-500">*</span>
                        </label>
                        <select name="account_payable_id" id="account_payable_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($akun2 as $account)
                                <option value="{{ $account->id }}">{{ $account->kode_akun }} -
                                    {{ $account->nama_akun }}</option>
                            @endforeach
                        </select>
                        @error('account_payable_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Freight expense --}}
                    <div class="mb-4">
                        <label for="freight_expense_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Freight Expense <span class="text-red-500">*</span>
                        </label>
                        <select name="freight_expense_id" id="freight_expense_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($akun2 as $account)
                                <option value="{{ $account->id }}">{{ $account->kode_akun }} -
                                    {{ $account->nama_akun }}</option>
                            @endforeach
                        </select>
                        @error('freight_expense_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Early Payment Sales Discount --}}
                    <div class="mb-4">
                        <label for="early_payment_purchase_discount_id"
                            class="block text-sm font-medium text-gray-700 mb-1">
                            Early Payment Purchase Discount <span class="text-red-500">*</span>
                        </label>
                        <select name="early_payment_purchase_discount_id" id="early_payment_purchase_discount_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($akun4 as $account)
                                <option value="{{ $account->id }}">{{ $account->kode_akun }} -
                                    {{ $account->nama_akun }}</option>
                            @endforeach
                        </select>
                        @error('early_payment_purchase_discount_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deposits & Prepaid Orders --}}
                    <div class="mb-4">
                        <label for="prepayments_prepaid_orders" class="block text-sm font-medium text-gray-700 mb-1">
                            Prepayments & Prepaid Orders <span class="text-red-500">*</span>
                        </label>
                        <select name="prepayments_prepaid_orders" id="prepayments_prepaid_orders" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($akun1 as $account)
                                <option value="{{ $account->id }}">{{ $account->kode_akun }} -
                                    {{ $account->nama_akun }}</option>
                            @endforeach
                        </select>
                        @error('prepayments_prepaid_orders')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('linkedAccountPurchases.index') }}"
                            class="px-6 py-2 mr-3 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                            Kembali
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
