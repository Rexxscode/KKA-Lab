<x-ai-log-list
    title="Riwayat AI Seluruh Pengguna"
    subtitle="Audit pemakaian Tutor AI. Kuota 30/hari/pengguna."
    :filterAction="route('admin.ai.index')"
    :modules="$modules"
    :filter="$filter"
    :logs="$logs"
    emptyText="Belum ada percakapan AI."
    :showRole="true"
/>
