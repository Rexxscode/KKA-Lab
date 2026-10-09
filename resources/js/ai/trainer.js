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
import { bboxTengah, isCahayaBuruk, jarakEmbedding, putuskanHasil, rataTerang, deserializeDataset, jepitHiperparam, kotakKeCrop, serializeDataset, JARAK_DUPLIKAT_MIN, MIN_CONFIDENCE, MIN_SAMPEL_UJI } from './trainer-utils.js';

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
    this.kanvasCek = null;
    this.embeddingTerakhir = {};
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

  /** Ambil 1 contoh dari frame video untuk kelas ke-labelIdx (LEGACY full-frame, hindari). */
  tambahContoh(video, labelIdx) {
    // Arahkan ke crop tengah agar tidak melatih background (anti-ngasal).
    return this.tambahContohTengah(video, labelIdx);
  }

  /** Cek pencahayaan frame crop: null=ok, string=alasan tolak. */
  cekCahaya() {
    try {
      if (!this.kanvasCrop) return null;
      const ctx = this.kanvasCrop.getContext('2d', { willReadFrequently: true });
      const data = ctx.getImageData(0, 0, Math.min(this.kanvasCrop.width, 56), Math.min(this.kanvasCrop.height, 56));
      const rata = rataTerang(data.data);
      if (isCahayaBuruk(rata)) {
        return rata < 128 ? 'cahaya-gelap' : 'cahaya-terang';
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  /** Tolak duplikat: true bila embedding terlalu mirip contoh terakhir label itu. */
  isDuplikat(labelIdx, embeddingArr) {
    const terakhir = this.embeddingTerakhir[labelIdx];
    if (!terakhir) return false;
    try {
      return jarakEmbedding(terakhir, embeddingArr) < JARAK_DUPLIKAT_MIN;
    } catch (e) {
      return false;
    }
  }

  /** Ambil 1 contoh dari tengah frame (crop wajib 60%). Kembalikan kode status. */
  tambahContohTengah(video, labelIdx, rasio = 0.6) {
    if (!video || !video.videoWidth) return 'frame-kosong';
    const bbox = bboxTengah(video.videoWidth, video.videoHeight, rasio);
    if (!bbox) return 'frame-kosong';
    const hasil = this.tambahContohCrop(video, bbox, labelIdx);
    return hasil === true ? 'ok' : hasil;
  }

  /**
   * Ambil 1 contoh dari hasil crop bbox deteksi (selector objek).
   * Kembalikan 'ok' bila berhasil, kode alasan bila ditolak:
   * 'box-invalid' | 'cahaya-gelap' | 'cahaya-terang' | 'duplikat'.
   * Kompatibel lama: true === 'ok'.
   */
  tambahContohCrop(video, bbox, labelIdx, target = 224) {
    const potong = kotakKeCrop(bbox, video.videoWidth, video.videoHeight);
    if (!potong) return false;
    if (!this.kanvasCrop) this.kanvasCrop = document.createElement('canvas');
    this.kanvasCrop.width = target;
    this.kanvasCrop.height = target;
    const ctx = this.kanvasCrop.getContext('2d', { willReadFrequently: true });
    ctx.drawImage(video, potong.sx, potong.sy, potong.s, potong.s, 0, 0, target, target);
    const masalahCahaya = this.cekCahaya();
    if (masalahCahaya) {
      ctx.clearRect(0, 0, target, target);
      return masalahCahaya;
    }
    const piksel = tf.browser.fromPixels(this.kanvasCrop);
    const aktivasi = this.backbone.infer(piksel, true);
    try {
      const dataEmb = Array.from(aktivasi.dataSync());
      if (this.isDuplikat(labelIdx, dataEmb)) {
        return 'duplikat';
      }
      this.knn.addExample(aktivasi, labelIdx);
      this.embeddingTerakhir[labelIdx] = dataEmb.slice(0, 128);
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

  /** Klasifikasi crop tengah frame -> {label, confidence, tidakYakin, alasan} atau null. */
  async uji(video, { minConfidence = MIN_CONFIDENCE } = {}) {
    if (this.knn.getNumClasses() < 2) return null;
    const counts = this.knn.getClassExampleCount();
    for (const k of Object.keys(counts)) {
      if ((counts[k] || 0) < MIN_SAMPEL_UJI) {
        return { butuhData: true, label: null, confidence: 0, tidakYakin: true, alasan: `Contoh tiap label minimal ${MIN_SAMPEL_UJI} untuk uji.` };
      }
    }
    // Uji memakai crop tengah yang sama seperti saat latih (konsisten).
    const s = Math.min(video.videoWidth || 0, video.videoHeight || 0) * 0.6;
    let aktivasi;
    let piksel = null;
    if (s > 0 && this.kanvasCrop) {
      const bbox = [video.videoWidth / 2 - s / 2, video.videoHeight / 2 - s / 2, s, s];
      const potong = kotakKeCrop(bbox, video.videoWidth, video.videoHeight);
      if (potong) {
        if (!this.kanvasCrop) this.kanvasCrop = document.createElement('canvas');
        this.kanvasCrop.width = 224;
        this.kanvasCrop.height = 224;
        const ctx = this.kanvasCrop.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(video, potong.sx, potong.sy, potong.s, potong.s, 0, 0, 224, 224);
        piksel = tf.browser.fromPixels(this.kanvasCrop);
        aktivasi = this.backbone.infer(piksel, true);
        ctx.clearRect(0, 0, 224, 224);
      }
    }
    if (!aktivasi) {
      aktivasi = this.backbone.infer(video, true);
    }
    try {
      const mentah = await this.knn.predictClass(aktivasi);
      const conf = Number(mentah.confidences?.[mentah.label] ?? 0);
      const putus = putuskanHasil(mentah.label, conf, minConfidence);
      return { ...putus, confidences: mentah.confidences };
    } finally {
      aktivasi.dispose();
      if (piksel) piksel.dispose();
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
    this.embeddingTerakhir = {};
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
    this.embeddingTerakhir = {};
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
   * Split 80/20 train/val per label (anti overfit, anti akurasi semu).
   * Mengembalikan {loss, acc, valAcc, trainCount, valCount}. Lempar bila data kurang.
   */
  async latihLanjutan({ epochs = 10, batchSize = 5, units = 64, lr = 0.001, onEpoch } = {}) {
    const { epochs: ep, batchSize: bs } = jepitHiperparam({ epochs, batchSize });
    const mentah = this.knn.getClassifierDataset();
    const kelas = Object.keys(mentah);
    if (kelas.length < 2) throw new Error('butuh-2-kelas');
    const dim = mentah[kelas[0]].shape[1];
    // Kumpulkan per label lalu split acak 80/20 (min 1 val bila >=5).
    const perLabel = {};
    for (const k of kelas) {
      const t = mentah[k];
      const data = t.dataSync();
      const perContoh = data.length / t.shape[0];
      const semua = [];
      for (let i = 0; i < t.shape[0]; i++) {
        semua.push(Array.from(data.slice(i * perContoh, (i + 1) * perContoh)));
      }
      if (semua.length < 10) throw new Error('contoh-kurang');
      // Acak.
      for (let i = semua.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [semua[i], semua[j]] = [semua[j], semua[i]];
      }
      const nVal = Math.max(1, Math.floor(semua.length * 0.2));
      perLabel[k] = { train: semua.slice(0, semua.length - nVal), val: semua.slice(semua.length - nVal) };
    }
    const xsArr = [];
    const ysArr = [];
    const valXsArr = [];
    const valYsArr = [];
    kelas.forEach((k, ci) => {
      perLabel[k].train.forEach((row) => { xsArr.push(row); ysArr.push(ci); });
      perLabel[k].val.forEach((row) => { valXsArr.push(row); valYsArr.push(ci); });
    });
    if (xsArr.length < kelas.length * 5) throw new Error('contoh-kurang');

    this.buangHead();
    const xs = tf.tensor2d(xsArr, [xsArr.length, dim]);
    const ys = tf.oneHot(tf.tensor1d(ysArr, 'int32'), kelas.length);
    const head = tf.sequential();
    head.add(tf.layers.dense({ inputShape: [dim], units: Math.max(16, Math.min(units, 256)), activation: 'relu' }));
    head.add(tf.layers.dropout({ rate: 0.2 }));
    head.add(tf.layers.dense({ units: kelas.length, activation: 'softmax' }));
    head.compile({ optimizer: tf.train.adam(lr), loss: 'categoricalCrossentropy', metrics: ['accuracy'] });
    this.head = head;
    this.headInfo = { kelas, dim, units, epochs: ep };
    let valXs = null;
    let valYs = null;
    try {
      if (valXsArr.length) {
        valXs = tf.tensor2d(valXsArr, [valXsArr.length, dim]);
        valYs = tf.oneHot(tf.tensor1d(valYsArr, 'int32'), kelas.length);
      }
      const riwayat = await head.fit(xs, ys, {
        shuffle: true,
        batchSize: Math.min(bs, xsArr.length),
        epochs: ep,
        validationData: valXs && valYs ? [valXs, valYs] : undefined,
        callbacks: {
          onEpochEnd: (epoch, logs) => {
            if (onEpoch) onEpoch(epoch, logs || {});
          },
        },
      });
      const loss = riwayat.history.loss || [];
      const acc = riwayat.history.acc || riwayat.history.accuracy || [];
      const valAccArr = riwayat.history.val_acc || riwayat.history.val_accuracy || [];
      // Hitung val-acc manual bila history tak menyediakan (konsisten antar versi tfjs).
      let valAcc = valAccArr.length ? valAccArr[valAccArr.length - 1] : null;
      if (valAcc === null && valXs) {
        try {
          const pred = head.predict(valXs);
          const predIdx = Array.from(pred.argMax(1).dataSync());
          pred.dispose();
          let benar = 0;
          predIdx.forEach((p, i) => { if (p === valYsArr[i]) benar++; });
          valAcc = valYsArr.length ? benar / valYsArr.length : null;
        } catch (e) { /* abaikan, tetap lapor train-acc */ }
      }
      this.headInfo.hasil = {
        loss: loss.length ? loss[loss.length - 1] : null,
        acc: acc.length ? acc[acc.length - 1] : null,
        valAcc,
        trainCount: xsArr.length,
        valCount: valXsArr.length,
      };
      return this.headInfo.hasil;
    } finally {
      xs.dispose();
      ys.dispose();
      if (valXs) valXs.dispose();
      if (valYs) valYs.dispose();
    }
  }

  hentikanLatih() {
    if (this.head) this.head.stopTraining = true;
  }

  get adaHead() {
    return this.head !== null && this.head !== undefined;
  }

  /** Prediksi memakai head hasil latih lanjutan -> {label, confidence, tidakYakin} atau null. */
  async ujiHead(video, { minConfidence = MIN_CONFIDENCE } = {}) {
    if (!this.head || !this.headInfo) return null;
    const aktivasi = this.backbone.infer(video, true);
    try {
      const keluar = this.head.predict(aktivasi);
      const skor = Array.from(keluar.dataSync());
      keluar.dispose();
      let idx = 0;
      skor.forEach((s, i) => { if (s > skor[idx]) idx = i; });
      const putus = putuskanHasil(this.headInfo.kelas[idx], skor[idx], minConfidence);
      return putus;
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
