<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapor {{ $siswa->name }} — KKALAB</title>
    <meta name="description" content="Rapor belajar {{ $siswa->name }} di KKALAB." />
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; color: #0f172a; max-width: 720px; margin: 0 auto; padding: 32px 20px; }
        h1 { font-size: 22px; margin: 0; }
        .meta { color: #64748b; font-size: 13px; margin: 4px 0 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        th { background: #f1f5f9; }
        .lulus { color: #047857; font-weight: 800; }
        .belum { color: #c2410c; font-weight: 700; }
        .saran { margin-top: 20px; font-size: 13px; }
        .saran li { margin-bottom: 6px; }
        .toolbar { margin-bottom: 20px; display: flex; gap: 8px; }
        .toolbar button, .toolbar a { font-size: 13px; font-weight: 700; border-radius: 10px; padding: 8px 14px; border: 1px solid #cbd5e1; background: #fff; color: #0f172a; text-decoration: none; cursor: pointer; }
        .toolbar button { background: #f97316; border-color: #f97316; color: #fff; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="toolbar">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('siswa.raporku') }}">Kembali</a>
    </div>
    <h1>Rapor Belajar — {{ $siswa->name }}</h1>
    <p class="meta">KKALAB • {{ $lulus }}/{{ count($daftar) }} modul lulus • Total {{ $totalXp }} XP • {{ now()->format('d M Y') }}</p>
    <table>
        <thead>
            <tr><th>No</th><th>Modul</th><th>Skor</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($daftar as $d)
                @php $p = $d['progress']; @endphp
                <tr>
                    <td>{{ $d['posisi'] }}</td>
                    <td>{{ $d['module']->title }}</td>
                    <td>{{ $p ? $p->quiz_score : '—' }}</td>
                    <td class="{{ $p && $p->is_completed ? 'lulus' : 'belum' }}">{{ $p && $p->is_completed ? 'Lulus' : 'Belum lulus' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if(count($saran))
        <div class="saran">
            <strong>Saran belajar:</strong>
            <ul>
                @foreach($saran as $s)
                    <li><strong>{{ $s['modul'] }}:</strong> {{ $s['teks'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if($umpanBalik->count())
        <div class="saran">
            <strong>Catatan guru:</strong>
            <ul>
                @foreach($umpanBalik as $fb)
                    <li><strong>{{ $fb->module->title ?? 'Umum' }}:</strong> {{ $fb->body }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>
