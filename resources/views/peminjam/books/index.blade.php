@extends('layouts.app')

@section('content')
@php
    $activeLoans = \App\Models\Loan::with('book')
        ->where('user_id', auth()->id())
        ->whereIn('status', ['dipinjam', 'return_requested'])
        ->latest()
        ->get();
    $pendingLoans = \App\Models\Loan::with('book')
        ->where('user_id', auth()->id())
        ->where('status', 'pending')
        ->latest()
        ->get();
    $categories = \App\Models\Category::has('books')->orderBy('name')->get();
@endphp

<div class="space-y-6">
    {{-- Header Page --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Katalog &amp; Peminjaman Buku</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Cari buku favorit dan ajukan peminjaman online mandiri.</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($pendingLoans->count() > 0)
                <a href="{{ route('peminjam.loans.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 transition">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>{{ $pendingLoans->count() }} Menunggu Admin</span>
                </a>
            @endif
            <a href="{{ route('peminjam.loans.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Pinjaman Aktif ({{ $activeLoans->count() }})</span>
            </a>
        </div>
    </div>

    {{-- Konten Utama --}}
    <div class="flex flex-col lg:flex-row items-start gap-6">

        {{-- Panel Filter dan Pencarian --}}
        <aside class="w-full lg:w-72 shrink-0 space-y-4 lg:sticky lg:top-20">
            <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </span>
                        <h2 class="text-sm font-bold text-slate-900">Pencarian &amp; Filter</h2>
                    </div>
                    <span id="activeFilterBadge" class="hidden text-[10px] font-semibold bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full">Aktif</span>
                </div>

                {{-- Input Kata Kunci --}}
                <div class="space-y-1.5">
                    <label for="searchInput" class="block text-xs font-semibold text-slate-700">Cari Kata Kunci</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Judul atau penulis..."
                            autocomplete="off"
                            class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div id="searchSpinner" class="hidden absolute inset-y-0 right-0 pr-2.5 flex items-center">
                            <svg class="animate-spin w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Dropdown Kategori --}}
                <div class="space-y-1.5">
                    <label for="categorySelect" class="block text-xs font-semibold text-slate-700">Kategori Buku</label>
                    <div class="relative">
                        <select
                            id="categorySelect"
                            class="w-full py-2 pl-3 pr-8 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition cursor-pointer appearance-none"
                        >
                            <option value="all">Semua Kategori ({{ $categories->count() }})</option>
                            @foreach ($categories as $cat)
                                <option value="{{ strtolower($cat->name) }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Tombol Reset --}}
                <div class="pt-2">
                    <button
                        type="button"
                        id="btnResetFilter"
                        class="w-full py-2 px-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Reset Pencarian</span>
                    </button>
                </div>

                {{-- Kotak Petunjuk --}}
                <div class="p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-[11px] text-slate-600 space-y-1">
                    <p class="font-bold text-sky-800 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Petunjuk Peminjaman:
                    </p>
                    <p class="leading-relaxed">Klik kartu buku untuk membaca sinopsis dan memilih tanggal pengembalian (maksimal 14 hari).</p>
                </div>
            </div>
        </aside>

        {{-- Katalog Buku --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- Status Peminjaman --}}
            @if ($pendingLoans->isNotEmpty())
                <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Permintaan Pinjam Dalam Proses</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach ($pendingLoans as $loan)
                            <div class="bg-white border border-amber-200 rounded-lg p-2.5 flex items-center justify-between shadow-2xs">
                                <div class="min-w-0 flex-1 pr-2">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $loan->book->title ?? 'Buku' }}</p>
                                    <p class="text-[10px] text-slate-500">Tenggat diminta: {{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}</p>
                                </div>
                                <span class="text-[10px] font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded shrink-0">Menunggu</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Koleksi Buku --}}
            <div class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border border-slate-200 shadow-2xs">
                <div class="flex items-center gap-2">
                    <h2 class="text-xs sm:text-sm font-bold text-slate-900">Koleksi Buku</h2>
                    <span id="bookCountBadge" class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                        {{ $books->total() }} judul
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 hidden sm:inline">Pilih kartu buku untuk detail</span>
            </div>

            {{-- Grid Buku --}}
            <div id="bookGrid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3.5">
                @forelse ($books as $book)
                    <x-book-card :book="$book" />
                @empty
                    <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p class="text-sm font-semibold text-slate-700">Koleksi buku belum tersedia</p>
                        <p class="text-xs text-slate-400 mt-1">Petugas perpustakaan belum menambahkan data buku.</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginasi --}}
            <div id="paginationContainer" class="pt-2">
                {{ $books->links() }}
            </div>
        </div>

    </div>
