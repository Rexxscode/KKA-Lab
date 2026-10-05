/**
 * Modul deteksi objek real-time: TensorFlow.js COCO-SSD (pretrained).
 *
 * - 100% inference di browser, tanpa upload frame ke server.
 * - Tanpa training dari nol: memakai bobot pretrained COCO-SSD.
 * - Satu instance model dipakai ulang (load sekali).
 * - Inference dibatasi (~5-10 FPS) dan tidak pernah tumpang tindih.
 *
 * File ini di-bundle oleh Vite sebagai entry terpisah sehingga hanya
 * dimuat di halaman Modul 2 (bukan di seluruh aplikasi).
 */
import * as tf from '@tensorflow/tfjs';
import * as cocoSsd from '@tensorflow-models/coco-ssd';
import { filterPredictions, summarizeDetections, translateLabel } from './detect-utils.js';

export { filterPredictions, summarizeDetections, translateLabel };
export { COCO_LABELS_ID } from './detect-utils.js';

/**
 * Loader bawaan: siapkan backend TF lalu muat model ringan.
 * Dipisah agar bisa diganti mock saat pengujian.
 */
export async function defaultLoadModel() {
  await tf.ready();
  return cocoSsd.load({ base: 'lite_mobilenet_v2' });
}

const ERROR_MESSAGES = {
  'no-media':
    'Browser tidak mendukung akses kamera. Coba Chrome/Edge/Firefox terbaru di HP atau laptop.',
  denied:
    'Izin kamera ditolak. Klik ikon kamera di address bar browser, izinkan akses, lalu tekan Aktifkan Kamera lagi.',
  unavailable:
    'Kamera tidak tersedia. Pastikan tidak dipakai aplikasi lain (Zoom/Meet) lalu coba lagi.',
  'no-model':
    'Model AI belum termuat. Tunggu tulisan "Model siap" lalu coba lagi.',
  'no-video':
    'Video kamera belum menampilkan gambar. Tunggu beberapa detik lalu coba lagi.',
};

export class ObjectDetector {
  /**
   * @param {object} opts
   * @param {HTMLVideoElement} opts.video elemen video sumber frame
   * @param {() => Promise<object>} opts.loadModel fungsi pemuat model (bisa di-inject saat tes)
   * @param {number} opts.minScore ambang confidence 0..1
   * @param {number} opts.intervalMs jeda minimum antar inference (ms)
   */
  constructor({ video = null, loadModel = defaultLoadModel, minScore = 0.5, intervalMs = 150 } = {}) {
    this.video = video;
    this.loadModel = loadModel;
    this.minScore = minScore;
    this.intervalMs = intervalMs;
    this.model = null;
    this.running = false;
    this.busy = false;
    this.rafId = null;
    this.lastRun = 0;
    this.stream = null;
  }

  get ready() {
    return this.model !== null;
  }

  /** Muat model sekali; pemanggilan berikutnya langsung mengembalikan model. */
  async load() {
    if (this.model) return this.model;
    try {
      this.model = await this.loadModel();
    } catch (e) {
      throw new Error('model-failed: ' + String((e && e.message) || e));
    }
    return this.model;
  }

  /** Nyalakan kamera perangkat. Melempar Error berkode bila gagal. */
  async startCamera() {
    const media = navigator && navigator.mediaDevices;
    if (!media || typeof media.getUserMedia !== 'function') {
      throw new Error('no-media');
    }
    try {
      this.stream = await media.getUserMedia({
        video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } },
        audio: false,
      });
    } catch (e) {
      const nama = (e && e.name) || '';
      if (nama === 'NotAllowedError' || nama === 'SecurityError') throw new Error('denied');
      if (nama === 'NotFoundError' || nama === 'OverconstrainedError') throw new Error('unavailable');
      throw new Error('unavailable');
    }
    return this.stream;
  }

  /** Matikan semua track kamera agar indikator kamera benar-benar padam. */
  stopCamera() {
    if (this.stream) {
      this.stream.getTracks().forEach((t) => {
        try {
          t.stop();
        } catch (e) {
          /* abaikan */
        }
      });
      this.stream = null;
    }
    if (this.video) {
      try {
        this.video.srcObject = null;
      } catch (e) {
        /* abaikan */
      }
    }
  }

  /**
   * Satu kali inference terhadap frame video saat ini.
   * Mengembalikan array prediksi yang sudah disaring (bisa kosong).
   * Tidak pernah menjalankan dua inference bersamaan.
   */
  async detectOnce() {
    if (!this.model) throw new Error('no-model');
    if (!this.video || this.video.readyState < 2 || this.video.videoWidth === 0) {
      throw new Error('no-video');
    }
    if (this.busy) return null;
    this.busy = true;
    try {
      const mentah = await this.model.detect(this.video);
      return filterPredictions(mentah, this.minScore);
    } finally {
      this.busy = false;
    }
  }

  /** Loop deteksi via requestAnimationFrame dengan throttle intervalMs. */
  startLoop(onFrame) {
    this.stopLoop();
    this.running = true;
    this.lastRun = 0;
    const langkah = async (waktu) => {
      if (!this.running) return;
      try {
        if (waktu - this.lastRun >= this.intervalMs) {
          this.lastRun = waktu;
          const hasil = await this.detectOnce();
          if (hasil !== null && typeof onFrame === 'function') {
            onFrame(hasil);
          }
        }
      } catch (e) {
        if (typeof onFrame === 'function') {
          onFrame([], e);
        }
      }
      if (this.running) {
        this.rafId = requestAnimationFrame(langkah);
      }
    };
    this.rafId = requestAnimationFrame(langkah);
  }

  stopLoop() {
    this.running = false;
    if (this.rafId !== null) {
      cancelAnimationFrame(this.rafId);
      this.rafId = null;
    }
  }

  /** Bersihkan loop + kamera + model. Dipanggil saat reset/halaman ditutup. */
  dispose() {
    this.stopLoop();
    this.stopCamera();
    if (this.model && typeof this.model.dispose === 'function') {
      try {
        this.model.dispose();
      } catch (e) {
        /* abaikan */
      }
    }
    this.model = null;
  }
}

export function pesanError(error) {
  const kode = error instanceof Error ? error.message.split(':')[0] : String(error || '');
  if (kode === 'model-failed') {
    return 'Gagal memuat model AI (butuh internet sekali, ±5 MB). Periksa koneksi lalu muat ulang halaman.';
  }
  return ERROR_MESSAGES[kode] || 'Terjadi galat tak terduga. Muat ulang halaman lalu coba lagi.';
}

if (typeof window !== 'undefined') {
  window.KkaObjectDetector = {
    ObjectDetector,
    defaultLoadModel,
    filterPredictions,
    summarizeDetections,
    translateLabel,
    pesanError,
  };
}
