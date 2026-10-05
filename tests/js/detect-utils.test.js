/**
 * Uji logika detector TANPA webcam, TANPA model, TANPA browser.
 * Jalankan: npm run test:js  (node --test tests/js/)
 */
import { describe, it } from 'node:test';
import assert from 'node:assert/strict';
import {
  COCO_LABELS_ID,
  filterPredictions,
  summarizeDetections,
  translateLabel,
} from '../../resources/js/ai/detect-utils.js';

describe('translateLabel', () => {
  it('menerjemahkan kelas COCO umum ke bahasa Indonesia', () => {
    assert.equal(translateLabel('bottle'), 'Botol');
    assert.equal(translateLabel('cell phone'), 'HP');
    assert.equal(translateLabel('dining table'), 'Meja makan');
    assert.equal(translateLabel('teddy bear'), 'Boneka beruang');
  });

  it('tidak menebak label yang tidak dikenal', () => {
    assert.equal(translateLabel('botolku'), 'botolku');
    assert.equal(translateLabel(''), '');
    assert.equal(translateLabel(null), '');
  });
});

describe('filterPredictions', () => {
  const contoh = [
    { bbox: [10, 20, 100, 80], class: 'bottle', score: 0.91 },
    { bbox: [5, 5, 50, 50], class: 'book', score: 0.42 },
    { bbox: [0, 0, 30, 30], class: 'cup', score: 0.77 },
  ];

  it('membuang prediksi di bawah ambang dan mengurutkan menurun', () => {
    const hasil = filterPredictions(contoh, 0.5);
    assert.equal(hasil.length, 2);
    assert.equal(hasil[0].class, 'bottle');
    assert.equal(hasil[1].class, 'cup');
  });

  it('tahan terhadap data rusak dan input bukan-array', () => {
    assert.deepEqual(filterPredictions(null, 0.5), []);
    assert.deepEqual(filterPredictions('rusak', 0.5), []);
    const hasil = filterPredictions(
      [{ bbox: [1, 2], class: 'book', score: 0.9 }, null, { bbox: [1, 2, 3, 4], score: 0.9 }, 'x'],
      0.1
    );
    assert.deepEqual(hasil, []);
  });
});

describe('summarizeDetections', () => {
  it('menghitung objek dan mengambil teratas', () => {
    const hasil = summarizeDetections([
      { bbox: [1, 2, 3, 4], class: 'bottle', score: 0.9 },
      { bbox: [1, 2, 3, 4], class: 'book', score: 0.6 },
    ]);
    assert.equal(hasil.count, 2);
    assert.equal(hasil.top.class, 'bottle');
  });

  it('kosong bila tidak ada deteksi', () => {
    assert.deepEqual(summarizeDetections([]), { count: 0, top: null });
    assert.deepEqual(summarizeDetections(null), { count: 0, top: null });
  });
});

describe('COCO_LABELS_ID', () => {
  it('memuat 80 kelas COCO', () => {
    assert.equal(Object.keys(COCO_LABELS_ID).length, 80);
  });
});
