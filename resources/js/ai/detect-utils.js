/**
 * Utilitas murni (tanpa dependency) untuk modul deteksi objek.
 *
 * Dipisah dari object-detector.js agar bisa diuji tanpa webcam,
 * tanpa TensorFlow, dan tanpa browser (node --test).
 */

/**
 * 80 kelas COCO (2017) dalam bahasa Indonesia.
 * Kunci = label asli model (Inggris), nilai = label tampil.
 */
export const COCO_LABELS_ID = {
  person: 'Orang',
  bicycle: 'Sepeda',
  car: 'Mobil',
  motorcycle: 'Motor',
  airplane: 'Pesawat',
  bus: 'Bus',
  train: 'Kereta',
  truck: 'Truk',
  boat: 'Perahu',
  'traffic light': 'Lampu lalu lintas',
  'fire hydrant': 'Hidran',
  'stop sign': 'Rambu stop',
  'parking meter': 'Meter parkir',
  bench: 'Bangku',
  bird: 'Burung',
  cat: 'Kucing',
  dog: 'Anjing',
  horse: 'Kuda',
  sheep: 'Domba',
  cow: 'Sapi',
  elephant: 'Gajah',
  bear: 'Beruang',
  zebra: 'Zebra',
  giraffe: 'Jerapah',
  backpack: 'Ransel',
  umbrella: 'Payung',
  handbag: 'Tas tangan',
  tie: 'Dasi',
  suitcase: 'Koper',
  frisbee: 'Frisbee',
  skis: 'Ski',
  snowboard: 'Snowboard',
  'sports ball': 'Bola olahraga',
  kite: 'Layang-layang',
  'baseball bat': 'Pemukul bisbol',
  'baseball glove': 'Sarung bisbol',
  skateboard: 'Skateboard',
  surfboard: 'Papan selancar',
  'tennis racket': 'Raket tenis',
  bottle: 'Botol',
  'wine glass': 'Gelas anggur',
  cup: 'Cangkir',
  fork: 'Garpu',
  knife: 'Pisau',
  spoon: 'Sendok',
  bowl: 'Mangkuk',
  banana: 'Pisang',
  apple: 'Apel',
  sandwich: 'Roti lapis',
  orange: 'Jeruk',
  broccoli: 'Brokoli',
  carrot: 'Wortel',
  'hot dog': 'Hot dog',
  pizza: 'Pizza',
  donut: 'Donat',
  cake: 'Kue',
  chair: 'Kursi',
  couch: 'Sofa',
  'potted plant': 'Tanaman pot',
  bed: 'Kasur',
  'dining table': 'Meja makan',
  toilet: 'Toilet',
  tv: 'TV',
  laptop: 'Laptop',
  mouse: 'Tetikus',
  remote: 'Remot',
  keyboard: 'Kibor',
  'cell phone': 'HP',
  microwave: 'Microwave',
  oven: 'Oven',
  toaster: 'Pemanggang roti',
  sink: 'Wastafel',
  refrigerator: 'Kulkas',
  book: 'Buku',
  clock: 'Jam',
  vase: 'Vas',
  scissors: 'Gunting',
  'teddy bear': 'Boneka beruang',
  'hair drier': 'Pengering rambut',
  toothbrush: 'Sikat gigi',
};

/**
 * Terjemahkan label COCO ke bahasa Indonesia.
 * Label yang tidak dikenal dikembalikan apa adanya (bukan ditebak).
 */
export function translateLabel(name) {
  if (typeof name !== 'string') return '';
  const key = name.trim().toLowerCase();
  return COCO_LABELS_ID[key] || name.trim();
}

/**
 * Normalisasi + saring + urutkan prediksi model.
 * Tahan terhadap data rusak: entri tanpa bbox/score yang valid dibuang.
 *
 * @param {Array} predictions keluaran mentah model.detect()
 * @param {number} minScore ambang 0..1
 * @returns {Array<{bbox: number[], class: string, score: number}>}
 */
export function filterPredictions(predictions, minScore = 0.5) {
  if (!Array.isArray(predictions)) return [];
  const ambang = Number.isFinite(minScore) ? minScore : 0.5;

  return predictions
    .map((p) => {
      if (!p || typeof p !== 'object') return null;
      const bbox = Array.isArray(p.bbox) ? p.bbox.map(Number) : [];
      const score = Number(p.score);
      const cls = typeof p.class === 'string' ? p.class : '';
      if (bbox.length !== 4 || bbox.some((n) => !Number.isFinite(n))) return null;
      if (!cls || !Number.isFinite(score)) return null;
      return { bbox, class: cls, score };
    })
    .filter((p) => p !== null && p.score >= ambang)
    .sort((a, b) => b.score - a.score);
}

/**
 * Ringkasan cepat untuk UI: jumlah objek + prediksi teratas.
 */
export function summarizeDetections(detections) {
  const list = Array.isArray(detections) ? detections : [];
  return { count: list.length, top: list.length > 0 ? list[0] : null };
}
