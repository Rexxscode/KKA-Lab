@props(['module' => null])

<div x-data="kkaAiAssistant()" class="no-print">
    <button @click="open = !open"
        class="fixed bottom-5 right-5 z-50 inline-flex items-center gap-2 px-3.5 sm:px-4 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-bold shadow-lg transition active:scale-95">
        <span class="text-lg">🤖</span><span class="hidden sm:inline" x-text="open ? 'Tutup' : 'Tanya AI'"></span>
    </button>

    <div x-show="open" x-transition class="fixed bottom-20 right-5 z-50 w-[22rem] max-w-[calc(100vw-2.5rem)] kka-card !p-0 overflow-hidden" style="display: none">
        <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-emerald-500 text-white">
            <p class="font-extrabold text-sm">Tutor AI KKA Lab</p>
            <p class="text-xs text-white/85">Tanya soal AI, KNN, bias data, atau etika. Bukan kunci jawaban kuis ya!</p>
        </div>
        <div class="h-64 overflow-y-auto p-3 space-y-2 bg-[var(--color-page)]" x-ref="box">
            <template x-for="(m, i) in messages" :key="i">
                <div :class="m.role === 'user' ? 'ml-8 bg-orange-500 text-white' : 'mr-8 bg-white border border-slate-200 text-slate-700'"
                     class="px-3 py-2 rounded-2xl text-sm whitespace-pre-wrap" x-text="m.text"></div>
            </template>
            <p x-show="loading" class="text-xs text-slate-400">AI sedang mengetik...</p>
        </div>
        <form @submit.prevent="send()" class="flex gap-2 p-3 border-t border-slate-200 bg-white">
            <input x-model="draft" placeholder="Tulis pertanyaan..." maxlength="2000"
                   class="flex-1 rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" />
            <button :disabled="loading || !draft.trim()" class="kka-btn-primary !px-3 !py-2 text-sm disabled:opacity-50">➤</button>
        </form>
    </div>

    <script>
    function kkaAiAssistant() {
        return {
            open: false, loading: false, draft: '',
            module: @js($module),
            messages: [{ role: 'model', text: 'Halo! Aku Tutor AI KKA Lab. Mau tanya apa soal AI hari ini?' }],
            async send() {
                const text = this.draft.trim();
                if (!text || this.loading) return;
                this.messages.push({ role: 'user', text });
                this.draft = ''; this.loading = true;
                this.$nextTick(() => this.$refs.box.scrollTop = this.$refs.box.scrollHeight);
                try {
                    const res = await fetch(@js(route('ai.chat')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            message: text,
                            module: this.module,
                            history: this.messages.slice(-9, -1).map(m => ({ role: m.role, text: m.text })),
                        }),
                    });
                    const data = await res.json();
                    this.messages.push({ role: 'model', text: data.reply || 'Maaf, tidak ada jawaban.' });
                } catch (e) {
                    this.messages.push({ role: 'model', text: 'Ups, gagal menghubungi AI. Coba lagi ya!' });
                }
                this.loading = false;
                this.$nextTick(() => this.$refs.box.scrollTop = this.$refs.box.scrollHeight);
            }
        }
    }
    </script>
</div>
