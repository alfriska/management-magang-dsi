# 🔐 Google OAuth + Google Authenticator 2FA Setup Guide

## Overview

Sistem autentikasi ini mengimplementasikan:

1. **Google OAuth** - Login menggunakan akun Google
2. **Google Authenticator 2FA** - Verifikasi tambahan dengan TOTP (Time-based One-Time Password)

## Flow Login

```
User klik "Login with Google"
         ↓
Redirect ke Google OAuth
         ↓
User pilih akun Google
         ↓
Callback ke aplikasi
         ↓
    [First Time?]
    /           \
  Yes           No
   ↓             ↓
Setup 2FA    Verify 2FA
(QR Code)    (Input Code)
   ↓             ↓
Verifikasi  Verifikasi
   ↓             ↓
   └─────────────┘
         ↓
    Dashboard
```

## Setup Google OAuth Credentials

### 1. Buka Google Cloud Console

- Kunjungi https://console.cloud.google.com/

### 2. Buat Project Baru (atau pilih yang sudah ada)

- Klik dropdown project di navbar
- Klik "New Project"
- Masukkan nama project, misal: "InternHub"
- Klik "Create"

### 3. Enable Google+ API

- Buka **APIs & Services** → **Library**
- Cari "Google+ API" atau "Google People API"
- Klik dan Enable

### 4. Setup OAuth Consent Screen

- Buka **APIs & Services** → **OAuth consent screen**
- Pilih "External" untuk test, atau "Internal" untuk G Suite org
- Isi informasi aplikasi:
    - **App name**: InternHub
    - **User support email**: your-email@example.com
    - **Developer contact**: your-email@example.com
- Klik "Save and Continue"
- Di bagian **Scopes**, tambahkan:
    - `.../auth/userinfo.email`
    - `.../auth/userinfo.profile`
- Lanjutkan sampai selesai

### 5. Buat OAuth Credentials

- Buka **APIs & Services** → **Credentials**
- Klik **+ CREATE CREDENTIALS** → **OAuth client ID**
- Pilih **Application type**: Web application
- **Name**: InternHub Web
- **Authorized JavaScript origins**:
    ```
    http://localhost:8000
    ```
- **Authorized redirect URIs**:
    ```
    http://localhost:8000/auth/google/callback
    ```
- Klik "Create"

### 6. Copy Credentials

Anda akan mendapat:

- **Client ID**: `xxx.apps.googleusercontent.com`
- **Client Secret**: `GOCSPX-xxx`

### 7. Update .env File

```env
GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxx
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## Production Setup

Untuk production, update redirect URI:

```env
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

Dan tambahkan domain production di Google Console:

- **Authorized JavaScript origins**: `https://yourdomain.com`
- **Authorized redirect URIs**: `https://yourdomain.com/auth/google/callback`

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       ├── SocialAuthController.php    # Handle Google OAuth
│   │       └── TwoFactorAuthController.php # Handle 2FA
│   └── Middleware/
│       └── TwoFactorMiddleware.php         # Protect routes
├── Models/
│   └── User.php                            # Updated with 2FA fields

resources/views/auth/
├── login.blade.php                         # Updated with Google button
└── 2fa/
    ├── setup.blade.php                     # QR Code setup page
    ├── verify.blade.php                    # OTP verification page
    ├── manage.blade.php                    # 2FA management
    └── regenerate.blade.php                # Regenerate QR Code

database/migrations/
└── 2026_01_19_000001_add_google2fa_and_social_columns_to_users_table.php

config/
└── services.php                            # Google OAuth config
```

## Testing

1. Jalankan server:

    ```bash
    php artisan serve
    ```

2. Buka browser: http://localhost:8000/login

3. Klik "Masuk dengan Google"

4. Pilih akun Google

5. Scan QR Code dengan Google Authenticator app:
    - Download dari [App Store](https://apps.apple.com/app/google-authenticator/id388497605)
    - Download dari [Play Store](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2)

6. Masukkan 6 digit kode

7. Login berhasil!

## Troubleshooting

### Error: Invalid Client

- Pastikan Client ID dan Client Secret benar
- Pastikan Redirect URI match persis dengan yang di Google Console

### Error: Redirect URI Mismatch

- Cek trailing slash
- Pastikan protocol sama (http vs https)
- Pastikan port sama

### Error: Access Denied

- User mungkin tidak mengizinkan akses
- Cek OAuth consent screen sudah lengkap

### QR Code tidak muncul

- Pastikan package `bacon/bacon-qr-code` terinstall
- Run `composer require bacon/bacon-qr-code`

## Security Notes

1. **Secret Key** - Disimpan encrypted di database
2. **Session** - User ID disimpan di session selama proses 2FA
3. **Window** - Kode berlaku ±60 detik (window: 2)
4. **HTTPS** - Gunakan HTTPS di production

## Commands

```bash
# Install packages
composer require laravel/socialite pragmarx/google2fa-laravel bacon/bacon-qr-code

# Run migration
php artisan migrate

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```
