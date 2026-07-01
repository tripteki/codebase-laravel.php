<?php

return [
    "common" => [
        "yes" => "Ya",
        "no" => "Tidak",
    ],

    "name_already_exists" => "Nama sudah terdaftar",
    "email_already_exists" => "Email sudah terdaftar",

    "auth" => [
        "password_mismatch" => "Password dan konfirmasi password harus sama",
        "password_confirmation_required" => "Konfirmasi password diperlukan ketika password diberikan",
        "password_required" => "Password diperlukan ketika konfirmasi password diberikan",
        "user_not_found" => "Pengguna tidak ditemukan",
    ],

    "export" => [
        "started" => "Export dimulai",
        "completed" => "Export berhasil diselesaikan. File Anda siap diunduh.",
        "failed" => "Export gagal. Silakan coba lagi nanti.",
        "sheet_name" => "Pengguna",
        "column" => [
            "id" => "ID",
            "tenant" => "Tenant",
            "name" => "Nama",
            "full_name" => "Nama Lengkap",
            "email" => "Email",
            "email_verified" => "Email Terverifikasi",
            "created_at" => "Dibuat Pada",
            "updated_at" => "Diperbarui Pada",
        ],
    ],

    "import" => [
        "started" => "Import dimulai",
        "completed" => "Import berhasil diselesaikan. :totalImported pengguna diimpor, :totalSkipped pengguna dilewati.",
        "failed" => "Import gagal. Silakan periksa format file dan coba lagi.",
        "no_data" => "Tidak ada data untuk divalidasi",
        "missing_columns" => "Kolom yang diperlukan tidak ada: :columns",
        "column" => [
            "id" => "ID",
            "tenant" => "Tenant",
            "name" => "Nama",
            "full_name" => "Nama Lengkap",
            "email" => "Email",
            "password" => "Kata Sandi",
            "email_verified" => "Email Terverifikasi",
            "created_at" => "Dibuat Pada",
            "updated_at" => "Diperbarui Pada",
        ],
    ],

    "admin" => [
        "role_forbidden" => "Anda tidak dapat memberi role ini",
        "role_self_forbidden" => "Anda tidak dapat mengubah role Anda sendiri",
        "account_locked" => "Hanya superadmin yang dapat mengubah akun ini",
    ],

    "roles" => [
        "superadmin" => "Superadmin",
        "admin" => "Admin",
        "user" => "Pengguna",
    ],

    "registration" => [
        "verified_title" => "Pendaftaran selesai",
        "verified_body" => "Email Anda sudah diverifikasi dan akun siap digunakan.",
        "new_title" => "Ada pengguna baru",
        "new_body" => "Pengguna baru mendaftar: :full_name <:email> · Role: :role",
    ],

    "account_created" => [
        "title" => "Akun dibuat",
        "body" => "Admin membuat akun Anda dengan role :role.",
        "subject" => "Akun Anda sudah siap",
        "line1" => "Admin membuat akun untuk Anda dengan role :role. Masuk menggunakan alamat email ini.",
        "line2" => "Email ini tidak berisi kata sandi. Gunakan Lupa kata sandi untuk mengatur sendiri.",
        "login" => "Masuk",
        "forgot" => "Lupa kata sandi",
    ],

    "account_email_change" => [
        "subject" => "Konfirmasi email baru Anda",
        "line1" => "Anda meminta memakai alamat ini untuk akun Anda. Tekan tombol di bawah untuk mengonfirmasinya. Tautan ini berlaku selama 60 menit.",
        "line2" => "Sebelum dikonfirmasi, Anda tetap bisa masuk dengan email lama. Jika Anda tidak meminta ini, abaikan email ini.",
        "verify" => "Konfirmasi email",
    ],

    "security" => [
        "password_subject" => "Kata sandi Anda telah diubah",
        "password_line_self" => "Anda mengubah kata sandi pada :time.",
        "password_line_admin" => "Admin :admin mengubah kata sandi Anda pada :time.",
        "password_warning" => "Jika Anda tidak memintanya, gunakan Lupa kata sandi dan hubungi admin.",
        "login" => "Masuk",
        "email_old_subject" => "Email akun Anda telah diubah",
        "email_old_line" => "Email akun Anda diubah menjadi :email.",
        "email_old_warning" => "Jika Anda tidak memintanya, hubungi admin.",
        "email_new_subject" => "Email ini sekarang dipakai untuk masuk",
        "email_new_line" => "Email ini sekarang dipakai untuk masuk ke akun Anda.",
    ],

    "role_changed" => [
        "title" => "Role diubah",
        "body" => "Role Anda sekarang :role.",
    ],
];
