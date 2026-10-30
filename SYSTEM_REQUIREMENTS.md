# Persyaratan Sistem (System Requirements) - BAST & Layanan Bagi Pakai DTSEN

Dokumen ini menjelaskan seluruh arsitektur sistem **Hybrid Modern (Python Backend + Laravel Frontend)** untuk aplikasi **Layanan Permintaan Bagi Pakai DTSEN & Pengesahan BAST 2026**.

---

## 🏗️ 1. Arsitektur Sistem Hybrid
Sistem ini memisahkan tugas komputasi secara elegan:
1. **Python FastAPI Backend Engine (Port 8000):**
   - Menangani pemrosesan logika bisnis, validasi variabel DTSEN, kalkulasi analitik, approval alur 5 tahapan, pencatatan audit trail forensik, dan penerbitan nomor BAST.
   - Pustaka terdaftar di [`requirements.txt`](requirements.txt).
2. **Laravel Presentation Layer (Port 8080):**
   - Menangani tampilan antarmuka (UI/UX) berbasis Blade templates (`.blade.php`), layout responsif, keamanan sesi/CSRF, dan manajemen tampilan bagi Perangkat Daerah (OPD) & Walidata.
3. **Basis Data Bersama (Shared SQLite):**
   - File `database/database.sqlite` diakses langsung secara lokal oleh Python dan PHP tanpa memerlukan server database terpisah.

---

## 💻 2. Kebutuhan Perangkat Keras & OS
- **Sistem Operasi:** Windows 10 / 11 (64-bit), Linux, atau macOS.
- **RAM:** Minimal 4 GB (Disarankan 8 GB).
- **Ruang Disk:** Minimal 500 MB ruang kosong.
- **Browser:** Google Chrome, Microsoft Edge, Firefox, atau Opera versi terbaru.

---

## 🛠️ 3. Kebutuhan Runtime & Pustaka

### A. Python Backend (Wajib)
- **Python:** Versi `3.10` atau `3.11+`
- **Pustaka Utama ([`requirements.txt`](requirements.txt)):**
  - `fastapi`: Framework REST API berkinerja tinggi
  - `uvicorn`: ASGI Server untuk FastAPI
  - `pydantic`: Validasi data skema
  - `python-multipart`: Penanganan upload form/multipart
  - `bcrypt` & `passlib`: Enkripsi & verifikasi kata sandi
  - `pandas` & `openpyxl`: Pemroses data & ekspor laporan

### B. PHP Frontend (Wajib)
- **PHP:** Versi `8.2` atau `8.3` (Tersedia portable di folder `php/`).
- **Ekstensi PHP:** `pdo_sqlite`, `sqlite3`, `fileinfo`, `curl`, `mbstring`, `openssl`, `gd`, `zip`, `intl`.
- **Framework:** Laravel 12

---

## 🚀 4. Cara Menjalankan Sistem (1-Klik)

Cukup klik dua kali:
```cmd
JALANKAN_SISTEM.bat
```

Skrip peluncur otomatis akan:
1. Menyiapkan dependensi Python (`pip install -r requirements.txt`).
2. Menyiapkan runtime PHP & dependensi vendor.
3. Menyiapkan database SQLite (`database/database.sqlite`), migrasi tabel, dan akun bawaan.
4. Menjalankan **Python FastAPI Backend** di port `8000`.
5. Menjalankan **Laravel Frontend Server** di port `8080`.
6. Membuka peramban secara otomatis ke `http://127.0.0.1:8080/`.

---

## 🔑 Kredensial Default Sistem
- **Portal Super Admin (Walidata):** `http://127.0.0.1:8080/dkisp-secret/`
  - Akun: `admin` / `admin` (atau `admin@gmail.com` / `admin`)
- **Portal Perangkat Daerah (OPD):** `http://127.0.0.1:8080/login`
  - Akun Dinsos: `dinsos` / `dinsos123`
  - Akun Bappeda: `bappeda` / `bappeda123`
- **Dokumentasi API Python:** `http://127.0.0.1:8000/docs` (Swagger UI Interaktif)
