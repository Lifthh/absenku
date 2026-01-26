@extends('layouts.admin')

@section('title', 'Data Absensi')
@section('subtitle', "Rekap absensi tanggal " . $tanggal->format('d M Y'))


@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}"
                       class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="izin_sakit" {{ request('status') == 'izin_sakit' ? 'selected' : '' }}>Izin Sakit</option>
                    <option value="izin_dinas" {{ request('status') == 'izin_dinas' ? 'selected' : '' }}>Izin Dinas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIP"
                       class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Filter
            </button>
        </form>
    </div>
    
    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $statistik['total_guru'] }}</p>
            <p class="text-sm text-gray-500">Total Guru</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $statistik['hadir'] }}</p>
            <p class="text-sm text-gray-500">Hadir</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $statistik['terlambat'] }}</p>
            <p class="text-sm text-gray-500">Terlambat</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $statistik['izin'] }}</p>
            <p class="text-sm text-gray-500">Izin</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $statistik['belum_absen'] }}</p>
            <p class="text-sm text-gray-500">Belum Absen</p>
        </div>
    </div>
    
    <!-- Tabel -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Jam Masuk</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Jam Pulang</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($absensi as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-primary-600 font-semibold text-sm">{{ $a->user->inisial }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-800">{{ $a->user->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $a->user->nip }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-800">{{ $a->jam_masuk ?? '-' }}</span>
                            @if($a->terlambat_menit > 0)
                            <p class="text-xs text-yellow-600">+{{ $a->terlambat_menit }} menit</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-800">{{ $a->jam_pulang ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $colors = [
                                    'hadir' => 'bg-green-100 text-green-700',
                                    'terlambat' => 'bg-yellow-100 text-yellow-700',
                                    'alpha' => 'bg-red-100 text-red-700',
                                    'izin_sakit' => 'bg-blue-100 text-blue-700',
                                    'izin_dinas' => 'bg-purple-100 text-purple-700',
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $colors[$a->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $a->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.absensi.show', $a->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($absensi->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $absensi->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection