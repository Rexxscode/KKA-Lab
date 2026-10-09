/**
 * Logika murni halaman Latih Model (tanpa TensorFlow, tanpa DOM).
 * Dipakai resources/js/ai/trainer.js dan diuji via node --test.
 */

export const MIN_LABEL = 2;
export const MAX_LABEL = 5;
export const MIN_SAMPEL = 20;
export const STREAK_SYARAT = 5;
export const MAKS_LABEL_PANJANG = 30;
export const MIN_SAMPEL_LATIH = 10;
export const MIN_SAMPEL_UJI = 10;
export const MIN_CONFIDENCE = 0.6;
export const MAKS_EPOCH = 20;
export const MAKS_BATCH = 16;
export const CAHAYA_MIN = 25;
export const CAHAYA_MAKS = 230;
export const JARAK_DUPLIKAT_MIN = 0.05;

/** Validasi daftar label karya siswa. */
export function validasiLabel(daftar) {
  const bersih = (daftar || []).map((s) => String(s || '').trim()).filter(Boolean);
  if (bersih.length < MIN_LABEL) return { ok: false, galat: `Minimal ${MIN_LABEL} label, mis. "Botol" dan "Buku".` };
  if (bersih.length > MAX_LABEL) return { ok: false, galat: `Maksimal ${MAX_LABEL} label.` };
  if (new Set(bersih.map((s) => s.toLowerCase())).size !== bersih.length) {
    return { ok: false, galat: 'Nama label tidak boleh sama.' };
  }
  if (bersih.some((s) => s.length > MAKS_LABEL_PANJANG)) {
    return { ok: false, galat: `Nama label maksimal ${MAKS_LABEL_PANJANG} karakter.` };
  }
  return { ok: true, galat: '', labels: bersih };
}

/**
 * Syarat publish: tiap label >= MIN_SAMPEL contoh dan streak uji beruntun tercapai.
 * jumlah: {label: n}, streak: int.
 */
export function cekSyarat(jumlah, streak) {
  const alasan = [];
  const labels = Object.keys(jumlah || {});
  if (labels.length < MIN_LABEL) alasan.push(`Butuh minimal ${MIN_LABEL} label.`);
  for (const label of labels) {
    if ((jumlah[label] || 0) < MIN_SAMPEL) alasan.push(`"${label}" baru ${jumlah[label] || 0}/${MIN_SAMPEL} contoh.`);
  }
  if ((streak || 0) < STREAK_SYARAT) alasan.push(`Uji beruntun ${streak || 0}/${STREAK_SYARAT} benar.`);
  return { ok: alasan.length === 0, alasan };
}

function keB64(bytes) {
  if (typeof Buffer !== 'undefined') return Buffer.from(bytes).toString('base64');
  let s = '';
  const CH = 8192;
  for (let i = 0; i < bytes.length; i += CH) {
    s += String.fromCharCode.apply(null, bytes.subarray(i, i + CH));
  }
  // eslint-disable-next-line no-undef
  return btoa(s);
}

function dariB64(b64) {
  if (typeof Buffer !== 'undefined') return new Uint8Array(Buffer.from(b64, 'base64'));
  // eslint-disable-next-line no-undef
  const s = atob(b64);
  const out = new Uint8Array(s.length);
  for (let i = 0; i < s.length; i++) out[i] = s.charCodeAt(i);
  return out;
}

/**
 * Serialisasi dataset KNN {label: [Float32Array]} menjadi JSON aman-kirim.
 * Bentuk: {backbone, dim, labels[], contoh: {label: [b64...]}}.
 */
export function serializeDataset(dataset, backbone = 'mobilenet') {
  const labels = Object.keys(dataset || {});
  let dim = 0;
  const contoh = {};
  for (const label of labels) {
    contoh[label] = (dataset[label] || []).map((arr) => {
      dim = dim || arr.length;
      return keB64(new Uint8Array(arr.buffer, arr.byteOffset, arr.byteLength));
    });
  }
  return { backbone, dim, labels, contoh };
}

