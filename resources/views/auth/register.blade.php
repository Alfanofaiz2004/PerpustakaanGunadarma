<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Daftar Akun | Perpustakaan Universitas Gunadarma</title>
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
                <a href="{{ route('login') }}" class="hover:text-sky-600 transition">Sudah Punya Akun? Masuk</a>
            </div>
        </div>
    </header>

    <!-- Centered Form Card -->
    <main class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-[420px]">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs p-6 sm:p-8">

                <!-- Segmented Tabs: Masuk vs Daftar -->
                <div class="flex p-1 bg-slate-100 rounded-xl border border-slate-200 mb-6 text-xs font-semibold">
                    <a href="{{ route('login') }}" class="flex-1 py-2 text-center rounded-lg text-slate-600 hover:text-slate-900 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 py-2 text-center rounded-lg bg-white text-sky-700 shadow-xs">
                        Daftar Baru
                    </a>
                </div>

                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold font-heading text-slate-900 tracking-tight">
                        Registrasi Anggota
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Daftar sebagai pemustaka Universitas Gunadarma
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1" for="name">
                            Nama Lengkap
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Nama sesuai KTM"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1" for="npm">
                            Nomor Pokok Mahasiswa (NPM)
                        </label>
                        <input
                            id="npm"
                            type="text"
                            name="npm"
                            value="{{ old('npm') }}"
                            required
                            placeholder="Contoh: 50421001"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1" for="email">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="nama@gunadarma.ac.id"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1" for="password">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1" for="password_confirmation">
                            Konfirmasi Ulang Kata Sandi
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi"
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"
                        />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full h-10 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-xs transition active:scale-[0.99]">
                            Daftar Sebagai Peminjam
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center border-t border-slate-200 pt-4">
                    <p class="text-xs text-slate-500">
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}" class="text-sky-600 font-semibold hover:underline ml-1">
                            Masuk di Sini
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
</body>
</html>
