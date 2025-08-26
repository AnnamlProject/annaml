@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 py-8">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Card Input RFID --}}
            <div class="bg-white shadow-lg rounded-xl p-8">
                <h2 class="text-xl font-semibold text-center text-gray-700 mb-6">
                    Silakan Tempel Kartu
                </h2>

                <form id="rfidForm" action="{{ route('absensi.scan') }}" method="post" class="flex flex-col">
                    @csrf
                    <input type="text" name="rfid" id="rfidInput" autofocus autocomplete="off"
                        placeholder="Tempelkan Kartu di Scanner"
                        class="border border-gray-300 rounded-lg px-4 py-2 
                               focus:outline-none focus:ring-2 focus:ring-blue-400 
                               text-center text-lg" />
                </form>

                {{-- Success Message --}}
                @if (session('success'))
                    <div
                        class="mt-4 px-4 py-2 text-green-700 bg-green-100 
                                border border-green-300 rounded-lg text-center">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(() => window.location.href = "{{ route('absensi.form') }}", 3000);
                    </script>
                @endif

                {{-- Error Message --}}
                @if (session('error'))
                    <div
                        class="mt-4 px-4 py-2 text-red-700 bg-red-100 
                                border border-red-300 rounded-lg text-center">
                        {{ session('error') }}
                    </div>
                    <script>
                        setTimeout(() => window.location.href = "{{ route('absensi.form') }}", 3000);
                    </script>
                @endif
            </div>

            {{-- Tabel Data Absensi --}}
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Data Absensi Terbaru</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Tanggal</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Jam</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensis as $absen)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $absen->employee->nama_karyawan }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $absen->tanggal }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $absen->jam }}
                                    </td>
                                    <td
                                        class="px-4 py-2 text-sm font-semibold 
                                               {{ $absen->status === 'Masuk' ? 'text-green-600' : 'text-blue-600' }}">
                                        {{ $absen->status }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                        Belum ada data absensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        let submitted = false;
        document.getElementById('rfidInput').addEventListener('change', function() {
            if (!submitted && this.value.trim() !== "") {
                submitted = true;
                document.getElementById('rfidForm').submit();
            }
        });
    </script>
@endsection