/** Kebalikan serializeDataset -> {label: [Float32Array]}. */
export function deserializeDataset(data) {
  const keluar = {};
  if (!data || !Array.isArray(data.labels)) return keluar;
  for (const label of data.labels) {
    keluar[label] = ((data.contoh || {})[label] || []).map((b64) => {
      const b = dariB64(b64);
      return new Float32Array(b.buffer, b.byteOffset, b.byteLength / 4);
    });
  }
  return keluar;
}

/**
 * Estimasi ukuran dataset (byte) untuk validasi batas upload.
 */
export function ukuranDataset(data) {
  let n = 0;
  for (const label of data.labels || []) {
    for (const b64 of (data.contoh || {})[label] || []) n += Math.ceil((b64.length * 3) / 4);
  }
  return n;
}

/**
 * Syarat mode Latih Lanjutan (transfer learning): minimal 2 label
 * dengan minimal MIN_SAMPEL_LATIH contoh tiap label.
 */
export function validasiLatih(jumlah) {
  const labels = Object.keys(jumlah || {});
  if (labels.length < MIN_LABEL) return { ok: false, galat: `Butuh minimal ${MIN_LABEL} label berisi contoh.` };
  for (const label of labels) {
    if ((jumlah[label] || 0) < MIN_SAMPEL_LATIH) {
      return { ok: false, galat: `"${label}" baru ${jumlah[label] || 0}/${MIN_SAMPEL_LATIH} contoh untuk latih lanjutan.` };
    }
  }
  return { ok: true, galat: '' };
}

/** Jepit hiperparameter ke batas aman HP (anti-OOM / anti-hang). */
export function jepitHiperparam({ epochs = 10, batchSize = 5 } = {}) {
  return {
    epochs: Math.max(1, Math.min(Math.floor(epochs) || 10, MAKS_EPOCH)),
    batchSize: Math.max(1, Math.min(Math.floor(batchSize) || 5, MAKS_BATCH)),
  };
}

/**
 * Syarat uji beruntun (anti-ngasal): minimal 2 label,
 * tiap label >= MIN_SAMPEL_UJI contoh. Publish tetap butuh MIN_SAMPEL.
 */
export function layakUji(jumlah) {
  const labels = Object.keys(jumlah || {});
  if (labels.length < MIN_LABEL) return { ok: false, galat: `Butuh minimal ${MIN_LABEL} label berisi contoh.` };
  for (const label of labels) {
    if ((jumlah[label] || 0) < MIN_SAMPEL_UJI) {
      return { ok: false, galat: `"${label}" baru ${jumlah[label] || 0}/${MIN_SAMPEL_UJI} contoh untuk uji. Tambah contoh yang bervariasi dulu.` };
    }
  }
  return { ok: true, galat: '' };
}

/**
 * Putuskan hasil prediksi dengan ambang keyakinan (anti-ngasal).
 * Kembalikan {label, confidence, tidakYakin, alasan}.
 */
export function putuskanHasil(label, confidence, minConfidence = MIN_CONFIDENCE) {
  const keyakinan = Number(confidence);
  if (!Number.isFinite(keyakinan)) {
    return { label, confidence: 0, tidakYakin: true, alasan: 'Keyakinan tidak valid.' };
  }
  if (keyakinan < minConfidence) {
    return {
      label,
      confidence: keyakinan,
      tidakYakin: true,
      alasan: `Keyakinan ${Math.round(keyakinan * 100)}% di bawah ambang ${Math.round(minConfidence * 100)}%. Dekatkan benda, perbaiki cahaya, tambah contoh bervariasi.`,
    };
  }
  return { label, confidence: keyakinan, tidakYakin: false, alasan: '' };
}

/**
 * Bbox persegi tengah frame (crop wajib anti-background).
 * videoW/H resolusi asli, rasio 0..1 dari sisi terpendek.
 */
