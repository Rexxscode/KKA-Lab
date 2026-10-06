<?php

return [
    // Email akun super admin penandatangan sertifikat.
    // Fallback otomatis ke admin tertua bila akun ini tidak ada.
    'super_admin_email' => env('SUPER_ADMIN_EMAIL', 'admin@kkalab.sch.id'),
];
