/**
 * Logika murni halaman Latih Model (tanpa TensorFlow, tanpa DOM).
 * Dipakai resources/js/ai/trainer.js dan diuji via node --test.
 */

export const MIN_LABEL = 2;
export const MAX_LABEL = 5;
export const MIN_SAMPEL = 20;
export const STREAK_SYARAT = 5;
export const MAKS_LABEL_PANJANG = 30;

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

/** Estimasi ukuran dataset (byte) untuk validasi batas upload. */
export function ukuranDataset(data) {
  let n = 0;
  for (const label of data.labels || []) {
    for (const b64 of (data.contoh || {})[label] || []) n += Math.ceil((b64.length * 3) / 4);
  }
  return n;
}
