<x-ai-log-list
    title="Riwayat AI Siswa Kelasmu"
    subtitle="Pantau pertanyaan ke Tutor AI — bantu yang nyasar, apresiasi yang kritis. Kuota 30/hari/siswa."
    :filterAction="route('guru.ai.index')"
    :modules="$modules"
    :filter="$filter"
    :logs="$logs"
    emptyText="Belum ada percakapan AI dari siswamu."
    :showRole="false"
/>
