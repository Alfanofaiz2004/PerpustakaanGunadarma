@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 font-heading">Kategori Buku</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar kategori perpustakaan.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-900 text-sm mb-4">Tambah Kategori Baru</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" id="name" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm" placeholder="Contoh: Rekayasa Perangkat Lunak">
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#1E3A8A] hover:bg-blue-900 text-white font-medium text-sm rounded-lg transition shadow-sm">
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <div class="md:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-100/70 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">No</th>
                        <th class="px-6 py-3.5">Nama Kategori</th>
                        <th class="px-6 py-3.5">Jumlah Buku</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $index => $cat)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-mono text-slate-400">
                                {{ $categories->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900">
                                {{ $cat->name }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $cat->books_count }} buku
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline-block" onsubmit="return confirm('Menghapus kategori ini juga akan mempengaruhi buku terkait. Yakin?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-md text-xs font-medium border border-rose-200 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($categories->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
