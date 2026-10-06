/**
 * Latih model KNN di browser (MobileNet embedding + knn-classifier).
 *
 * - 100% di browser: contoh, latih, dan uji tanpa upload.
 * - Draf dataset tersimpan di IndexedDB per siswa.
 * - Di-bundle Vite sebagai entry terpisah (hanya halaman Latih Model).
 */
import * as tf from '@tensorflow/tfjs';
import * as mobilenet from '@tensorflow-models/mobilenet';
import * as knnClassifier from '@tensorflow-models/knn-classifier';
import * as cocoSsd from '@tensorflow-models/coco-ssd';
import { deserializeDataset, jepitHiperparam, kotakKeCrop, serializeDataset } from './trainer-utils.js';

export const BACKBONE_NAMA = 'mobilenet-v1-0.25';

export async function defaultLoadBackbone() {
  await tf.ready();
  // Alpha 0.25: bobot <1MB (vs 16MB alpha 1.0) — muat cepat & ramah HP.
  return mobilenet.load({ version: 1, alpha: 0.25 });
}

export async function defaultLoadDetektor() {
  await tf.ready();
  return cocoSsd.load({ base: 'lite_mobilenet_v2' });
}

function bukaDB() {
  return new Promise((resolve, reject) => {
    if (typeof indexedDB === 'undefined') {
      reject(new Error('no-idb'));
      return;
    }
    const req = indexedDB.open('kka-latih', 1);
    req.onupgradeneeded = () => req.result.createObjectStore('draf');
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

async function tulisDraf(kunci, nilai) {
  const db = await bukaDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction('draf', 'readwrite');
    tx.objectStore('draf').put(nilai, kunci);
    tx.oncomplete = () => resolve();
    tx.onerror = () => reject(tx.error);
  });
}

async function bacaDraf(kunci) {
  const db = await bukaDB();
  return new Promise((resolve, reject) => {
    const req = db.transaction('draf', 'readonly').objectStore('draf').get(kunci);
    req.onsuccess = () => resolve(req.result || null);
    req.onerror = () => reject(req.error);
  });
}

export class Pelatih {
  constructor({ loadBackbone = defaultLoadBackbone, loadDetektor = defaultLoadDetektor } = {}) {
    this.loadBackbone = loadBackbone;
    this.loadDetektor = loadDetektor;
    this.backbone = null;
    this.detektor = null;
    this.knn = knnClassifier.create();
    this.stream = null;
    this.kanvasCrop = null;
  }

  get siap() {
    return this.backbone !== null;
  }

  async load() {
    if (this.backbone) return this.backbone;
    try {
      this.backbone = await this.loadBackbone();
    } catch (e) {
      throw new Error('model-failed: ' + String((e && e.message) || e));
    }
    return this.backbone;
  }

  async mulaiKamera(video) {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    await video.play();
    this.stream = stream;
  }

  hentikanKamera(video) {
    if (this.stream) this.stream.getTracks().forEach((t) => t.stop());
    if (video) video.srcObject = null;
    this.stream = null;
  }

  /** Ambil 1 contoh dari frame video untuk kelas ke-labelIdx. */
  tambahContoh(video, labelIdx) {
    const aktivasi = this.backbone.infer(video, true);
    try {
      this.knn.addExample(aktivasi, labelIdx);
    } finally {
      aktivasi.dispose();
    }
  }

  /**
   * Ambil 1 contoh dari hasil crop bbox deteksi (selector objek).
   * Mengembalikan true bila crop valid, false bila box di luar frame.
   */
  tambahContohCrop(video, bbox, labelIdx, target = 224) {
    const potong = kotakKeCrop(bbox, video.videoWidth, video.videoHeight);
    if (!potong) return false;
    if (!this.kanvasCrop) this.kanvasCrop = document.createElement('canvas');
    this.kanvasCrop.width = target;
    this.kanvasCrop.height = target;
    const ctx = this.kanvasCrop.getContext('2d', { willReadFrequently: true });
    ctx.drawImage(video, potong.sx, potong.sy, potong.s, potong.s, 0, 0, target, target);
    const piksel = tf.browser.fromPixels(this.kanvasCrop);
    const aktivasi = this.backbone.infer(piksel, true);
    try {
      this.knn.addExample(aktivasi, labelIdx);
    } finally {
      piksel.dispose();
      aktivasi.dispose();
      ctx.clearRect(0, 0, target, target);
    }
    return true;
  }

  /** Muat detektor objek (malas: hanya saat user menekan Pindai). */
  async loadPendeteksi() {
    if (this.detektor) return this.detektor;
    this.detektor = await this.loadDetektor();
    return this.detektor;
  }

  /** Deteksi objek di frame saat ini -> [{bbox, class, score}] (maks 10, min 0.5). */
  async pindai(video) {
    const detektor = await this.loadPendeteksi();
    const hasil = await detektor.detect(video, 10, 0.5);
    return (hasil || []).map((d) => ({ bbox: d.bbox, class: d.class, score: d.score }));
  }

  /** Klasifikasi frame saat ini -> {label, keyakinan} atau null. */
  async uji(video) {
    if (this.knn.getNumClasses() === 0) return null;
    const aktivasi = this.backbone.infer(video, true);
    try {
      return await this.knn.predictClass(aktivasi);
    } finally {
      aktivasi.dispose();
    }
  }

  jumlahPerKelas() {
    const counts = this.knn.getClassExampleCount();
    const keluar = {};
    for (const [k, v] of Object.entries(counts)) keluar[k] = v;
    return keluar;
  }

  getDataset() {
    const mentah = this.knn.getClassifierDataset();
    const keluar = {};
    for (const [k, tensor] of Object.entries(mentah)) {
      keluar[k] = [];
      const data = tensor.dataSync();
      const perContoh = data.length / tensor.shape[0];
      for (let i = 0; i < tensor.shape[0]; i++) {
        keluar[k].push(Float32Array.from(data.slice(i * perContoh, (i + 1) * perContoh)));
      }
    }
    return keluar;
  }

  setDataset(dataset) {
    this.knn.clearAllClasses();
    this.buangHead();
    const tensors = {};
    for (const [k, arrs] of Object.entries(dataset || {})) {
      if (!arrs.length) continue;
      const dim = arrs[0].length;
      const flat = new Float32Array(arrs.length * dim);
      arrs.forEach((a, i) => flat.set(a, i * dim));
      tensors[k] = tf.tensor2d(Array.from(flat), [arrs.length, dim]);
    }
    if (Object.keys(tensors).length) this.knn.setClassifierDataset(tensors);
  }

  serialize(labels) {
    const mentah = this.getDataset();
    const bernama = {};
    for (const [idx, nama] of Object.entries(labels || {})) {
      if (mentah[idx]) bernama[nama] = mentah[idx];
    }
    const s = serializeDataset(bernama, BACKBONE_NAMA);
    return s;
  }

  restore(data, labels) {
    const bernama = deserializeDataset(data);
    const berindeks = {};
    const daftar = Array.isArray(labels) ? labels : Object.values(labels || {});
    daftar.forEach((nama, i) => {
      if (bernama[nama]) berindeks[i] = bernama[nama];
    });
    this.setDataset(berindeks);
  }

  reset() {
    this.knn.clearAllClasses();
    this.buangHead();
  }

  /** Buang head transfer-learning bila ada (dipakai ulang tiap latih baru). */
  buangHead() {
    if (this.head) {
      try { this.head.dispose(); } catch (e) { /* abaikan */ }
      this.head = null;
      this.headInfo = null;
    }
  }

  /**
   * Latih Lanjutan (transfer learning beneran): head dense kecil di atas
   * embedding MobileNet beku, dilatih dari contoh KNN yang terkumpul.
   * Mengembalikan {loss, acc} epoch terakhir. Lempar bila data kurang.
   */
  async latihLanjutan({ epochs = 10, batchSize = 5, units = 64, lr = 0.001, onEpoch } = {}) {
    const { epochs: ep, batchSize: bs } = jepitHiperparam({ epochs, batchSize });
    const mentah = this.knn.getClassifierDataset();
    const kelas = Object.keys(mentah);
    if (kelas.length < 2) throw new Error('butuh-2-kelas');
    const dim = mentah[kelas[0]].shape[1];
    const xsArr = [];
    const ysArr = [];
    kelas.forEach((k, ci) => {
      const t = mentah[k];
      const data = t.dataSync();
      const perContoh = data.length / t.shape[0];
      for (let i = 0; i < t.shape[0]; i++) {
        xsArr.push(Array.from(data.slice(i * perContoh, (i + 1) * perContoh)));
        ysArr.push(ci);
      }
    });
    if (xsArr.length < kelas.length * 5) throw new Error('contoh-kurang');

    this.buangHead();
    const xs = tf.tensor2d(xsArr, [xsArr.length, dim]);
    const ys = tf.oneHot(tf.tensor1d(ysArr, 'int32'), kelas.length);
    const head = tf.sequential();
    head.add(tf.layers.dense({ inputShape: [dim], units: Math.max(16, Math.min(units, 256)), activation: 'relu' }));
    head.add(tf.layers.dense({ units: kelas.length, activation: 'softmax' }));
    head.compile({ optimizer: tf.train.adam(lr), loss: 'categoricalCrossentropy', metrics: ['accuracy'] });
    this.head = head;
    this.headInfo = { kelas, dim, units, epochs: ep };
    try {
      const riwayat = await head.fit(xs, ys, {
        shuffle: true,
        batchSize: Math.min(bs, xsArr.length),
        epochs: ep,
        callbacks: {
          onEpochEnd: (epoch, logs) => {
            if (onEpoch) onEpoch(epoch, logs || {});
          },
        },
      });
      const loss = riwayat.history.loss || [];
      const acc = riwayat.history.acc || riwayat.history.accuracy || [];
      this.headInfo.hasil = {
        loss: loss.length ? loss[loss.length - 1] : null,
        acc: acc.length ? acc[acc.length - 1] : null,
      };
      return this.headInfo.hasil;
    } finally {
      xs.dispose();
      ys.dispose();
    }
  }

  hentikanLatih() {
    if (this.head) this.head.stopTraining = true;
  }

  get adaHead() {
    return this.head !== null && this.head !== undefined;
  }

  /** Prediksi memakai head hasil latih lanjutan -> {label, confidence} atau null. */
  async ujiHead(video) {
    if (!this.head || !this.headInfo) return null;
    const aktivasi = this.backbone.infer(video, true);
    try {
      const keluar = this.head.predict(aktivasi);
      const skor = Array.from(keluar.dataSync());
      keluar.dispose();
      let idx = 0;
      skor.forEach((s, i) => { if (s > skor[idx]) idx = i; });
      return { label: this.headInfo.kelas[idx], confidence: skor[idx] };
    } finally {
      aktivasi.dispose();
    }
  }

  /** Serialisasi bobot head -> {units, epochs, tensors:[{shape, dtype, data:b64}]} atau null. */
  serializeBobot() {
    if (!this.head) return null;
    const tensors = this.head.getWeights().map((t) => {
      const data = t.dataSync();
      const bytes = new Uint8Array(data.buffer, data.byteOffset, data.byteLength);
      let b64;
      if (typeof Buffer !== 'undefined') {
        b64 = Buffer.from(bytes).toString('base64');
      } else {
        let s = '';
        for (let i = 0; i < bytes.length; i += 8192) {
          s += String.fromCharCode.apply(null, bytes.subarray(i, i + 8192));
        }
        // eslint-disable-next-line no-undef
        b64 = btoa(s);
      }
      return { shape: t.shape, dtype: t.dtype, data: b64 };
    });
    return {
      units: this.headInfo?.units ?? null,
      kelas: this.headInfo?.kelas ?? [],
      tensors,
    };
  }

  /** Unduh bobot head (model.json + weights.bin) sebagai artefak. */
  async unduhBobot(nama = 'model-latihanku') {
    if (!this.head) throw new Error('belum-latih');
    await this.head.save('downloads://' + String(nama).replace(/[^a-z0-9-_]+/gi, '-').slice(0, 40));
  }

  dispose() {
    this.hentikanKamera(null);
    this.knn.dispose();
    if (this.backbone && this.backbone.dispose) this.backbone.dispose();
    if (this.detektor && this.detektor.dispose) this.detektor.dispose();
    this.backbone = null;
    this.detektor = null;
  }
}

export { tulisDraf, bacaDraf };
export * from './trainer-utils.js';

if (typeof window !== 'undefined') {
  window.KkaTrainer = { Pelatih, tulisDraf, bacaDraf };
}