</div>

{{-- Modal Detail Buku --}}
<div
    id="bookModal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalTitle"
>
    <div id="modalBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity" onclick="closeBookModal()"></div>

    <div
        id="modalPanel"
        class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-200 scale-95 opacity-0 flex flex-col max-h-[90vh]"
    >
        <button
            type="button"
            onclick="closeBookModal()"
            aria-label="Tutup Detail"
            class="absolute top-2.5 right-2.5 z-20 w-8 h-8 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center backdrop-blur-xs transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="w-full aspect-video bg-slate-100 overflow-hidden shrink-0 relative flex items-center justify-center">
            <img id="modalCover" src="" alt="Sampul Buku" class="w-full h-full object-cover hidden"/>
            <div id="modalCoverPlaceholder" class="w-full h-full flex flex-col items-center justify-center text-sky-400 p-4">
                <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-xs text-slate-400">Tidak ada foto sampul</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 overflow-y-auto space-y-3.5 text-xs">
            <div>
                <span id="modalCategory" class="inline-block text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-100 px-2 py-0.5 rounded mb-1.5"></span>
                <h2 id="modalTitle" class="text-base sm:text-lg font-bold font-heading text-slate-900 leading-snug"></h2>
                <p id="modalAuthor" class="text-xs text-slate-500 mt-0.5"></p>
                <p id="modalYear" class="text-[11px] text-slate-400 mt-0.5"></p>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sinopsis &amp; Deskripsi</h3>
                <p id="modalDescription" class="text-xs text-slate-600 leading-relaxed max-h-24 overflow-y-auto"></p>
            </div>

            <div id="modalStockBadge"></div>

            <div id="datePickerSection" class="pt-2 border-t border-slate-100 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Pilih Tanggal Pengembalian</h3>
                        <p class="text-[11px] text-slate-400">Maksimal peminjaman 14 hari</p>
                    </div>
                    <span id="selectedDateLabel" class="text-[11px] font-bold text-sky-600">Pilih tanggal</span>
                </div>

                <div class="bg-slate-50 rounded-xl border border-slate-200 p-2.5">
                    <div class="flex items-center justify-between mb-2">
                        <button type="button" id="calPrev" aria-label="Bulan Sebelumnya" class="w-6 h-6 rounded-md hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span id="calMonthYear" class="text-xs font-bold text-slate-800"></span>
                        <button type="button" id="calNext" aria-label="Bulan Berikutnya" class="w-6 h-6 rounded-md hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 mb-1 text-center">
                        @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                            <div class="text-[9px] font-bold text-slate-400 uppercase py-0.5">{{ $day }}</div>
                        @endforeach
                    </div>
                    <div id="calDays" class="grid grid-cols-7 gap-0.5"></div>
                </div>
            </div>
        </div>

        <div class="p-3 sm:p-4 border-t border-slate-200 bg-white shrink-0">
            <button
                id="btnPinjam"
                type="button"
                onclick="submitLoan()"
                disabled
                class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-semibold transition text-xs flex items-center justify-center gap-1.5 shadow-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Pinjam Buku Ini</span>
            </button>
        </div>
    </div>
</div>

{{-- Modal Berhasil --}}
<div
    id="successPopup"
    class="fixed inset-0 z-60 hidden items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
>
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs" onclick="closeSuccessPopup()"></div>
    <div
        id="successPanel"
        class="relative z-10 bg-white rounded-2xl shadow-2xl p-6 sm:p-7 max-w-xs sm:max-w-sm w-full text-center transform transition-all duration-200 scale-95 opacity-0"
    >
        <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3 text-emerald-600">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 mb-1">Permintaan Terkirim!</h3>
        <p class="text-xs text-slate-500 mb-5">Petugas perpustakaan akan meninjau dan menyetujui peminjaman Anda. Status dapat dipantau di menu Riwayat.</p>
        <button
            type="button"
            onclick="closeSuccessPopup()"
            class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold transition text-xs shadow-xs"
        >
            Oke, Mengerti
        </button>
    </div>
