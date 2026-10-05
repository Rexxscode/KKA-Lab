@if(session('toast'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
     class="fixed top-20 lg:top-4 right-4 left-4 sm:left-auto z-50 sm:max-w-sm sm:w-full" role="status">
    <div class="flex items-start gap-3 p-4 rounded-2xl border shadow-lg bg-white {{ session('toast.type') === 'success' ? 'border-emerald-200' : 'border-orange-200' }}">
        <span class="inline-flex w-8 h-8 shrink-0 rounded-full items-center justify-center {{ session('toast.type') === 'success' ? 'bg-emerald-600 text-white' : 'bg-orange-500 text-white' }}">
            @if(session('toast.type') === 'success')
                <x-icon name="check" class="w-4 h-4" />
            @else
                <x-icon name="alert" class="w-4 h-4" />
            @endif
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-800">{{ session('toast.message') }}</p>
        </div>
        <button @click="show = false" aria-label="Tutup notifikasi" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 font-bold shrink-0">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
</div>
@endif
