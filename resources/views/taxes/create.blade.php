@extends('layouts.app')

@section('content')
    <div class="py-10 max-w-full mx-auto px-6">
        <h2>Tambah Data Pajak</h2>

        {{-- Tampilkan error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('taxes.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 rounded-xl shadow-sm space-y-6>
            @csrf

            {{-- Pilih Bulan --}}
            <div class="mb-3">
            <label for="bulan" class="form-label">Bulan</label>
            <select name="bulan" id="bulan" class="form-control" required>
                <option value="">-- Pilih Bulan --</option>
                @foreach ([
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ] as $num => $nama)
                    <option value="{{ $num }}">{{ $nama }}</option>
                @endforeach
            </select>
    </div>

    {{-- Pilih Tahun --}}
    <div class="mb-3">
        <label for="tahun" class="form-label">Tahun</label>
        <select name="tahun" id="tahun" class="form-control" required>
            <option value="">-- Pilih Tahun --</option>
            @for ($year = date('Y'); $year >= 2000; $year--)
                <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </select>
    </div>

    {{-- Jenis Pajak --}}
    <div class="mb-3">
        <label for="jenis_pajak" class="form-label">Jenis Pajak</label>
        <input type="text" name="jenis_pajak" id="jenis_pajak" class="form-control" required>
    </div>

    {{-- Jenis Dokumen --}}
    <div class="mb-3">
        <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
        <input type="text" name="jenis_dokumen" id="jenis_dokumen" class="form-control" required>
    </div>

    {{-- Upload File --}}
    <div class="mb-3">
        <label for="file_path" class="form-label">Upload File</label>
        <input type="file" name="file_path" id="file_path" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Batal</a>
    </form>
    </div>
@endsection
