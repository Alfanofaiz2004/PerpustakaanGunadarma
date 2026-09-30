@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold font-heading text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </span>
                <span>Tambah Koleksi Buku Baru</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Isi rincian buku dan unggah foto sampul untuk katalog perpustakaan.</p>
        </div>
        <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:border-slate-300 text-xs font-semibold transition active:scale-[0.98] self-start sm:self-auto shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 sm:p-7">
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-6">
            @csrf

            <!-- Left Column: Cover Image Upload -->
            <div class="md:col-span-4 space-y-2">
                <label for="cover_image" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Foto Sampul Buku</label>
                
                <div class="relative w-full aspect-[3/4] rounded-xl border-2 border-dashed border-sky-200 bg-sky-50/40 hover:bg-sky-50 hover:border-sky-400 transition flex flex-col items-center justify-center text-center p-4 cursor-pointer group" id="dropzoneContainer">
                    <input type="file" name="cover_image" id="cover_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)"/>
                    
                    <div id="uploadPlaceholder" class="space-y-2 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-white border border-sky-200 text-sky-500 flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Pilih / Drag Foto</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG, WEBP (Maks. 2MB)</p>
                        </div>
                    </div>

                    <img id="imagePreview" src="#" alt="Preview Sampul" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl shadow-xs"/>
                </div>
                <p class="text-[10px] text-slate-400 text-center">Rasio sampul otomatis disesuaikan secara proporsional.</p>
            </div>

            <!-- Right Column: Book Metadata Form -->
            <div class="md:col-span-8 space-y-4">
                <div>
                    <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Pemrograman Web Modern" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"/>
                </div>

                <div>
                    <label for="author" class="block text-xs font-semibold text-slate-700 mb-1">Nama Penulis <span class="text-rose-500">*</span></label>
                    <input type="text" name="author" id="author" value="{{ old('author') }}" required placeholder="Contoh: Ahmad Subagja" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"/>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="category_id" class="block text-xs font-semibold text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                            <button type="button" onclick="openCategoryModal()" class="text-[10px] font-semibold text-sky-600 hover:text-sky-700 flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Baru</span>
                            </button>
                        </div>
                        <select name="category_id" id="category_id" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition">
                            <option value="">-- Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stock" class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <div class="relative flex items-center">
                            <input type="number" name="stock" id="stock" value="{{ old('stock', 5) }}" min="0" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"/>
                            <span class="absolute right-3 text-xs text-slate-400 font-mono">Eks.</span>
                        </div>
                    </div>

                    <div>
                        <label for="published_year" class="block text-xs font-semibold text-slate-700 mb-1">Tahun Terbit</label>
                        <input type="number" name="published_year" id="published_year" value="{{ old('published_year') }}" min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition"/>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Sinopsis Buku</label>
                    <textarea name="description" id="description" rows="3" placeholder="Tulis ringkasan atau sinopsis singkat..." class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none transition resize-none">{{ old('description') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs transition shadow-xs flex items-center gap-1.5 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Koleksi Buku</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Inline Tambah Kategori Baru -->
<div id="categoryModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-2xs flex items-center justify-center p-4 hidden opacity-0 transition-opacity">
    <div class="bg-white border border-slate-200 rounded-2xl p-5 max-w-sm w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span>Tambah Kategori Baru</span>
            </h3>
            <button type="button" onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1" for="newCategoryName">Nama Kategori</label>
            <input type="text" id="newCategoryName" placeholder="Contoh: Kecerdasan Buatan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none"/>
        </div>
        <div class="flex items-center justify-end gap-2 pt-1">
            <button type="button" onclick="closeCategoryModal()" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="button" onclick="saveNewCategory()" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-sky-600 text-white hover:bg-sky-700 shadow-xs">Simpan Kategori</button>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('uploadPlaceholder');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function openCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function saveNewCategory() {
        const name = document.getElementById('newCategoryName').value.trim();
        if (!name) return alert('Nama kategori wajib diisi!');

        fetch("{{ route('admin.categories.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ name: name })
        })
        .then(res => res.json())
        .then(data => {
            closeCategoryModal();
            const select = document.getElementById('category_id');
            const opt = document.createElement('option');
            opt.value = data.id || Date.now();
            opt.textContent = name;
            opt.selected = true;
            select.appendChild(opt);
            document.getElementById('newCategoryName').value = '';
        })
        .catch(() => {
            window.location.reload();
        });
    }
</script>
@endsection
