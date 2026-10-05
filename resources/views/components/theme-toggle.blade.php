<button type="button"
    x-data="{ gelap: document.documentElement.classList.contains('dark') }"
    @click="gelap = !gelap; document.documentElement.classList.toggle('dark', gelap); try { localStorage.setItem('kka-theme', gelap ? 'dark' : 'light'); } catch (e) {} window.dispatchEvent(new CustomEvent('kka:theme'));"
    :aria-pressed="gelap.toString()"
    aria-label="Ganti tema gelap terang"
    title="Ganti tema"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-orange-600 transition dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-orange-400']) }}>
    <svg x-show="!gelap" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0012 21.75a9.753 9.753 0 009.752-6.748z" /></svg>
    <svg x-show="gelap" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true" style="display: none"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
</button>
