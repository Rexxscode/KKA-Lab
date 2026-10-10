{{-- Skrip shared kuis & ujian akhir: stepper Alpine, guard beforeunload,
  hitung mundur (server yang menegakkan; habis -> kumpul otomatis). --}}
<script>
function kkaBelajar(total) {
    return {
        total: total, soalIdx: 0, pesan: '', sudahKumpul: false,
        dijawab(i) {
            return !!document.querySelector('input[name="answers[' + i + ']"]:checked');
        },
        terjawabSemua() {
            let n = 0;
            for (let i = 0; i < this.total; i++) if (this.dijawab(i)) n++;
            return n;
        },
        keSoal(i) {
            this.soalIdx = Math.max(0, Math.min(this.total - 1, i));
            this.pesan = '';
            const aktif = document.querySelector('fieldset[x-show]');
            if (aktif) {
                const radio = aktif.querySelector('input[type="radio"]');
                if (radio) radio.focus({ preventScroll: true });
            }
        },
        kumpulValid() {
            for (let i = 0; i < this.total; i++) {
                if (!this.dijawab(i)) {
                    this.soalIdx = i;
                    this.pesan = 'Masih ada soal belum dijawab (cek soal ' + (i + 1) + ').';
                    return false;
                }
            }
            return true;
        }
    }
}
// Cegah jawaban hilang karena salah tekan back / tutup tab saat ujian berjalan.
(function () {
    let terkunci = true;
    document.getElementById('form-kuis')?.addEventListener('submit', function () { terkunci = false; });
    window.addEventListener('beforeunload', function (e) {
        if (!terkunci) return;
        const terjawab = document.querySelectorAll('#form-kuis input[type="radio"]:checked').length;
        if (terjawab > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
})();
// Hitung mundur batas waktu (server yang menegakkan). Habis -> kumpul otomatis.
(function () {
    const el = document.getElementById('sisa-waktu');
    if (!el) return;
    let sisa = parseInt(el.dataset.detik || '0', 10);
    const cetak = () => {
        const m = String(Math.floor(Math.max(0, sisa) / 60)).padStart(2, '0');
        const d = String(Math.max(0, sisa) % 60).padStart(2, '0');
        el.textContent = m + ':' + d;
    };
    cetak();
    setInterval(function () {
        sisa--;
        cetak();
        if (sisa <= 0) {
            const form = document.getElementById('form-kuis');
            if (form && !form.dataset.kadaluarsa) {
                form.dataset.kadaluarsa = '1';
                form.submit();
            }
        }
    }, 1000);
})();
</script>
