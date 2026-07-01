<?php

return [
    "common" => [
        "yes" => "Yes",
        "no" => "No",
    ],

    "name_already_exists" => "Name already exists",
    "email_already_exists" => "Email already exists",

    "auth" => [
        "password_mismatch" => "Password and password confirmation must match",
        "password_confirmation_required" => "Password confirmation is required when password is provided",
        "password_required" => "Password is required when password confirmation is provided",
        "user_not_found" => "User not found",
    ],

    "export" => [
        "started" => "Export started",
        "completed" => "Export completed successfully. Your file is ready for download.",
        "failed" => "Export failed. Please try again later.",
        "sheet_name" => "Users",
        "column" => [
            "id" => "ID",
            "tenant" => "Tenant",
            "name" => "Name",
            "full_name" => "Full Name",
            "email" => "Email",
            "email_verified" => "Email Verified",
            "created_at" => "Created At",
            "updated_at" => "Updated At",
        ],
    ],

    "import" => [
        "started" => "Import started",
        "completed" => "Import completed successfully. :totalImported users imported, :totalSkipped users skipped.",
        "failed" => "Import failed. Please check the file format and try again.",
        "no_data" => "No data to validate",
        "missing_columns" => "Missing required columns: :columns",
        "column" => [
            "id" => "ID",
            "tenant" => "Tenant",
            "name" => "Name",
            "full_name" => "Full Name",
            "email" => "Email",
            "password" => "Password",
            "email_verified" => "Email Verified",
            "created_at" => "Created At",
            "updated_at" => "Updated At",
        ],
    ],

    "admin" => [
        "role_forbidden" => "You cannot assign this role",
        "role_self_forbidden" => "You cannot change your own role",
        "account_locked" => "Only a superadmin can change this account",
    ],

    "roles" => [
        "superadmin" => "Superadmin",
        "admin" => "Admin",
        "user" => "User",
    ],

    "registration" => [
        "verified_title" => "Registration complete",
        "verified_body" => "Your email is verified and your account is ready.",
        "new_title" => "A new user registered",
        "new_body" => "A new user registered: :full_name <:email> · Role: :role",
    ],

    "account_created" => [
        "title" => "Account created",
        "body" => "An admin created your account with the role :role.",
        "subject" => "Your account is ready",
        "line1" => "An admin created an account for you with the role :role. Sign in with this email address.",
        "line2" => "This email does not contain a password. Use Forgot password to set your own.",
        "login" => "Sign in",
        "forgot" => "Forgot password",
    ],

    "account_email_change" => [
        "subject" => "Confirm your new email",
        "line1" => "You asked to use this address for your account. Use the button below to confirm it. The link works for 60 minutes.",
        "line2" => "Until you confirm, you can keep signing in with your old email. If you did not ask for this, you can ignore this email.",
        "verify" => "Confirm email",
    ],

    "security" => [
        "password_subject" => "Your password was changed",
        "password_line_self" => "You changed your password at :time.",
        "password_line_admin" => "Admin :admin changed your password at :time.",
        "password_warning" => "If you did not request this, use Forgot password and contact an admin.",
        "login" => "Sign in",
        "email_old_subject" => "Your account email was changed",
        "email_old_line" => "Your account email was changed to :email.",
        "email_old_warning" => "If you did not request this, contact an admin.",
        "email_new_subject" => "This email is now used to sign in",
        "email_new_line" => "This email is now used to sign in to your account.",
    ],

    "role_changed" => [
        "title" => "Role changed",
        "body" => "Your role is now :role.",
    ],
];
