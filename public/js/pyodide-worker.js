/* KKA Lab — Web Worker Python (Pyodide).
 * Python asli via WASM, 100% di browser. BUKAN simulasi, BUKAN API luar.
 * Anti-hang: thread utama menghentikan (terminate) worker ini bila
 * eksekusi melewati 10 detik — satu `while True` tidak membekukan tab.
 */
'use strict';

var PYODIDE_URL = 'https://cdn.jsdelivr.net/pyodide/v0.26.4/full/pyodide.js';

var pyodide = null;
var lagiJalan = false;

function kirim(type, data) {
  self.postMessage({ type: type, data: data });
}

self.onmessage = function (e) {
  var pesan = e.data || {};
  if (pesan.cmd === 'jalankan') {
    jalankan(pesan.code || '');
  }
};

async function jalankan(code) {
  if (lagiJalan) {
    return;
  }
  lagiJalan = true;
  try {
    if (!pyodide) {
      kirim('status', 'mengunduh-runtime');
      importScripts(PYODIDE_URL);
      pyodide = await loadPyodide();
    }
    kirim('status', 'mengeksekusi');
    var keluar = [];
    pyodide.setStdout({
      batched: function (teks) {
        keluar.push(teks);
      },
    });
    pyodide.setStderr({
      batched: function (teks) {
        keluar.push(teks);
      },
    });
    var hasil;
    try {
      hasil = await pyodide.runPythonAsync(code);
    } catch (err) {
      kirim('selesai', { ok: false, output: keluar.join('\n'), error: String((err && err.message) || err) });
      return;
    }
    if (hasil !== undefined && hasil !== null) {
      var teks = String(hasil);
      if (teks !== '') {
        keluar.push(teks);
      }
    }
    kirim('selesai', { ok: true, output: keluar.join('\n'), error: null });
  } catch (err) {
    kirim('selesai', {
      ok: false,
      output: '',
      error: 'Gagal memuat Python (butuh internet sekali untuk mengunduh ±10MB). Periksa koneksi lalu tekan Jalankan lagi. Rincian: ' + String((err && err.message) || err),
      offline: true,
    });
  } finally {
    lagiJalan = false;
  }
}
