@if(session('toast'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
     class="fixed top-4 right-4 z-50 max-w-sm w-full">
    <div class="flex items-start gap-3 p-4 rounded-2xl border shadow-lg backdrop-blur bg-white/95 {{ session('toast.type') === 'success' ? 'border-emerald-200' : 'border-orange-200' }}">
        <span class="inline-flex w-8 h-8 rounded-full items-center justify-center font-bold {{ session('toast.type') === 'success' ? 'bg-emerald-500 text-white' : 'bg-orange-500 text-white' }}">
            {{ session('toast.type') === 'success' ? '✓' : '!' }}
        </span>
        <div class="flex-1">
            <p class="text-sm font-semibold text-slate-800">{{ session('toast.message') }}</p>
        </div>
        <button @click="show = false" class="text-slate-400 hover:text-slate-600 font-bold">×</button>
    </div>
</div>
@endif
