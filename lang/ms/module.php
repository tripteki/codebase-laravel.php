<?php

/*
|--------------------------------------------------------------------------
| Baris Bahasa Modul
|--------------------------------------------------------------------------
|
| Baris bahasa berikut digunakan untuk berbagai teks di seluruh
| aplikasi. Baris ini dipaparkan kepada pengguna untuk memberikan
| maklum balas tentang proses yang berbeza seperti menavigasi melalui bahagian,
| berinteraksi dengan butang, dan menghantar borang.
|
*/

return [

    ...(require __DIR__."/../../src/V1/Api/User/Langs/id.php"),
    ...(require __DIR__."/../../src/V1/Api/Acl/Langs/id/role.php"),
    ...(require __DIR__."/../../src/V1/Api/Acl/Langs/id/permission.php"),
    ...(require __DIR__."/../../src/V1/Api/Log/Langs/id/activity.php"),

];
