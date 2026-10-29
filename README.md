# BAST & Layanan Permintaan Bagi Pakai DTSEN
### *Sistem Registrasi Tiket, Verifikasi Variabel Surat Edaran, Alur 5 Tahap & Pengesahan BAST*
**Repository:** [github.com/Syadada/BAST](https://github.com/Syadada/BAST)

---

## 🌟 Fitur Unggulan Sistem

### 1. Otomasi Lingkungan & Dependencies (Zero-Configuration)
Sistem ini dilengkapi dengan otomasi penuh untuk mengunduh seluruh dependensi dan menyiapkan *environment*:
* **GitHub Actions CI/CD (`.github/workflows/ci.yml`)**: Otomatis menjalankan instalasi dependensi (`composer install`), konfigurasi `.env`, inisialisasi SQLite database, migrasi tabel, dan verifikasi booting aplikasi pada setiap push/PR.
* **Auto Setup Script Windows (`auto_setup.bat`)**: Cukup klik dua kali, skrip akan otomatis mendeteksi PHP, mengunduh `composer.phar` resmi jika belum terpasang, menginstal seluruh dependensi (`vendor/`), membuat database SQLite, menjalankan migrasi & seeder, serta membuka browser langsung ke aplikasi.
* **Auto Setup Script Linux/Mac (`auto_setup.sh`)**: Skrip bash otomatis untuk lingkungan Linux, macOS, dan WSL.

---

### 2. Segmentasi Autentikasi & URL Rahasia Administrator
Untuk mencegah eskalasi hak akses dan manipulasi URL (*URL tampering*):
* **Portal Login OPD (`/login` / `/opd/login`)**:
  * Ditujukan untuk akun Perangkat Daerah (Dinas Sosial, Bappeda, dll.).
  * Menggunakan warna biru instansi (`#0b57d0`), form bersih, dan deteksi otomatis ID Laptop/Perangkat.
* **Portal Rahasia Administrator Walidata (`/dkisp-secret/`)**:
  * URL masuk rahasia yang tidak dapat ditebak.
  * URL decoy `/admin/login` secara otomatis dialihkan senyap ke `/login` umum untuk menyamarkan keberadaan rute admin.
* **Pencegahan Akses URL Ilegal (*URL Tampering Prevention*)**:
  * Jika akun OPD mencoba iseng membuka `/admin/*` atau `/admin/dashboard`, sistem akan langsung mengalihkannya kembali ke `/opd/dashboard` tanpa notifikasi banner yang mencurigakan.
  * Setiap percobaan dicatat secara forensik di **Audit Log Administrator** (`UNAUTHORIZED_ADMIN_URL_ATTEMPT`).

---

### 3. Alur 5 Tahap Bagi Pakai Data DTSEN & Pengesahan BAST
Alur kerja mengadopsi standar tata kelola BAST V2:
1. **Tahap 1: Pengajuan Tiket Permohonan** oleh OPD.
2. **Tahap 2: Verifikasi Kelengkapan & Variabel Data** (disesuaikan dengan Surat Edaran).
3. **Tahap 3: Pemrosesan & Ekstraksi Data** oleh tim Walidata.
4. **Tahap 4: Penandatanganan BAST (Berita Acara Serah Terima)** resmi antara Walidata dan OPD.
5. **Tahap 5: Penyerahan Data Selesai & Berita Acara Sah**.

---

## 🚀 Panduan Menjalankan Sistem

### Cara Cepat (Otomatis):

#### Di Windows:
Cukup klik dua kali file:
```cmd
auto_setup.bat
```
Atau jika portable PHP lokal sudah tersedia:
```cmd
JALANKAN_LAYANAN_DTSEN.bat
```

#### Di Linux / macOS / WSL:
```bash
chmod +x auto_setup.sh
./auto_setup.sh
```

---

## 🔑 Kredensial Akun Bawaan (Default)

### 1. Super Administrator (Walidata)
* **Portal Login:** `http://127.0.0.1:8080/dkisp-secret/`
* **Username / Email:** `admin` atau `admin@gmail.com`
* **Password:** `admin`
* **Dashboard:** `/admin/dashboard`

### 2. Perangkat Daerah (OPD)
* **Portal Login:** `http://127.0.0.1:8080/login`
* **Dashboard:** `/opd/dashboard`

| Instansi | Username | Email | Password | Role |
| :--- | :--- | :--- | :--- | :--- |
| **Dinas Sosial Provinsi** | `dinsos` | `dinsos@pemda.go.id` | `dinsos123` | `perangkat_daerah` |
| **Bappeda Litbang** | `bappeda` | `bappeda@pemda.go.id` | `bappeda123` | `perangkat_daerah` |

---

## 🛠️ Stack Teknologi
* **Framework:** Laravel 12 (PHP 8.2+)
* **Database:** SQLite
* **Autentikasi:** Multi-Guard & Session RBAC
* **Styling:** Vanilla CSS Custom UI
* **CI/CD:** GitHub Actions