export function bboxTengah(videoW, videoH, rasio = 0.6) {
  const w = Number(videoW);
  const h = Number(videoH);
  const r = Number(rasio);
  if (!Number.isFinite(w) || !Number.isFinite(h) || w <= 0 || h <= 0) return null;
  const rasioAman = Math.max(0.3, Math.min(r || 0.6, 0.9));
  const s = Math.min(w, h) * rasioAman;
  if (!(s > 0)) return null;
  return [w / 2 - s / 2, h / 2 - s / 2, s, s];
}

/** Rata-rata terang 0..255 dari data piksel (Uint8ClampedArray RGBA). */
export function rataTerang(dataPiksel) {
  if (!dataPiksel || !dataPiksel.length) return null;
  const n = Math.floor(dataPiksel.length / 4);
  if (n <= 0) return null;
  // Sampel adaptif: semua piksel bila sedikit, tiap 16 piksel bila banyak (cepat di HP).
  const langkahPiksel = n > 1000 ? 16 : 1;
  const langkahByte = langkahPiksel * 4;
  let total = 0;
  let hitung = 0;
  for (let i = 0; i + 2 < dataPiksel.length; i += langkahByte) {
    total += (dataPiksel[i] + dataPiksel[i + 1] + dataPiksel[i + 2]) / 3;
    hitung++;
  }
  if (!hitung) return null;
  return total / hitung;
}

/** True bila cahaya terlalu gelap/terang untuk dilatih. */
export function isCahayaBuruk(rata) {
  if (rata === null || rata === undefined || !Number.isFinite(rata)) return false;
  return rata < CAHAYA_MIN || rata > CAHAYA_MAKS;
}

/** Jarak Euclidean sederhana untuk tolak duplikat frame identik. */
export function jarakEmbedding(a, b) {
  if (!a || !b || a.length !== b.length || !a.length) return Infinity;
  let total = 0;
  const langkah = Math.max(1, Math.floor(a.length / 64));
  let n = 0;
  for (let i = 0; i < a.length; i += langkah) {
    const d = Number(a[i]) - Number(b[i]);
    total += d * d;
    n++;
  }
  if (!n) return Infinity;
  return Math.sqrt(total / n);
}

/**
 * Ubah bbox deteksi [x, y, w, h] (px video) menjadi kotak crop persegi
 * {sx, sy, s} yang aman (terjepit dalam frame, dipad dari tengah).
 * target hanya dokumentasi ukuran resize (default 224).
 */
export function kotakKeCrop(bbox, videoW, videoH) {
  const [bx, by, bw, bh] = (bbox || []).map(Number);
  if (![bx, by, bw, bh].every(Number.isFinite) || bw <= 0 || bh <= 0 || videoW <= 0 || videoH <= 0) {
    return null;
  }
  const x = Math.max(0, Math.min(bx, videoW));
  const y = Math.max(0, Math.min(by, videoH));
  const w = Math.max(0, Math.min(bw, videoW - x));
  const h = Math.max(0, Math.min(bh, videoH - y));
  if (w <= 0 || h <= 0) return null;
  const s = Math.max(w, h);
  const cx = x + w / 2;
  const cy = y + h / 2;
  let sx = Math.round(cx - s / 2);
  let sy = Math.round(cy - s / 2);
  sx = Math.max(0, Math.min(sx, videoW - s));
  sy = Math.max(0, Math.min(sy, videoH - s));
  return { sx, sy, s: Math.round(Math.min(s, videoW, videoH)) };
}

/**
 * Petakan bbox video ke koordinat tampilan untuk video object-cover.
 * rectW/rectH = ukuran elemen tampil, videoW/H = resolusi asli.
 */
export function petakanBox(bbox, rectW, rectH, videoW, videoH) {
  if (!bbox || rectW <= 0 || rectH <= 0 || videoW <= 0 || videoH <= 0) return null;
  const skala = Math.max(rectW / videoW, rectH / videoH);
  const offX = (rectW - videoW * skala) / 2;
  const offY = (rectH - videoH * skala) / 2;
  const [x, y, w, h] = bbox;
  return {
    left: Math.round(x * skala + offX),
    top: Math.round(y * skala + offY),
    width: Math.round(w * skala),
    height: Math.round(h * skala),
  };
}
