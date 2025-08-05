@extends('layouts.app')

@section('content')
    <div class="py-10 max-w-full mx-auto px-6">
        <form action="{{ route('unit_kerja.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm space-y-6">
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
            <div>
                <label class="block font-medium text-gray-700">Nama</label>
                <input type="text" name="nama_unit"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="3"></textarea>
            </div>



            <div class="text-right">
                <a href="{{ route('unit_kerja.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
@endsection
