@extends('layouts.app')

@section('content')
@php
    $totalBooks = \App\Models\Book::count();
    $totalBorrowed = \App\Models\Loan::whereIn('status', ['dipinjam', 'return_requested'])->count();
    $outOfStock = \App\Models\Book::where('stock', 0)->count();
    $activeTransactions = \App\Models\Loan::with(['user', 'book'])->latest()->take(5)->get();
@endphp

<div class="space-y-6">
    <!-- Stat Counters Row -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stat 1: Total Buku -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Buku</span>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 tracking-tight">{{ number_format($totalBooks) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 border border-sky-100">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>

        <!-- Stat 2: Sedang Dipinjam -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Sedang Dipinjam</span>
                <p class="text-2xl font-bold text-amber-600 mt-0.5 tracking-tight">{{ number_format($totalBorrowed) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Stat 3: Stok Habis -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Stok Habis</span>
                <p class="text-2xl font-bold text-rose-600 mt-0.5 tracking-tight">{{ number_format($outOfStock) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </section>

    <!-- Main Layout: 2-Column Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Primary Area: Book Management (8 Cols) -->
        <section class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-0.5">
                    <h2 class="text-base font-bold text-slate-900">Kelola Koleksi Buku</h2>
                    <p class="text-xs text-slate-500">Daftar seluruh judul dan stok perpustakaan.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Pencarian Buku --}}
                    <form method="GET" action="{{ route('admin.books.index') }}" class="flex items-center gap-1.5">
                        <div class="relative">
                            <input
                                type="text"
                                name="q"
                                value="{{ $query ?? '' }}"
                                placeholder="Cari judul / penulis..."
                                class="w-40 sm:w-52 h-8 px-2.5 pl-8 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                            />
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="submit" class="h-8 px-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition shadow-xs">
                            Cari
                        </button>
                        @if (!empty($query))
                            <a href="{{ route('admin.books.index') }}" class="h-8 px-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold flex items-center transition" title="Reset Pencarian">
                                Reset
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('admin.books.create') }}" class="h-8 inline-flex items-center justify-center gap-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold px-3 rounded-lg transition shadow-xs active:scale-[0.98]">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Tambah Buku</span>
                    </a>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold tracking-wider">
                            <th class="py-3 px-4 w-16 whitespace-nowrap">ID</th>
                            <th class="py-3 px-4">Judul &amp; Penulis</th>
                            <th class="py-3 px-4 whitespace-nowrap">Kategori</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Stok</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($books as $index => $book)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-400 font-medium whitespace-nowrap">
                                    BK-{{ str_pad($book->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-12 rounded bg-slate-100 border border-slate-200 flex-shrink-0 overflow-hidden flex items-center justify-center text-sky-500">
                                            @if ($book->cover_url)
                                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover"/>
                                            @else
                                                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs leading-snug">{{ $book->title }}</div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $book->author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-xs font-medium text-slate-700">
                                    {{ $book->category->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap font-bold text-sm">
                                    @if ($book->stock > 2)
                                        <span class="text-emerald-600">{{ $book->stock }}</span>
                                    @elseif ($book->stock >= 1)
                                        <span class="text-amber-500">{{ $book->stock }}</span>
                                    @else
                                        <span class="text-rose-600">0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2.5 text-xs">
                                        <a href="{{ route('admin.books.edit', $book) }}" class="font-semibold text-sky-600 hover:text-sky-800 transition">
                                            Edit
                                        </a>
                                        <span class="text-slate-300">|</span>
                                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-600 hover:text-rose-800 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 px-4 text-center text-slate-400 text-xs">
                                    @if (!empty($query))
                                        Tidak ada buku yang cocok dengan kata kunci "{{ $query }}".
                                    @else
                                        Belum ada data buku. Klik tombol "Tambah Buku" di atas.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($books->hasPages())
                <div class="p-3 bg-slate-50 border-t border-slate-200">
                    {{ $books->links() }}
                </div>
            @endif
        </section>

        <!-- Secondary Area: Live Monitoring / Riwayat Peminjaman (4 Cols) -->
        <section class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col">
            <div class="p-4 border-b border-slate-200/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Peminjaman</h2>
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                </div>
                <span class="text-xs text-slate-500">Terbaru</span>
            </div>

            <div class="p-4 space-y-3">
                @forelse ($activeTransactions as $loan)
                    <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:border-sky-300 transition flex flex-col gap-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="font-mono text-xs text-sky-600 font-semibold">#LOAN-{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-xs text-slate-500 font-medium">({{ $loan->user->name ?? 'User' }})</span>
                            </div>
                            <span class="text-xs font-semibold {{ $loan->status === 'dipinjam' ? 'text-sky-600' : 'text-emerald-600' }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $loan->book->title ?? 'Buku' }}</p>
                        <div class="text-[11px] text-slate-500 flex items-center justify-between pt-1 border-t border-slate-200/60">
                            <span>Pinjam: {{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</span>
                            <span>Tenggat: {{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-slate-400">
                        Tidak ada riwayat peminjaman tercatat.
                    </div>
                @endforelse
            </div>

            <div class="p-3 mt-auto bg-slate-50 border-t border-slate-200 text-center">
                <a href="{{ route('admin.loans.index') }}" class="text-xs font-semibold text-sky-600 hover:underline inline-flex items-center gap-1">
                    <span>Buka Seluruh Riwayat Peminjaman</span>
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </section>

    </div>
</div>
@endsection
