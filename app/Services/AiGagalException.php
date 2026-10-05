<?php

namespace App\Services;

/**
 * Kegagalan memanggil AI (kunci belum diisi, respons kosong, jaringan
 * putus). Dibedakan dari balasan asli agar controller tidak menyimpan
 * teks galat sebagai riwayat chat dan tidak memakan kuota harian.
 */
class AiGagalException extends \RuntimeException
{
}
