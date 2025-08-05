@extends('layouts.app')

@section('content')
    <div class="py-10 max-w-full mx-auto px-6">
        <form action="{{ route('PaymentMethod.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm space-y-6">
            @csrf

            <div>
                <label class="block font-medium text-gray-700">Kode Method</label>
                <input type="text" name="kode_jenis"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Nama Method</label>
                <input type="text" name="nama_jenis"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            <div>
                <label class="block font-medium text-gray-700">Status Method</label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="1" selected>Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>

            <div class="text-right">
                <a href="{{ route('PaymentMethod.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
@endsection
