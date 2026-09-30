@extends('layouts.app')

@section('content')
<div
    x-data="{
        // Rejection modal
        rejectModalOpen: false,
        rejectActionUrl: '',
        rejectBookTitle: '',
        rejectUserName: '',
        rejectReason: 'Stok buku fisik sedang tidak tersedia atau dalam perbaikan.',
        openRejectModal(url, bookTitle, userName) {
            this.rejectActionUrl = url;
            this.rejectBookTitle = bookTitle;
            this.rejectUserName = userName;
            this.rejectReason = 'Stok buku fisik sedang tidak tersedia atau dalam perbaikan.';
            this.rejectModalOpen = true;
        },

        // Detail modal
        detailModalOpen: false,
        selectedLoan: null,
        openDetailModal(loan) {
            this.selectedLoan = loan;
            this.detailModalOpen = true;
        }
    }"
    class="space-y-8"
>
    {{-- Banner Header --}}
    <div class="rounded-2xl bg-sky-600 p-6 sm:p-7 text-white shadow-sm border border-sky-500">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <span class="w-12 h-12 rounded-xl bg-sky-700/60 text-white flex items-center justify-center flex-shrink-0 border border-sky-400/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                        Manajemen Peminjaman
                    </h1>
                    <p class="text-xs sm:text-sm text-sky-100 mt-0.5">Kelola permintaan peminjaman dan pengembalian buku perpustakaan.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                @if ($pendingLoans->count() > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-sky-700/60 text-white border border-sky-400/40 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse"></span>
                        {{ $pendingLoans->count() }} perlu persetujuan
                    </span>
                @endif
                @if ($returnRequested->count() > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-sky-700/60 text-white border border-sky-400/40 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-cyan-200 animate-pulse"></span>
                        {{ $returnRequested->count() }} perlu konfirmasi
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Permintaan Persetujuan --}}
    @if ($pendingLoans->isNotEmpty())
        <section class="space-y-4">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <h2 class="text-base font-bold font-heading text-slate-900">Permintaan Peminjaman — Menunggu Persetujuan</h2>
                <span class="ml-auto text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full">{{ $pendingLoans->count() }} permintaan</span>
            </div>

            <div class="bg-white rounded-2xl shadow-xs border border-amber-200/70 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-amber-50 border-b border-amber-200 uppercase font-semibold text-amber-700 tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Peminjam</th>
                                <th class="px-5 py-3.5">Buku</th>
                                <th class="px-5 py-3.5">Tenggat Diminta</th>
                                <th class="px-5 py-3.5">Waktu Request</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pendingLoans as $loan)
                                <tr class="hover:bg-amber-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900">{{ $loan->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $loan->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($loan->book?->cover_url)
                                                <img src="{{ $loan->book->cover_url }}" alt="" class="w-8 h-10 rounded object-cover flex-shrink-0 border border-slate-200"/>
                                            @endif
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $loan->book->title ?? 'Buku Terhapus' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $loan->book->author ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-mono">{{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}</td>
                                    <td class="px-5 py-4 text-slate-500">{{ $loan->created_at->diffForHumans() }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" onsubmit="return confirm('Setujui peminjaman ini?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition shadow-xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Setujui
                                                </button>
                                            </form>
                                            <button
                                                type="button"
                                                @click="openRejectModal('{{ route('admin.loans.reject', $loan) }}', '{{ addslashes($loan->book->title ?? 'Buku') }}', '{{ addslashes($loan->user->name ?? 'Peminjam') }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold transition"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    {{-- Permintaan Pengembalian --}}
    @if ($returnRequested->isNotEmpty())
        <section class="space-y-4">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500 animate-pulse"></span>
                <h2 class="text-base font-bold font-heading text-slate-900">Permintaan Pengembalian — Menunggu Konfirmasi</h2>
                <span class="ml-auto text-xs font-semibold bg-sky-100 text-sky-700 border border-sky-200 px-2.5 py-0.5 rounded-full">{{ $returnRequested->count() }} permintaan</span>
            </div>

            <div class="bg-white rounded-2xl shadow-xs border border-sky-200/70 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-sky-50 border-b border-sky-200 uppercase font-semibold text-sky-700 tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Peminjam</th>
                                <th class="px-5 py-3.5">Buku</th>
                                <th class="px-5 py-3.5">Tanggal Pinjam</th>
                                <th class="px-5 py-3.5">Tenggat</th>
                                <th class="px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($returnRequested as $loan)
                                <tr class="hover:bg-sky-50/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900">{{ $loan->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $loan->user->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($loan->book?->cover_url)
                                                <img src="{{ $loan->book->cover_url }}" alt="" class="w-8 h-10 rounded object-cover flex-shrink-0 border border-slate-200"/>
                                            @endif
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $loan->book->title ?? 'Buku Terhapus' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $loan->book->author ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-mono">{{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}</td>
                                    <td class="px-5 py-4 font-mono">{{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <form action="{{ route('admin.loans.confirm-return', $loan) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku ini?')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold transition shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Konfirmasi Kembali
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    {{-- Riwayat Semua Peminjaman --}}
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base font-bold font-heading text-slate-900">Riwayat Semua Peminjaman</h2>
                <span class="text-xs text-slate-400">({{ $allLoans->total() }} transaksi)</span>
            </div>

            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('admin.loans.index') }}" class="flex items-center gap-2">
                @if ($userId)
                    <input type="hidden" name="user_id" value="{{ $userId }}"/>
                @endif
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query ?? '' }}"
                        placeholder="Cari peminjam, email, buku..."
                        class="w-52 sm:w-64 h-8 px-2.5 pl-8 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                    />
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="h-8 px-3 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition shadow-xs">
                    Cari
                </button>
                @if (!empty($query) || $userId)
                    <a href="{{ route('admin.loans.index') }}" class="h-8 px-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold flex items-center transition" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Filter Anggota --}}
        @if ($filteredUser)
            <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl flex items-center justify-between text-xs text-sky-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>Menampilkan khusus riwayat peminjaman untuk anggota: <strong>{{ $filteredUser->name }}</strong> ({{ $filteredUser->email }})</span>
                </div>
                <a href="{{ route('admin.loans.index') }}" class="font-semibold text-sky-700 hover:underline text-[11px]">
                    Tampilkan Semua Peminjam &times;
                </a>
            </div>
        @endif

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/90 overflow-hidden">
            {{-- Tampilan Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500 tracking-wider text-[11px]">
                        <tr>
                            <th class="px-5 py-3.5">ID</th>
                            <th class="px-5 py-3.5">Peminjam</th>
                            <th class="px-5 py-3.5">Judul Buku</th>
                            <th class="px-5 py-3.5">Pinjam</th>
                            <th class="px-5 py-3.5">Tenggat</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allLoans as $loan)
                            @php
                                $loanDate   = $loan->loan_date;
                                $dueDate    = $loan->due_date;
                                $returnDate = $loan->return_date;
                                $today      = now()->startOfDay();

                                $totalDays   = ($loanDate && $dueDate) ? (int) $loanDate->diffInDays($dueDate) : 14;
                                $totalDays   = max($totalDays, 1);

                                if ($loan->status === 'dikembalikan' && $returnDate) {
                                    $elapsedDays  = (int) $loanDate->diffInDays($returnDate);
                                    $progress     = 100;
                                    $barClass     = 'bg-emerald-500';
                                    $ringStroke   = '#10b981';
                                    $ringBg       = '#d1fae5';
                                    $textColor    = 'text-emerald-700';
                                    $daysLeft     = 0;
                                } elseif ($loan->status === 'ditolak') {
                                    $elapsedDays  = 0;
                                    $progress     = 0;
                                    $barClass     = 'bg-rose-500';
                                    $ringStroke   = '#ef4444';
                                    $ringBg       = '#fee2e2';
                                    $textColor    = 'text-rose-700';
                                    $daysLeft     = 0;
                                } elseif ($loan->status === 'pending' || !$loanDate) {
                                    $elapsedDays  = 0;
                                    $progress     = 0;
                                    $barClass     = 'bg-slate-300';
                                    $ringStroke   = '#94a3b8';
                                    $ringBg       = '#f1f5f9';
                                    $textColor    = 'text-slate-500';
                                    $daysLeft     = $totalDays;
                                } else {
                                    $elapsedDays  = (int) min($loanDate->diffInDays($today), $totalDays);
                                    $progress     = min((int) round($elapsedDays / $totalDays * 100), 100);
                                    $daysLeft     = $totalDays - $elapsedDays;
                                    $pctLeft      = $daysLeft / $totalDays;

                                    if ($loan->status === 'return_requested') {
                                        $barClass   = 'bg-purple-500';
                                        $ringStroke = '#a855f7';
                                        $ringBg     = '#f3e8ff';
                                        $textColor  = 'text-purple-700';
                                    } elseif ($pctLeft > 0.5) {
                                        $barClass   = 'bg-emerald-500';
                                        $ringStroke = '#10b981';
                                        $ringBg     = '#d1fae5';
                                        $textColor  = 'text-emerald-700';
                                    } elseif ($pctLeft > 0.25) {
                                        $barClass   = 'bg-amber-400';
                                        $ringStroke = '#f59e0b';
                                        $ringBg     = '#fef3c7';
                                        $textColor  = 'text-amber-700';
                                    } else {
                                        $barClass   = 'bg-rose-500';
                                        $ringStroke = '#ef4444';
                                        $ringBg     = '#fee2e2';
                                        $textColor  = 'text-rose-700';
                                    }
                                }

                                $loanData = [
                                    'id' => $loan->id,
                                    'code' => '#LOAN-' . str_pad($loan->id, 5, '0', STR_PAD_LEFT),
                                    'status' => $loan->status,
                                    'rejection_note' => $loan->rejection_note,
                                    'user' => [
                                        'name' => $loan->user->name ?? '-',
                                        'npm' => $loan->user->npm ?? null,
                                        'email' => $loan->user->email ?? '-',
                                    ],
                                    'book' => [
                                        'title' => $loan->book->title ?? 'Buku Terhapus',
                                        'author' => $loan->book->author ?? '-',
                                        'year' => $loan->book?->published_year,
                                        'category' => $loan->book?->category?->name,
                                        'cover_url' => $loan->book?->cover_url,
                                    ],
                                    'loan_date' => $loanDate ? $loanDate->format('d M Y') : '-',
                                    'due_date' => $dueDate ? $dueDate->format('d M Y') : '-',
                                    'return_date' => $returnDate ? $returnDate->format('d M Y') : null,
                                    'progress' => $progress,
                                    'elapsed_days' => $elapsedDays,
                                    'total_days' => $totalDays,
                                    'days_left' => $daysLeft,
                                    'bar_class' => $barClass,
                                    'ring_stroke' => $ringStroke,
                                    'ring_bg' => $ringBg,
                                    'text_color' => $textColor,
                                    'approve_url' => route('admin.loans.approve', $loan),
                                    'reject_url' => route('admin.loans.reject', $loan),
                                    'confirm_return_url' => route('admin.loans.confirm-return', $loan),
                                ];
                            @endphp

                            <tr
                                @click="openDetailModal({{ json_encode($loanData) }})"
                                class="hover:bg-slate-50/80 cursor-pointer transition group"
                            >
                                <td class="px-5 py-3.5 font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap">
                                    {{ $loanData['code'] }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $loan->user->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">
                                        @if ($loan->user?->npm)
                                            <span class="text-sky-700 font-semibold">{{ $loan->user->npm }}</span> &middot;
                                        @endif
                                        <span>{{ $loan->user->email ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs">
                                    <div class="font-bold text-slate-800 truncate group-hover:text-sky-600 transition" title="{{ $loan->book->title ?? 'Buku Terhapus' }}">
                                        {{ $loan->book->title ?? 'Buku Terhapus' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 truncate">
                                        {{ $loan->book->author ?? '-' }}
                                        @if ($loan->book?->published_year)
                                            · {{ $loan->book->published_year }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $loanData['loan_date'] }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $loanData['due_date'] }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($loan->status === 'pending')
                                        <span class="text-xs font-semibold text-amber-600">Menunggu</span>
                                    @elseif ($loan->status === 'dipinjam')
                                        <span class="text-xs font-semibold text-sky-600">Dipinjam</span>
                                    @elseif ($loan->status === 'return_requested')
                                        <span class="text-xs font-semibold text-purple-600">Konfirmasi</span>
                                    @elseif ($loan->status === 'ditolak')
                                        <span class="text-xs font-semibold text-rose-600">Ditolak</span>
                                    @else
                                        <span class="text-xs font-semibold text-emerald-600">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap" @click.stop>
                                    <button
                                        type="button"
                                        @click="openDetailModal({{ json_encode($loanData) }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-xs font-semibold transition"
                                        title="Lihat Detail Transaksi"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Belum ada data riwayat transaksi peminjaman buku.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Mobile --}}
            <div class="md:hidden divide-y divide-slate-100">
                @forelse ($allLoans as $loan)
                    @php
                        $loanDate   = $loan->loan_date;
                        $dueDate    = $loan->due_date;
                        $returnDate = $loan->return_date;
                        $today      = now()->startOfDay();

                        $totalDays   = ($loanDate && $dueDate) ? (int) $loanDate->diffInDays($dueDate) : 14;
                        $totalDays   = max($totalDays, 1);

                        if ($loan->status === 'dikembalikan' && $returnDate) {
                            $elapsedDays  = (int) $loanDate->diffInDays($returnDate);
                            $progress     = 100;
                            $barClass     = 'bg-emerald-500';
                            $ringStroke   = '#10b981';
                            $ringBg       = '#d1fae5';
                            $textColor    = 'text-emerald-700';
                            $daysLeft     = 0;
                        } elseif ($loan->status === 'ditolak') {
                            $elapsedDays  = 0;
                            $progress     = 0;
                            $barClass     = 'bg-rose-500';
                            $ringStroke   = '#ef4444';
                            $ringBg       = '#fee2e2';
                            $textColor    = 'text-rose-700';
                            $daysLeft     = 0;
                        } elseif ($loan->status === 'pending' || !$loanDate) {
                            $elapsedDays  = 0;
                            $progress     = 0;
                            $barClass     = 'bg-slate-300';
                            $ringStroke   = '#94a3b8';
                            $ringBg       = '#f1f5f9';
                            $textColor    = 'text-slate-500';
                            $daysLeft     = $totalDays;
                        } else {
                            $elapsedDays  = (int) min($loanDate->diffInDays($today), $totalDays);
                            $progress     = min((int) round($elapsedDays / $totalDays * 100), 100);
                            $daysLeft     = $totalDays - $elapsedDays;
                            $pctLeft      = $daysLeft / $totalDays;

                            if ($loan->status === 'return_requested') {
                                $barClass   = 'bg-purple-500';
                                $ringStroke = '#a855f7';
                                $ringBg     = '#f3e8ff';
                                $textColor  = 'text-purple-700';
                            } elseif ($pctLeft > 0.5) {
                                $barClass   = 'bg-emerald-500';
                                $ringStroke = '#10b981';
                                $ringBg     = '#d1fae5';
                                $textColor  = 'text-emerald-700';
                            } elseif ($pctLeft > 0.25) {
                                $barClass   = 'bg-amber-400';
                                $ringStroke = '#f59e0b';
                                $ringBg     = '#fef3c7';
                                $textColor  = 'text-amber-700';
                            } else {
                                $barClass   = 'bg-rose-500';
                                $ringStroke = '#ef4444';
                                $ringBg     = '#fee2e2';
                                $textColor  = 'text-rose-700';
                            }
                        }

                        $mLoanData = [
                            'id' => $loan->id,
                            'code' => '#LOAN-' . str_pad($loan->id, 5, '0', STR_PAD_LEFT),
                            'status' => $loan->status,
                            'rejection_note' => $loan->rejection_note,
                            'user' => [
                                'name' => $loan->user->name ?? '-',
                                'npm' => $loan->user->npm ?? null,
                                'email' => $loan->user->email ?? '-',
                            ],
                            'book' => [
                                'title' => $loan->book->title ?? 'Buku Terhapus',
                                'author' => $loan->book->author ?? '-',
                                'year' => $loan->book?->published_year,
                                'category' => $loan->book?->category?->name,
                                'cover_url' => $loan->book?->cover_url,
                            ],
                            'loan_date' => $loanDate ? $loanDate->format('d M Y') : '-',
                            'due_date' => $dueDate ? $dueDate->format('d M Y') : '-',
                            'return_date' => $returnDate ? $returnDate->format('d M Y') : null,
                            'progress' => $progress,
                            'elapsed_days' => $elapsedDays,
                            'total_days' => $totalDays,
                            'days_left' => $daysLeft,
                            'bar_class' => $barClass,
                            'ring_stroke' => $ringStroke,
                            'ring_bg' => $ringBg,
                            'text_color' => $textColor,
                            'approve_url' => route('admin.loans.approve', $loan),
                            'reject_url' => route('admin.loans.reject', $loan),
                            'confirm_return_url' => route('admin.loans.confirm-return', $loan),
                        ];
                    @endphp

                    <div
                        @click="openDetailModal({{ json_encode($mLoanData) }})"
                        class="p-4 space-y-2 hover:bg-slate-50 cursor-pointer transition"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[10px] text-slate-400 font-semibold">{{ $mLoanData['code'] }}</span>
                            @if ($loan->status === 'pending')
                                <span class="text-xs font-semibold text-amber-600">Menunggu</span>
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
                            <p class="text-[11px] text-slate-500">Peminjam: <strong class="text-slate-700">{{ $loan->user->name ?? '-' }}</strong> @if($loan->user?->npm) <span class="text-sky-700 font-mono font-semibold">({{ $loan->user->npm }})</span> @endif</p>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-100 font-mono">
                            <span>Pinjam: {{ $mLoanData['loan_date'] }}</span>
                            <span>Tenggat: {{ $mLoanData['due_date'] }}</span>
                        </div>
                        <button
                            type="button"
                            @click.stop="openDetailModal({{ json_encode($mLoanData) }})"
                            class="w-full mt-1 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-xs font-semibold text-center transition"
                        >
                            Lihat Rincian Lengkap
                        </button>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat transaksi peminjaman buku.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Paginasi --}}
        @if ($allLoans->hasPages())
            <div class="pt-2">
                {{ $allLoans->links() }}
            </div>
        @endif
    </section>

    {{-- Modal Penolakan --}}
    <div
        x-show="rejectModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.away="rejectModalOpen = false"
            class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        >
            <div class="px-6 py-4 bg-rose-50/60 border-b border-rose-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tolak Permintaan Peminjaman</h3>
                        <p class="text-[11px] text-slate-500">Berikan bukti alasan penolakan yang jelas kepada peminjam.</p>
                    </div>
                </div>
                <button type="button" @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600 transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="rejectActionUrl" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
                    <div class="text-slate-500">Peminjam: <strong class="text-slate-800" x-text="rejectUserName"></strong></div>
                    <div class="text-slate-500">Buku: <strong class="text-slate-800" x-text="rejectBookTitle"></strong></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilihan Cepat Alasan Penolakan:</label>
                    <div class="grid grid-cols-1 gap-1.5">
                        <button
                            type="button"
                            @click="rejectReason = 'Stok buku fisik sedang tidak tersedia atau dalam perbaikan.'"
                            class="text-left text-xs px-3 py-2 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50/50 transition text-slate-700"
                        >
                            • Stok buku fisik sedang tidak tersedia atau dalam perbaikan.
                        </button>
                        <button
                            type="button"
                            @click="rejectReason = 'Batas maksimal kuota peminjaman aktif Anda telah tercapai.'"
                            class="text-left text-xs px-3 py-2 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50/50 transition text-slate-700"
                        >
                            • Batas maksimal kuota peminjaman aktif Anda telah tercapai.
                        </button>
                        <button
                            type="button"
                            @click="rejectReason = 'Ada tanggungan peminjaman buku sebelumnya yang belum diselesaikan.'"
                            class="text-left text-xs px-3 py-2 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50/50 transition text-slate-700"
                        >
                            • Ada tanggungan peminjaman buku sebelumnya yang belum diselesaikan.
                        </button>
                    </div>
                </div>

                <div>
                    <label for="reject_reason_input" class="block text-xs font-bold text-slate-700 mb-1">
                        Pesan Alasan Penolakan (Akan masuk ke notifikasi peminjam):
                    </label>
                    <textarea
                        id="reject_reason_input"
                        name="reason"
                        x-model="rejectReason"
                        rows="3"
                        required
                        class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500/20 shadow-xs placeholder-slate-400"
                        placeholder="Tuliskan alasan penolakan secara jelas..."
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button
                        type="button"
                        @click="rejectModalOpen = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Detail Peminjaman --}}
    <div
        x-show="detailModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <template x-if="selectedLoan">
            <div
                @click.away="detailModalOpen = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden my-8"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-mono font-semibold text-slate-400" x-text="selectedLoan.code"></span>
                        <h3 class="text-sm font-bold text-slate-900 leading-snug">Rincian Transaksi Peminjaman</h3>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    <!-- Status Header Banner -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-xs font-bold"
                            :class="{
                                'text-amber-600': selectedLoan.status === 'pending',
                                'text-sky-600': selectedLoan.status === 'dipinjam',
                                'text-purple-600': selectedLoan.status === 'return_requested',
                                'text-emerald-600': selectedLoan.status === 'dikembalikan',
                                'text-rose-600': selectedLoan.status === 'ditolak',
                            }"
                            x-text="
                                selectedLoan.status === 'pending' ? 'Status: Menunggu Persetujuan' :
                                selectedLoan.status === 'dipinjam' ? 'Status: Sedang Dipinjam' :
                                selectedLoan.status === 'return_requested' ? 'Status: Menunggu Konfirmasi Pengembalian' :
                                selectedLoan.status === 'ditolak' ? 'Status: Permintaan Ditolak' :
                                'Status: Selesai Dikembalikan'
                            "
                        ></span>
                        <span class="text-[11px] font-mono text-slate-400 font-semibold" x-text="selectedLoan.code"></span>
                    </div>

                    <!-- Book & Borrower Card -->
                    <div class="flex gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <template x-if="selectedLoan.book.cover_url">
                            <div class="w-16 h-24 rounded-lg overflow-hidden bg-slate-200 flex-shrink-0 border border-slate-200">
                                <img :src="selectedLoan.book.cover_url" alt="" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <template x-if="!selectedLoan.book.cover_url">
                            <div class="w-16 h-24 rounded-lg bg-slate-200 flex-shrink-0 border border-slate-300 flex items-center justify-center text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                        </template>

                        <div class="min-w-0 flex-1 space-y-1">
                            <h4 class="text-sm font-bold text-slate-900 leading-snug" x-text="selectedLoan.book.title"></h4>
                            <p class="text-xs text-slate-500">Penulis: <span class="font-medium text-slate-700" x-text="selectedLoan.book.author"></span></p>
                            <template x-if="selectedLoan.book.category">
                                <p class="text-xs text-slate-500">Kategori: <span class="font-medium text-slate-700" x-text="selectedLoan.book.category"></span></p>
                            </template>
                            <div class="pt-2 mt-2 border-t border-slate-200 text-xs text-slate-600">
                                <p>Peminjam: <strong class="text-slate-900" x-text="selectedLoan.user.name"></strong></p>
                                <p class="text-[11px] font-mono text-slate-500">
                                    <template x-if="selectedLoan.user.npm">
                                        <span class="text-sky-700 font-semibold" x-text="'NPM: ' + selectedLoan.user.npm + ' · '"></span>
                                    </template>
                                    <span x-text="selectedLoan.user.email"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Visualization (Only if not pending and not ditolak) -->
                    <template x-if="selectedLoan.status !== 'pending' && selectedLoan.status !== 'ditolak'">
                        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs space-y-3">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-bold text-slate-800">Visualisasi Durasi Peminjaman</h5>
                                <span class="text-xs font-bold" :class="selectedLoan.text_color" x-text="selectedLoan.progress + '%'"></span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Mulai Pinjam</span>
                                    <span class="font-semibold text-slate-700" x-text="selectedLoan.loan_date"></span>
                                </div>
                                <template x-if="selectedLoan.return_date">
                                    <div class="text-right">
                                        <span class="text-[10px] text-slate-400 block">Dikembalikan</span>
                                        <span class="font-semibold text-emerald-600" x-text="selectedLoan.return_date"></span>
                                    </div>
                                </template>
                                <template x-if="!selectedLoan.return_date">
                                    <div class="text-right">
                                        <span class="text-[10px] text-slate-400 block">Batas Tenggat</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedLoan.due_date"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Linear Progress Bar -->
                            <div class="relative w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-700"
                                    :class="selectedLoan.bar_class"
                                    :style="'width: ' + selectedLoan.progress + '%'"
                                ></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span x-text="selectedLoan.elapsed_days + ' dari ' + selectedLoan.total_days + ' hari'"></span>
                                <template x-if="selectedLoan.status === 'dipinjam'">
                                    <span class="font-semibold" :class="selectedLoan.text_color" x-text="selectedLoan.days_left > 0 ? selectedLoan.days_left + ' hari lagi' : 'Melewati batas tenggat!'"></span>
                                </template>
                                <template x-if="selectedLoan.status === 'dikembalikan'">
                                    <span class="text-emerald-600 font-semibold">Telah selesai dikembalikan</span>
                                </template>
                                <template x-if="selectedLoan.status === 'return_requested'">
                                    <span class="text-purple-600 font-semibold">Menunggu konfirmasi admin</span>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Rejection Note Box (If Ditolak) -->
                    <template x-if="selectedLoan.status === 'ditolak'">
                        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl space-y-1 text-xs text-rose-800">
                            <div class="font-bold flex items-center gap-1.5 text-rose-700">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Bukti Alasan Penolakan:
                            </div>
                            <p class="leading-relaxed" x-text="selectedLoan.rejection_note || 'Stok buku fisik sedang tidak tersedia atau kuota peminjaman tercapai.'"></p>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <!-- Quick actions inside modal if still pending or return_requested -->
                        <template x-if="selectedLoan.status === 'pending'">
                            <div class="flex items-center gap-2">
                                <form :action="selectedLoan.approve_url" method="POST" onsubmit="return confirm('Setujui peminjaman ini?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-xs">
                                        Setujui Pinjaman
                                    </button>
                                </form>
                                <button
                                    type="button"
                                    @click="detailModalOpen = false; openRejectModal(selectedLoan.reject_url, selectedLoan.book.title, selectedLoan.user.name)"
                                    class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold transition"
                                >
                                    Tolak
                                </button>
                            </div>
                        </template>

                        <template x-if="selectedLoan.status === 'return_requested'">
                            <form :action="selectedLoan.confirm_return_url" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku ini?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition shadow-xs">
                                    Konfirmasi Pengembalian
                                </button>
                            </form>
                        </template>
                    </div>

                    <button
                        type="button"
                        @click="detailModalOpen = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-white transition ml-auto"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
