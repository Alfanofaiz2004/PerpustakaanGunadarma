@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading tracking-tight">Riwayat Peminjaman Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar seluruh transaksi peminjaman dan pengembalian buku Anda.</p>
        </div>
        <a href="{{ route('peminjam.books.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition self-start sm:self-auto shadow-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Pinjam Buku Lain</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        {{-- Tampilan Desktop --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">ID Transaksi</th>
                        <th class="px-5 py-3.5">Judul Buku</th>
                        <th class="px-5 py-3.5">Tanggal Pinjam</th>
                        <th class="px-5 py-3.5">Tenggat</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($loans as $loan)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 font-mono text-[11px] text-slate-400 font-semibold">
                                #LOAN-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 leading-snug">{{ $loan->book->title ?? 'Buku Terhapus' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $loan->book->author ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-600">
                                {{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-600">
                                {{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($loan->status === 'pending')
                                    <span class="text-xs font-semibold text-amber-600">Menunggu Persetujuan</span>
                                @elseif ($loan->status === 'dipinjam')
                                    <span class="text-xs font-semibold text-sky-600">Sedang Dipinjam</span>
                                @elseif ($loan->status === 'return_requested')
                                    <span class="text-xs font-semibold text-purple-600">Menunggu Konfirmasi</span>
                                @elseif ($loan->status === 'ditolak')
                                    <div class="space-y-1">
                                        <span class="text-xs font-semibold text-rose-600">Permintaan Ditolak</span>
                                        @if ($loan->rejection_note)
                                            <p class="text-[11px] text-rose-600 bg-rose-50/80 border border-rose-100 rounded-md px-2 py-1 max-w-xs leading-tight">
                                                <strong class="font-medium">Catatan:</strong> {{ $loan->rejection_note }}
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs font-semibold text-emerald-600">
                                        Selesai Dikembalikan {{ $loan->return_date ? '('.$loan->return_date->format('d M Y').')' : '' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if ($loan->status === 'dipinjam')
                                    <form action="{{ route('peminjam.loans.return', $loan) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian buku ini kepada petugas perpustakaan?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold transition shadow-xs active:scale-[0.98]">
                                            Kembalikan Buku
                                        </button>
                                    </form>
                                @elseif ($loan->status === 'pending')
                                    <span class="text-xs text-amber-600 italic">Menunggu admin...</span>
                                @elseif ($loan->status === 'return_requested')
                                    <span class="text-xs text-purple-600 italic">Diproses petugas...</span>
                                @elseif ($loan->status === 'ditolak')
                                    <a href="{{ route('peminjam.books.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-xs font-semibold transition">
                                        <span>Cari Buku Lain</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs">
                                Anda belum memiliki riwayat transaksi peminjaman buku.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tampilan Mobile --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse ($loans as $loan)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-[10px] text-slate-400 font-semibold">#LOAN-{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</span>
                        @if ($loan->status === 'pending')
                            <span class="text-xs font-semibold text-amber-600">Menunggu Admin</span>
                        @elseif ($loan->status === 'dipinjam')
                            <span class="text-xs font-semibold text-sky-600">Dipinjam</span>
                        @elseif ($loan->status === 'return_requested')
                            <span class="text-xs font-semibold text-purple-600">Konfirmasi</span>
                        @elseif ($loan->status === 'ditolak')
                            <span class="text-xs font-semibold text-rose-600">Ditolak</span>
                        @else
                            <span class="text-xs font-semibold text-emerald-600">Selesai</span>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ $loan->book->title ?? 'Buku Terhapus' }}</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $loan->book->author ?? '-' }}</p>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                        <span>Pinjam: {{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</span>
                        <span>Tenggat: {{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}</span>
                    </div>

                    @if ($loan->status === 'ditolak' && $loan->rejection_note)
                        <div class="p-2.5 bg-rose-50 border border-rose-200/80 rounded-xl text-[11px] text-rose-700 space-y-0.5">
                            <span class="font-bold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Alasan Penolakan:
                            </span>
                            <p class="leading-relaxed">{{ $loan->rejection_note }}</p>
                        </div>
                        <a href="{{ route('peminjam.books.index') }}" class="block text-center py-2 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded-lg text-xs font-semibold transition">
                            Cari Buku Lain
                        </a>
                    @endif

                    @if ($loan->status === 'dipinjam')
                        <form action="{{ route('peminjam.loans.return', $loan) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian buku ini kepada petugas perpustakaan?')">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold transition text-center shadow-xs">
                                Kembalikan Buku
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Anda belum memiliki riwayat transaksi peminjaman buku.
                </div>
            @endforelse
        </div>

        @if ($loans->hasPages())
            <div class="p-3 border-t border-slate-200 bg-slate-50/70">
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