</div>

<script>
// In-Memory Book Data Store (eliminates JSON in HTML escaping issues)
window.booksStore = new Map();

@foreach ($books as $b)
window.booksStore.set({{ $b->id }}, {
    id: {{ $b->id }},
    title: {!! json_encode($b->title) !!},
    author: {!! json_encode($b->author) !!},
    published_year: {!! json_encode($b->published_year) !!},
    description: {!! json_encode($b->description ?? 'Tidak ada deskripsi untuk buku ini.') !!},
    cover_url: {!! json_encode($b->cover_url) !!},
    stock: {{ (int)$b->stock }},
    category: { name: {!! json_encode($b->category->name ?? 'Umum') !!} }
});
@endforeach

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const categorySelect = document.getElementById('categorySelect');
    const btnResetFilter = document.getElementById('btnResetFilter');
    const bookGrid = document.getElementById('bookGrid');
    const paginationContainer = document.getElementById('paginationContainer');
    const activeFilterBadge = document.getElementById('activeFilterBadge');
    const bookCountBadge = document.getElementById('bookCountBadge');

    let debounceTimer = null;

    // Search input listener
    searchInput.addEventListener('input', function () {
        searchSpinner.classList.remove('hidden');
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            searchSpinner.classList.add('hidden');
            triggerSearch();
        }, 300);
    });

    // Dropdown category listener
    categorySelect.addEventListener('change', function () {
        triggerSearch();
    });

    // Reset filter button
    btnResetFilter.addEventListener('click', function () {
        searchInput.value = '';
        categorySelect.value = 'all';
        triggerSearch();
    });

    function triggerSearch() {
        const query = searchInput.value.trim();
        const selectedCat = categorySelect.value;

        const hasFilter = query !== '' || selectedCat !== 'all';
        if (hasFilter) {
            activeFilterBadge.classList.remove('hidden');
        } else {
            activeFilterBadge.classList.add('hidden');
        }

        fetch(`{{ route('peminjam.books.search') }}?q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                res.data.forEach(book => {
                    const coverUrl = book.cover_url || (book.cover_image
                        ? (book.cover_image.startsWith('http') ? book.cover_image : `/storage/${book.cover_image}`)
                        : null);

                    window.booksStore.set(book.id, {
                        id: book.id,
                        title: book.title,
                        author: book.author,
                        published_year: book.published_year,
                        description: book.description || 'Tidak ada deskripsi untuk buku ini.',
                        cover_url: coverUrl,
                        stock: parseInt(book.stock) || 0,
                        category: { name: book.category ? book.category.name : 'Umum' }
                    });
                });

                let filtered = res.data;
                if (selectedCat !== 'all') {
                    filtered = filtered.filter(b => (b.category ? b.category.name.toLowerCase() : '').includes(selectedCat));
                }
                renderBooks(filtered);

                if (bookCountBadge) {
                    bookCountBadge.textContent = `${filtered.length} judul`;
                }

                if (paginationContainer) {
                    paginationContainer.style.display = hasFilter ? 'none' : 'block';
                }
            }
        })
        .catch(() => {
            searchSpinner.classList.add('hidden');
        });
    }

    function renderBooks(books) {
        if (!books || books.length === 0) {
            bookGrid.innerHTML = `
                <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <p class="text-sm font-semibold text-slate-700">Buku tidak ditemukan</p>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau ubah pilihan kategori pada panel kiri.</p>
                </div>
            `;
            return;
        }

        bookGrid.innerHTML = books.map(book => {
            const catName = book.category ? book.category.name : 'Umum';
            const isAvail = book.stock > 0;
            const coverUrl = book.cover_url || (book.cover_image
                ? (book.cover_image.startsWith('http') ? book.cover_image : `/storage/${book.cover_image}`)
                : null);

            const coverHtml = coverUrl
                ? `<img src="${escHtml(coverUrl)}" alt="${escHtml(book.title)}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"/>`
                : `<div class="flex flex-col items-center justify-center text-sky-400 p-2 text-center"><svg class="w-8 h-8 text-sky-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg><span class="text-[9px] text-slate-400 font-mono">Tanpa Sampul</span></div>`;

            const stockBadge = isAvail
                ? `<span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>${book.stock}</span>`
                : `<span class="inline-flex items-center text-[10px] font-semibold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 shrink-0">Habis</span>`;

            const actionBtn = isAvail
                ? `<button type="button" onclick="event.stopPropagation(); openBookModal(${book.id})" class="w-full py-1.5 px-2 rounded-lg bg-sky-50 hover:bg-sky-500 hover:text-white text-sky-700 transition-colors text-[11px] font-semibold border border-sky-200 hover:border-transparent flex items-center justify-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg><span>Pinjam</span></button>`
                : `<button disabled class="w-full py-1.5 px-2 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed text-[11px] font-medium border border-slate-200">Habis</button>`;

            return `
                <div class="book-card bg-white rounded-xl border border-slate-200 flex flex-col justify-between shadow-xs hover:border-sky-300 hover:shadow-md transition-all duration-200 group cursor-pointer overflow-hidden p-2.5 sm:p-3" onclick="openBookModal(${book.id})">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-2">
                            <span class="text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100 px-1.5 py-0.5 rounded truncate max-w-[65%]">${escHtml(catName)}</span>
                            ${stockBadge}
                        </div>
                        <div class="w-full aspect-[3/4] max-h-40 rounded-lg bg-slate-100 border border-slate-200/80 overflow-hidden mb-2.5 relative flex items-center justify-center">
                            ${coverHtml}
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-sky-600 transition-colors" title="${escHtml(book.title)}">${escHtml(book.title)}</h3>
                        <p class="text-[11px] text-slate-500 truncate mt-0.5">${escHtml(book.author || '')}</p>
                        ${book.published_year ? `<p class="text-[10px] text-slate-400 mt-0.5">${escHtml(book.published_year)}</p>` : ''}
                    </div>
                    <div class="pt-2.5 mt-2 border-t border-slate-100">
                        ${actionBtn}
                    </div>
                </div>
            `;
        }).join('');
    }

    window.escHtml = function(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    };
});

