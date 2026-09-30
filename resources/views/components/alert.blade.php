@if (session('success'))
    <div class="mb-6 p-4 rounded-lg bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 flex items-center justify-between shadow-sm" role="alert">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 rounded-lg bg-rose-50 border-l-4 border-rose-500 text-rose-800 flex items-center justify-between shadow-sm" role="alert">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('error') }}</span>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-lg bg-rose-50 border-l-4 border-rose-500 text-rose-800 shadow-sm" role="alert">
        <div class="font-semibold text-sm mb-1">Terdapat kesalahan pada inputan:</div>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
