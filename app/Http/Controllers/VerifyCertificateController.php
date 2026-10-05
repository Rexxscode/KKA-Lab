<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class VerifyCertificateController extends Controller
{
    public function show(string $code)
    {
        $certificate = Certificate::with('user')->where('code', $code)->firstOrFail();

        return view('sertifikat-verifikasi', compact('certificate'));
    }
}
