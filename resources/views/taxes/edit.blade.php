@extends('layouts.app')

@section('content')
    <div class="py-10">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

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

                <form action="{{ route('taxes.update', $tax->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                <option value="{{ $num }}" {{ $tax->bulan == $num ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilih Tahun --}}
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control" required>
                            <option value="">-- Pilih Tahun --</option>
                            @for ($year = date('Y'); $year >= 2000; $year--)
                                <option value="{{ $year }}" {{ $tax->tahun == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Jenis Pajak --}}
                    <div class="mb-3">
                        <label for="jenis_pajak" class="form-label">Jenis Pajak</label>
                        <input type="text" name="jenis_pajak" id="jenis_pajak" class="form-control"
                            value="{{ old('jenis_pajak', $tax->jenis_pajak) }}" required>
                    </div>

                    {{-- Jenis Dokumen --}}
                    <div class="mb-3">
                        <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                        <input type="text" name="jenis_dokumen" id="jenis_dokumen" class="form-control"
                            value="{{ old('jenis_dokumen', $tax->jenis_dokumen) }}" required>
                    </div>

                    {{-- Upload File --}}
                    <div class="mb-3">
                        <label for="file_path" class="form-label">Upload File (kosongkan jika tidak diganti)</label>
                        <input type="file" name="file_path" id="file_path" class="form-control">

                        @if ($tax->file_path)
                            <small>File lama: <a href="{{ asset('storage/' . $tax->file_path) }}" target="_blank">Lihat
                                    File</a></small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
