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
import { deserializeDataset, serializeDataset } from './trainer-utils.js';

export async function defaultLoadBackbone() {
  await tf.ready();
  return mobilenet.load();
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
  constructor({ loadBackbone = defaultLoadBackbone } = {}) {
    this.loadBackbone = loadBackbone;
    this.backbone = null;
    this.knn = knnClassifier.create();
    this.stream = null;
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
    return serializeDataset(bernama);
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
  }

  dispose() {
    this.hentikanKamera(null);
    this.knn.dispose();
    if (this.backbone && this.backbone.dispose) this.backbone.dispose();
    this.backbone = null;
  }
}

export { tulisDraf, bacaDraf };
export * from './trainer-utils.js';

if (typeof window !== 'undefined') {
  window.KkaTrainer = { Pelatih, tulisDraf, bacaDraf };
}
