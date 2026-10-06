/**
 * Uji logika latih-model TANPA webcam, TANPA TensorFlow, TANPA browser.
 * Jalankan: npm run test:js  (node --test tests/js/)
 */
import { describe, it } from 'node:test';
import assert from 'node:assert/strict';
import {
  MIN_SAMPEL,
  STREAK_SYARAT,
  cekSyarat,
  deserializeDataset,
  serializeDataset,
  ukuranDataset,
  validasiLabel,
} from '../../resources/js/ai/trainer-utils.js';

describe('validasiLabel', () => {
  it('menolak kurang dari 2 label', () => {
    assert.equal(validasiLabel(['Botol']).ok, false);
    assert.equal(validasiLabel([]).ok, false);
  });

  it('menerima 2-5 label unik', () => {
    const r = validasiLabel(['Botol', 'Buku', ' HP ']);
    assert.equal(r.ok, true);
    assert.deepEqual(r.labels, ['Botol', 'Buku', 'HP']);
  });

  it('menolak duplikat dan label terlalu panjang', () => {
    assert.equal(validasiLabel(['Botol', 'botol']).ok, false);
    assert.equal(validasiLabel(['Botol', 'x'.repeat(31)]).ok, false);
    assert.equal(validasiLabel(['a', 'b', 'c', 'd', 'e', 'f']).ok, false);
  });
});

describe('cekSyarat', () => {
  it('lolos bila tiap label cukup contoh dan streak cukup', () => {
    const r = cekSyarat({ Botol: 25, Buku: 20 }, 5);
    assert.equal(r.ok, true);
  });

  it('menyebut alasan yang kurang', () => {
    const r = cekSyarat({ Botol: 25, Buku: 3 }, 2);
    assert.equal(r.ok, false);
    assert.ok(r.alasan.some((a) => a.includes('Buku')));
    assert.ok(r.alasan.some((a) => a.includes('2/5')));
  });
});

describe('serializeDataset', () => {
  it('bolak-balik tanpa merusak nilai float', () => {
    const dataset = {
      Botol: [new Float32Array([0.5, -1.25, 3.0]), new Float32Array([0, 0, 0])],
      Buku: [new Float32Array([9.5, 8.5, 7.5])],
    };
    const s = serializeDataset(dataset);
    assert.deepEqual(s.labels, ['Botol', 'Buku']);
    assert.equal(s.contoh.Botol.length, 2);
    const balik = deserializeDataset(JSON.parse(JSON.stringify(s)));
    assert.deepEqual(Array.from(balik.Buku[0]), [9.5, 8.5, 7.5]);
    assert.deepEqual(Array.from(balik.Botol[0]), [0.5, -1.25, 3.0]);
    assert.ok(ukuranDataset(s) > 0);
    assert.ok(ukuranDataset(s) < 2 * 1024 * 1024);
  });

  it('tahan data rusak', () => {
    assert.deepEqual(deserializeDataset(null), {});
    assert.deepEqual(deserializeDataset({}), {});
  });
});

describe('konstanta ambang', () => {
  it('sesuai kontrak publish', () => {
    assert.equal(MIN_SAMPEL, 20);
    assert.equal(STREAK_SYARAT, 5);
  });
});
