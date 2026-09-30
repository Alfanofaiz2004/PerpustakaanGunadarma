<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Masuk | Perpustakaan Universitas Gunadarma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 flex flex-col justify-between antialiased font-sans">

    <!-- Minimal Header -->
    <header class="w-full bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-20">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a class="flex items-center gap-2.5 group" href="/">
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
            <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                <a href="{{ route('register') }}" class="hover:text-sky-600 transition">Daftar Akun</a>
            </div>
        </div>
    </header>

    <!-- Centered Form Card -->
    <main class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-[420px]">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs p-6 sm:p-8">

                <!-- Segmented Tabs: Masuk vs Daftar -->
                <div class="flex p-1 bg-slate-100 rounded-xl border border-slate-200 mb-6 text-xs font-semibold">
                    <a href="{{ route('login') }}" class="flex-1 py-2 text-center rounded-lg bg-white text-sky-700 shadow-xs">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 py-2 text-center rounded-lg text-slate-600 hover:text-slate-900 transition">
                        Daftar Baru
                    </a>
                </div>

                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold font-heading text-slate-900 tracking-tight">
                        Masuk ke Akun
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Gunakan email akun Admin atau Mahasiswa Anda
                    </p>
                </div>

                <!-- Session Alert -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Demo Hint Box -->
                <div class="mb-5 p-3 rounded-xl bg-sky-50/70 border border-sky-200 text-[11px] text-slate-600 space-y-1">
                    <p class="font-bold text-sky-800 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Akun Uji Coba (Demo):
                    </p>
                    <p><span class="font-semibold text-slate-700">Admin:</span> admin@gunadarma.ac.id / password</p>
                    <p><span class="font-semibold text-slate-700">Mahasiswa:</span> mahasiswa@gunadarma.ac.id <span class="text-sky-700 font-mono">(atau NPM: 50421001)</span> / password</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5" for="login">
                            Alamat Email atau Nomor NPM
                        </label>
                        <input
                            id="login"
                            type="text"
                            name="login"
                            value="{{ old('login', old('email')) }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="NPM atau Email"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5" for="password">
                            Kata Sandi
                        </label>
                        <div class="relative flex items-center">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full h-10 pl-3.5 pr-10 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                            />
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                aria-label="Lihat kata sandi"
                                class="absolute right-0 pr-3 text-slate-400 hover:text-slate-600 flex items-center justify-center"
                            >
                                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500/20 cursor-pointer"/>
                            <span class="text-xs text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full h-10 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-xs transition active:scale-[0.99]">
                            Masuk
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center border-t border-slate-200 pt-4">
                    <p class="text-xs text-slate-500">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}" class="text-sky-600 font-semibold hover:underline ml-1">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>

            </div>

            <p class="text-center text-[11px] text-slate-400 mt-4">
                Universitas Gunadarma &copy; {{ date('Y') }}
            </p>
        </div>
    </main>

    <!-- Clean Footer -->
    <footer class="w-full bg-white border-t border-slate-200 py-4 text-center text-[11px] text-slate-400">
        Perpustakaan Universitas Gunadarma &mdash; Sistem Peminjaman Buku
    </footer>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
            } else {
                pwd.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }
    </script>
</body>
</html>
