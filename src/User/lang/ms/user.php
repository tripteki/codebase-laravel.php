<?php

return [
    "common" => [
        "yes" => "Ya",
        "no" => "Tidak",
    ],

    "name_already_exists" => "Nama ini sudah didaftarkan",
    "email_already_exists" => "E-mel ini sudah didaftarkan",

    "auth" => [
        "password_mismatch" => "Kata laluan dan pengesahan kata laluan mesti sama",
        "password_confirmation_required" => "Sila taip semula kata laluan untuk pengesahan",
        "password_required" => "Sila isi kata laluan",
        "user_not_found" => "Pengguna tidak dijumpai",
    ],

    "export" => [
        "started" => "Eksport dimulakan",
        "completed" => "Eksport selesai. Fail anda sedia untuk dimuat turun.",
        "failed" => "Eksport gagal. Sila cuba lagi nanti.",
        "sheet_name" => "Pengguna",
        "column" => [
            "id" => "ID",
            "tenant" => "Tenant",
            "name" => "Nama",
            "full_name" => "Nama Penuh",
            "email" => "E-mel",
            "email_verified" => "E-mel Disahkan",
            "created_at" => "Dicipta Pada",
            "updated_at" => "Dikemas Kini Pada",
        ],
    ],

    "import" => [
        "started" => "Import dimulakan",
        "completed" => "Import selesai. :totalImported pengguna diimport, :totalSkipped pengguna dilangkau.",
        "failed" => "Import gagal. Sila semak format fail dan cuba lagi.",
        "no_data" => "Tiada data untuk disemak",
        "missing_columns" => "Lajur yang diperlukan tiada: :columns",
        "column" => [
            "id" => "ID",
            "tenant" => "Tenant",
            "name" => "Nama",
            "full_name" => "Nama Penuh",
            "email" => "E-mel",
            "password" => "Kata Laluan",
            "email_verified" => "E-mel Disahkan",
            "created_at" => "Dicipta Pada",
            "updated_at" => "Dikemas Kini Pada",
        ],
    ],

    "admin" => [
        "role_forbidden" => "Anda tidak boleh memberi peranan ini",
        "role_self_forbidden" => "Anda tidak boleh menukar peranan anda sendiri",
        "account_locked" => "Hanya superadmin boleh mengubah akaun ini",
    ],

    "roles" => [
        "superadmin" => "Superadmin",
        "admin" => "Admin",
        "user" => "Pengguna",
    ],

    "registration" => [
        "verified_title" => "Pendaftaran selesai",
        "verified_body" => "E-mel anda telah disahkan dan akaun anda sedia untuk digunakan.",
        "new_title" => "Ada pengguna baharu",
        "new_body" => "Pengguna baharu mendaftar: :full_name <:email> · Peranan: :role",
    ],

    "account_created" => [
        "title" => "Akaun dicipta",
        "body" => "Admin mencipta akaun anda dengan peranan :role.",
        "subject" => "Akaun anda sudah sedia",
        "line1" => "Admin mencipta akaun untuk anda dengan peranan :role. Log masuk menggunakan alamat e-mel ini.",
        "line2" => "E-mel ini tidak mengandungi kata laluan. Untuk menetapkan kata laluan sendiri, gunakan Lupa kata laluan.",
        "login" => "Log masuk",
        "forgot" => "Lupa kata laluan",
    ],

    "account_email_change" => [
        "subject" => "Sahkan e-mel baharu anda",
        "line1" => "Anda meminta untuk menggunakan alamat ini bagi akaun anda. Gunakan butang di bawah untuk mengesahkannya. Pautan ini sah selama 60 minit.",
        "line2" => "Sebelum disahkan, anda masih boleh log masuk dengan e-mel lama. Jika anda tidak meminta ini, abaikan e-mel ini.",
        "verify" => "Sahkan e-mel",
    ],

    "security" => [
        "password_subject" => "Kata laluan anda telah ditukar",
        "password_line_self" => "Kata laluan anda telah ditukar oleh anda sendiri pada :time.",
        "password_line_admin" => "Kata laluan anda telah ditukar oleh admin :admin pada :time.",
        "password_warning" => "Jika anda tidak memintanya, segera gunakan Lupa kata laluan dan hubungi admin.",
        "login" => "Log masuk",
        "email_old_subject" => "E-mel akaun anda telah ditukar",
        "email_old_line" => "E-mel akaun anda telah ditukar kepada :email.",
        "email_old_warning" => "Jika anda tidak memintanya, segera hubungi admin.",
        "email_new_subject" => "E-mel ini kini digunakan untuk log masuk",
        "email_new_line" => "E-mel ini kini digunakan untuk log masuk ke akaun anda.",
    ],

    "role_changed" => [
        "title" => "Peranan ditukar",
        "body" => "Peranan anda kini :role.",
    ],
];