// Modal Controller
let currentBookId = null;
let selectedDueDate = null;
let calViewYear = new Date().getFullYear();
let calViewMonth = new Date().getMonth();

window.openBookModal = function(bookId) {
    const book = window.booksStore.get(Number(bookId));
    if (!book) return;

    currentBookId = book.id;
    selectedDueDate = null;

    document.getElementById('modalTitle').textContent = book.title || '';
    document.getElementById('modalAuthor').textContent = 'Penulis: ' + (book.author || '-');
    document.getElementById('modalYear').textContent = book.published_year ? 'Tahun: ' + book.published_year : '';
    document.getElementById('modalDescription').textContent = book.description || 'Tidak ada deskripsi untuk buku ini.';
    document.getElementById('modalCategory').textContent = book.category?.name || 'Umum';

    const coverImg = document.getElementById('modalCover');
    const placeholder = document.getElementById('modalCoverPlaceholder');
    if (book.cover_url) {
        coverImg.src = book.cover_url;
        coverImg.classList.remove('hidden');
        placeholder.classList.add('hidden');
    } else {
        coverImg.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }

    const isAvail = book.stock > 0;
    const badge = document.getElementById('modalStockBadge');
    if (isAvail) {
        badge.innerHTML = `<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Tersedia — Stok: ${book.stock} eks.</span>`;
    } else {
        badge.innerHTML = `<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Stok Habis — Tidak dapat dipinjam</span>`;
    }

    document.getElementById('datePickerSection').style.display = isAvail ? 'block' : 'none';
    const btnPinjam = document.getElementById('btnPinjam');
    btnPinjam.disabled = true;
    document.getElementById('selectedDateLabel').textContent = 'Pilih tanggal';

    const now = new Date();
    calViewYear = now.getFullYear();
    calViewMonth = now.getMonth();
    renderCalendar();

    const modal = document.getElementById('bookModal');
    const panel = document.getElementById('modalPanel');
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    });
};

