<header
    x-data="{
        mobileMenuOpen: false,
        notifOpen: false,
        unreadCount: 0,
        notifications: [],
        loading: false,
        async fetchNotifications() {
            try {
                const res = await fetch('{{ route('notifications.index') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.unreadCount = data.unread_count || 0;
                    this.notifications = data.notifications || [];
                }
            } catch (e) {
                console.error('Failed to load notifications', e);
            }
        },
        async markAllRead() {
            try {
                const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}';
                const res = await fetch('{{ route('notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                if (res.ok) {
                    this.unreadCount = 0;
                    this.notifications.forEach(n => n.is_read = true);
                }
            } catch (e) {
                console.error(e);
            }
        },
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 25000);
        }
    }"
    class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-xs"
>
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand Logo & Soft Badge -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <span class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold shadow-xs group-hover:bg-sky-700 transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="text-sm sm:text-base font-bold tracking-tight text-slate-900 group-hover:text-sky-600 transition block leading-tight">
                            Perpustakaan Gunadarma
                        </span>
                        <span class="text-[10px] text-slate-400 hidden sm:block">Sistem Peminjaman Buku</span>
                    </div>
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ Auth::user()->isAdmin() ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                    {{ Auth::user()->role }}
                </span>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 h-full">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-1.5 h-16 border-b-2 {{ request()->routeIs('admin.books.*') ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium' }} transition-colors text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Kelola Buku</span>
                    </a>
                    <a href="{{ route('admin.loans.index') }}" class="inline-flex items-center gap-1.5 h-16 border-b-2 {{ request()->routeIs('admin.loans.*') ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium' }} transition-colors text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Riwayat Peminjaman</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 h-16 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium' }} transition-colors text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Data Anggota</span>
                    </a>
                @else
                    <a href="{{ route('peminjam.books.index') }}" class="inline-flex items-center gap-1.5 h-16 border-b-2 {{ request()->routeIs('peminjam.books.*') ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium' }} transition-colors text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Katalog Buku</span>
                    </a>
                    <a href="{{ route('peminjam.loans.index') }}" class="inline-flex items-center gap-1.5 h-16 border-b-2 {{ request()->routeIs('peminjam.loans.*') ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium' }} transition-colors text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat Peminjaman</span>
                    </a>
                @endif
            </nav>

            <!-- Desktop Profile & Actions -->
            <div class="hidden md:flex items-center gap-3">
                <!-- Notification Bell (Desktop) -->
                <button
                    type="button"
                    @click="notifOpen = !notifOpen"
                    class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-sky-500/20"
                    title="Pusat Notifikasi"
                    aria-label="Pusat Notifikasi"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span
                        x-show="unreadCount > 0"
                        x-text="unreadCount > 9 ? '9+' : unreadCount"
                        x-cloak
                        class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white shadow-xs animate-pulse"
                    ></span>
                </button>

                <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-700 border border-sky-200 flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-slate-800 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 leading-none mt-1">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors flex items-center gap-1 text-xs font-medium" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>

            <!-- Mobile Controls -->
            <div class="flex items-center gap-1 md:hidden">
                <!-- Mobile Notification Bell -->
                <button
                    type="button"
                    @click="notifOpen = !notifOpen"
                    class="relative p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none"
                    aria-label="Pusat Notifikasi"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span
                        x-show="unreadCount > 0"
                        x-text="unreadCount > 9 ? '9+' : unreadCount"
                        x-cloak
                        class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white shadow-xs animate-pulse"
                    ></span>
                </button>

                <!-- Mobile Hamburger Toggle Button -->
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none"
                    aria-label="Toggle Navigation"
                >
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Dropdown Notifikasi -->
    <div
        x-show="notifOpen"
        x-cloak
        @click.away="notifOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        class="absolute right-3 sm:right-6 lg:right-8 top-16 w-[calc(100vw-24px)] sm:w-96 max-w-sm bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden z-50 divide-y divide-slate-100"
    >
        <!-- Popover Header -->
        <div class="px-4 py-3 bg-slate-50/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pusat Notifikasi</h3>
                <span
                    x-show="unreadCount > 0"
                    x-text="unreadCount + ' Baru'"
                    class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full border border-sky-200"
                ></span>
            </div>
            <button
                type="button"
                x-show="unreadCount > 0"
                @click="markAllRead()"
                class="text-[11px] font-semibold text-sky-600 hover:text-sky-700 hover:underline"
            >
                Tandai dibaca
            </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
            <template x-for="item in notifications" :key="item.id">
                <a
                    :href="'/notifications/' + item.id + '/read'"
                    class="block p-3.5 hover:bg-slate-50/80 transition group"
                    :class="{ 'bg-sky-50/30': !item.is_read }"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0 mt-0.5">
                            <template x-if="item.type === 'success'">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </template>
                            <template x-if="item.type === 'danger'">
                                <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center border border-rose-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            </template>
                            <template x-if="item.type === 'info' || !['success', 'danger'].includes(item.type)">
                                <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center border border-sky-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-xs font-bold text-slate-800 leading-snug group-hover:text-sky-600 transition" x-text="item.title"></h4>
                                <span x-show="!item.is_read" class="w-2 h-2 rounded-full bg-sky-500 flex-shrink-0" title="Belum dibaca"></span>
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed line-clamp-2" x-text="item.message"></p>
                            <span class="text-[10px] text-slate-400 mt-1 inline-block" x-text="item.time_ago"></span>
                        </div>
                    </div>
                </a>
            </template>

            <!-- Empty State -->
            <div x-show="notifications.length === 0" class="p-8 text-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <p class="text-xs font-semibold text-slate-700">Tidak ada notifikasi</p>
                <p class="text-[11px] text-slate-400">Pemberitahuan persetujuan dan pengembalian akan muncul di sini.</p>
            </div>
        </div>

        <!-- Popover Footer -->
        <div class="p-2.5 bg-slate-50/70 text-center">
            <a
                href="{{ Auth::user()->isAdmin() ? route('admin.loans.index') : route('peminjam.loans.index') }}"
                class="text-xs font-semibold text-sky-600 hover:text-sky-700 hover:underline flex items-center justify-center gap-1"
            >
                <span>Lihat Semua Riwayat Peminjaman</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden border-t border-slate-200 bg-white/95 backdrop-blur-md px-4 pt-3 pb-5 space-y-3"
    >
        <!-- User Info Strip -->
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
            <div class="w-9 h-9 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-bold border border-sky-200">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-amber-100 text-amber-700' : 'bg-sky-100 text-sky-700' }}">
                {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>

        <!-- Links -->
        <div class="space-y-1">
            @if (Auth::user()->isAdmin())
                <a
                    href="{{ route('admin.books.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.books.*') ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-700 hover:bg-slate-100' }} transition"
                >
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Kelola Buku</span>
                </a>
                <a
                    href="{{ route('admin.loans.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.loans.*') ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-700 hover:bg-slate-100' }} transition"
                >
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Riwayat Peminjaman</span>
                </a>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-700 hover:bg-slate-100' }} transition"
                >
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Data Anggota</span>
                </a>
            @else
                <a
                    href="{{ route('peminjam.books.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('peminjam.books.*') ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-700 hover:bg-slate-100' }} transition"
                >
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Katalog Buku</span>
                </a>
                <a
                    href="{{ route('peminjam.loans.index') }}"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('peminjam.loans.*') ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-700 hover:bg-slate-100' }} transition"
                >
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Peminjaman</span>
                </a>
            @endif
        </div>

        <!-- Logout Button in Mobile Menu -->
        <div class="pt-2 border-t border-slate-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition border border-rose-200"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </div>
</header>
