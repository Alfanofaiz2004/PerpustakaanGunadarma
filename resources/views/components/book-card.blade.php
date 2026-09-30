@props(['book'])

<div
    class="book-card bg-white rounded-xl border border-slate-200 flex flex-col justify-between shadow-xs hover:border-sky-300 hover:shadow-md transition-all duration-200 group cursor-pointer overflow-hidden p-2.5 sm:p-3"
    onclick="openBookModal({{ $book->id }})"
    data-book-id="{{ $book->id }}"
>
    <div>
        {{-- Card Header: Category & Stock --}}
        <div class="flex items-center justify-between gap-1.5 mb-2">
            <span class="text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100 px-1.5 py-0.5 rounded truncate max-w-[65%]">
                {{ $book->category->name ?? 'Umum' }}
            </span>
            @if ($book->stock > 0)
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $book->stock }}
                </span>
            @else
                <span class="inline-flex items-center text-[10px] font-semibold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 shrink-0">
                    Habis
                </span>
            @endif
        </div>

        {{-- Cover Image (Compact 3:4 aspect ratio) --}}
        <div class="w-full aspect-[3/4] max-h-40 rounded-lg bg-slate-100 border border-slate-200/80 overflow-hidden mb-2.5 relative flex items-center justify-center">
            @if ($book->cover_url)
                <img
                    src="{{ $book->cover_url }}"
                    alt="{{ $book->title }}"
                    loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
            @else
                <div class="flex flex-col items-center justify-center text-sky-400 p-2 text-center">
                    <svg class="w-8 h-8 text-sky-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="text-[9px] text-slate-400 font-mono">Tanpa Sampul</span>
                </div>
            @endif

            {{-- Hover overlay indicator --}}
            <div class="absolute inset-0 bg-sky-900/0 group-hover:bg-sky-900/15 transition-colors flex items-center justify-center">
                <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/95 backdrop-blur-xs text-sky-700 text-[11px] font-semibold px-2 py-1 rounded-md shadow-xs flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Detail
                </span>
            </div>
        </div>

        {{-- Book Metadata --}}
        <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-sky-600 transition-colors" title="{{ $book->title }}">
            {{ $book->title }}
        </h3>
        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $book->author }}</p>
        @if ($book->published_year)
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $book->published_year }}</p>
        @endif
    </div>

    {{-- Bottom Action Button --}}
    <div class="pt-2.5 mt-2 border-t border-slate-100">
        @if ($book->stock > 0)
            <button
                type="button"
                onclick="event.stopPropagation(); openBookModal({{ $book->id }})"
                class="w-full py-1.5 px-2 rounded-lg bg-sky-50 hover:bg-sky-500 hover:text-white text-sky-700 transition-colors text-[11px] font-semibold border border-sky-200 hover:border-transparent flex items-center justify-center gap-1"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Pinjam</span>
            </button>
        @else
            <button
                disabled
                class="w-full py-1.5 px-2 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed text-[11px] font-medium border border-slate-200"
            >
                Habis
            </button>
        @endif
    </div>
</div>