window.closeBookModal = function() {
    const modal = document.getElementById('bookModal');
    const panel = document.getElementById('modalPanel');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.style.display = 'none';
        selectedDueDate = null;
        currentBookId = null;
    }, 200);
};

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('calPrev').addEventListener('click', function() {
        calViewMonth--;
        if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
        renderCalendar();
    });
    document.getElementById('calNext').addEventListener('click', function() {
        calViewMonth++;
        if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
        renderCalendar();
    });
});

function renderCalendar() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 14);

    let selectedDate = null;
    if (selectedDueDate) {
        const p = selectedDueDate.split('-');
        selectedDate = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
        selectedDate.setHours(0, 0, 0, 0);
    }

    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    document.getElementById('calMonthYear').textContent = `${monthNames[calViewMonth]} ${calViewYear}`;

    const firstDay = new Date(calViewYear, calViewMonth, 1).getDay();
    const daysInMonth = new Date(calViewYear, calViewMonth + 1, 0).getDate();

    const calDays = document.getElementById('calDays');
    let html = '';

    for (let i = 0; i < firstDay; i++) {
        html += `<div></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const thisDate = new Date(calViewYear, calViewMonth, d);
        thisDate.setHours(0, 0, 0, 0);
        const dateStr = `${calViewYear}-${String(calViewMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

        const isToday = thisDate.getTime() === today.getTime();
        const isSelectable = thisDate > today && thisDate <= maxDate;
        const isSelected = selectedDate && thisDate.getTime() === selectedDate.getTime();
        const isInRange = selectedDate && thisDate >= tomorrow && thisDate < selectedDate;

        let wrapCls = 'relative text-center ';
        if (isInRange || isSelected) {
            wrapCls += 'bg-sky-100/70 rounded-md';
        }

        let btnCls = 'relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-medium mx-auto ';
        if (isSelected) {
            btnCls += 'bg-sky-600 text-white font-bold cursor-pointer shadow-xs ring-2 ring-sky-300 ring-offset-1';
        } else if (isInRange) {
            btnCls += 'text-sky-800 font-semibold border border-sky-300 bg-white cursor-pointer hover:bg-sky-200 transition';
        } else if (isToday) {
            btnCls += 'border-2 border-sky-400 text-sky-600 font-bold cursor-default';
        } else if (isSelectable) {
            btnCls += 'text-slate-700 hover:bg-sky-100 hover:text-sky-800 cursor-pointer transition';
        } else {
            btnCls += 'text-slate-300 cursor-not-allowed';
        }

        const onclick = isSelectable ? `onclick="selectDate('${dateStr}')"` : '';
        html += `<div class="${wrapCls}"><button type="button" class="${btnCls}" ${onclick}>${d}</button></div>`;
    }

    calDays.innerHTML = html;
}

window.selectDate = function(dateStr) {
    selectedDueDate = dateStr;
    const p = dateStr.split('-');
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('selectedDateLabel').textContent = `${parseInt(p[2])} ${months[parseInt(p[1]) - 1]} ${p[0]}`;
    document.getElementById('btnPinjam').disabled = false;
    renderCalendar();
};

window.submitLoan = function() {
    if (!currentBookId || !selectedDueDate) {
        alert('Silakan pilih tanggal pengembalian terlebih dahulu.');
        return;
    }

    const btn = document.getElementById('btnPinjam');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-4 h-4 text-white inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

    fetch("{{ route('peminjam.loans.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            book_id: currentBookId,
            due_date: selectedDueDate
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            closeBookModal();
            showSuccessPopup();
        } else {
            alert(data.message || 'Terjadi kesalahan saat memproses pinjaman.');
            btn.disabled = false;
            btn.innerHTML = 'Pinjam Buku Ini';
        }
    })
    .catch(() => {
        alert('Gagal terhubung ke server perpustakaan.');
        btn.disabled = false;
        btn.innerHTML = 'Pinjam Buku Ini';
    });
};

function showSuccessPopup() {
    const popup = document.getElementById('successPopup');
    const panel = document.getElementById('successPanel');
    popup.style.display = 'flex';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    });
}

window.closeSuccessPopup = function() {
    const popup = document.getElementById('successPopup');
    const panel = document.getElementById('successPanel');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        popup.style.display = 'none';
        window.location.reload();
    }, 200);
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('bookModal').style.display === 'flex') closeBookModal();
        if (document.getElementById('successPopup').style.display === 'flex') closeSuccessPopup();
    }
});
</script>
@endsection
