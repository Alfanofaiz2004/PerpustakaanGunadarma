@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                Data Anggota &amp; Akun Pemustaka
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar mahasiswa pemustaka yang terdaftar dan riwayat buku yang dipinjam.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-200 text-slate-700 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                Total Anggota: {{ $totalMembers }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 border border-emerald-200 text-emerald-700 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Sedang Meminjam: {{ $activeBorrowers }}
            </span>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ $query ?? '' }}"
                    placeholder="Cari nama anggota, email, atau NPM..."
                    class="w-full h-9 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="h-9 px-3.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold transition shadow-xs">
                Cari
            </button>
            @if (!empty($query))
                <a href="{{ route('admin.users.index') }}" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold flex items-center transition" title="Reset">
                    Reset
                </a>
            @endif
        </form>

        <span class="text-xs text-slate-400 hidden sm:inline">Menampilkan {{ $users->total() }} akun anggota</span>
    </div>

    {{-- Members Table --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5 whitespace-nowrap">Anggota</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Terdaftar Sejak</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Status Peminjaman Aktif</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-center">Riwayat Selesai</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 leading-snug">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">
                                            @if ($user->npm)
                                                <span class="text-sky-700 font-semibold">{{ $user->npm }}</span> &middot;
                                            @endif
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap font-mono text-slate-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if ($user->active_loans_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            {{ $user->active_loans_count }} dipinjam
                                        </span>
                                    @endif
                                    @if ($user->pending_loans_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                            {{ $user->pending_loans_count }} pending
                                        </span>
                                    @endif
                                    @if ($user->return_requested_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                                            {{ $user->return_requested_count }} konfirmasi
                                        </span>
                                    @endif
                                    @if ($user->active_loans_count == 0 && $user->pending_loans_count == 0 && $user->return_requested_count == 0)
                                        <span class="text-[11px] text-slate-400 italic">Tidak ada pinjaman aktif</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center font-bold text-slate-700">
                                {{ $user->returned_loans_count }} buku
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center justify-end gap-2.5">
                                    <button
                                        type="button"
                                        onclick="openUserDetail({{ $user->id }})"
                                        class="px-2.5 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-600 hover:text-white text-sky-700 border border-sky-200 hover:border-transparent font-semibold text-[11px] transition shadow-2xs"
                                    >
                                        Lihat Rincian
                                    </button>
                                    <a
                                        href="{{ route('admin.loans.index', ['user_id' => $user->id]) }}"
                                        class="px-2.5 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 font-semibold text-[11px] transition"
                                        title="Buka riwayat peminjaman akun ini di menu Riwayat"
                                    >
                                        Riwayat Pinjam
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center text-slate-400 text-xs">
                                @if (!empty($query))
                                    Tidak ada anggota yang cocok dengan kata kunci "{{ $query }}".
                                @else
                                    Belum ada akun anggota yang terdaftar.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-3 bg-slate-50/70 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ==================== MODAL DETAIL PINJAMAN ANGGOTA ==================== --}}
<div
    id="userModal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4"
    role="dialog"
    aria-modal="true"
>
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity" onclick="closeUserModal()"></div>

    <div
        id="userModalPanel"
        class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-200 scale-95 opacity-0 flex flex-col max-h-[85vh]"
    >
        {{-- Header Modal --}}
        <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/70 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm shrink-0" id="modalUserInitial">
                    U
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate" id="modalUserName">Nama Anggota</h3>
                    <p class="text-[11px] text-slate-400 font-mono truncate" id="modalUserEmail">email@gunadarma.ac.id</p>
                </div>
            </div>
            <button
                type="button"
                onclick="closeUserModal()"
                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="p-4 sm:p-5 overflow-y-auto space-y-4 text-xs">
            <div class="flex items-center justify-between text-slate-500 text-[11px] pb-2 border-b border-slate-100">
                <span>Terdaftar sejak: <strong id="modalUserJoined" class="text-slate-700">-</strong></span>
                <span id="modalTotalLoansBadge" class="font-bold text-sky-600">0 Total Transaksi</span>
            </div>

            <div id="modalLoansList" class="space-y-2.5">
                {{-- Injected dynamically via JS --}}
                <div class="py-8 text-center text-slate-400">Memuat rincian transaksi...</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-3 sm:p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
            <a id="modalAllLoansLink" href="#" class="text-xs font-semibold text-sky-600 hover:underline flex items-center gap-1">
                <span>Buka di Menu Riwayat Peminjaman</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <button
                type="button"
                onclick="closeUserModal()"
                class="px-4 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
window.openUserDetail = function(userId) {
    const modal = document.getElementById('userModal');
    const panel = document.getElementById('userModalPanel');
    const loansList = document.getElementById('modalLoansList');

    loansList.innerHTML = `<div class="py-8 text-center text-slate-400"><svg class="animate-spin w-5 h-5 text-sky-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuat data transaksi anggota...</div>`;

    document.getElementById('modalAllLoansLink').href = `{{ route('admin.loans.index') }}?user_id=${userId}`;

    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    });

    fetch(`{{ url('/admin/users') }}/${userId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            const user = res.user;
            document.getElementById('modalUserName').textContent = user.name;
            document.getElementById('modalUserEmail').textContent = (user.npm && user.npm !== '-' ? 'NPM: ' + user.npm + ' · ' : '') + user.email;
            document.getElementById('modalUserInitial').textContent = (user.name || 'U').charAt(0).toUpperCase();
            document.getElementById('modalUserJoined').textContent = user.created_at;
            document.getElementById('modalTotalLoansBadge').textContent = `${user.loans.length} Transaksi Peminjaman`;

            if (user.loans.length === 0) {
                loansList.innerHTML = `<div class="py-8 text-center text-slate-400">Anggota ini belum pernah melakukan peminjaman buku.</div>`;
                return;
            }

            loansList.innerHTML = user.loans.map(loan => {
                let badge = '';
                if (loan.status === 'pending') {
                    badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Persetujuan</span>`;
                } else if (loan.status === 'dipinjam') {
                    badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">Sedang Dipinjam</span>`;
                } else if (loan.status === 'return_requested') {
                    badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">Menunggu Konfirmasi</span>`;
                } else {
                    badge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai Dikembalikan</span>`;
                }

                return `
                    <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[10px] text-slate-400 font-semibold">#LOAN-${String(loan.id).padStart(5, '0')}</span>
                            ${badge}
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 leading-snug">${escapeHtml(loan.book_title)}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">${escapeHtml(loan.book_author)}</p>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1.5 border-t border-slate-100">
                            <span>Pinjam: ${loan.loan_date}</span>
                            <span>Tenggat: ${loan.due_date}</span>
                            ${loan.status === 'dikembalikan' ? `<span class="text-emerald-600 font-semibold">Kembali: ${loan.return_date}</span>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }
    })
    .catch(() => {
        loansList.innerHTML = `<div class="py-8 text-center text-rose-500">Gagal memuat rincian pinjaman anggota.</div>`;
    });
};

window.closeUserModal = function() {
    const modal = document.getElementById('userModal');
    const panel = document.getElementById('userModalPanel');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 200);
};

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>
@endsection
