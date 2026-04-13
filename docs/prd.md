# Product Requirements Document (PRD) — D-Form v2

**Versi dokumen:** 1.0  
**Sumber:** Project Brief D-Form v2 (PDF), diselaraskan dengan keadaan repositori dan keputusan stack frontend.

Dokumen ini mendefinisikan *apa* yang dibangun dan kriteria utamanya. Detail implementasi mengikuti [pedoman front-end](rules/front-end.md), [pedoman back-end](rules/back-end.md), dan [struktur folder](02-directory-structure.md). Tahapan pengiriman diuraikan di [milestone.md](milestone.md).

---

## 1. Ringkasan produk

D-Form v2 adalah aplikasi web untuk **membuat, mengelola, dan memproses formulir pendaftaran event secara dinamis**. Admin mendefinisikan event dan form tanpa mengubah kode; peserta mendaftar melalui form tersebut, menerima **email konfirmasi berisi QR Code**, dan kehadiran dicatat dengan **pemindaian QR** saat event berlangsung.

---

## 2. Tujuan produk

1. Menyediakan platform manajemen event berbasis web yang **dinamis dan dapat dikembangkan** (scalable secara arsitektur).
2. Memudahkan admin membuat **form event dengan input yang sepenuhnya dapat dikustomisasi** (label, tipe field, validasi, urutan).
3. Mempercepat **pendaftaran oleh peserta** melalui alur yang jelas (pilih event → isi form → konfirmasi).
4. Mengotomatisasi **email konfirmasi** pendaftaran beserta **QR unik** untuk absensi.
5. Menyediakan **absensi berbasis QR** yang terukur (waktu, event, petugas scan).
6. Menegakkan **pemisahan peran Admin dan Member** untuk keamanan dan pembatasan akses.

---

## 3. Persona dan use case utama

### 3.1 Admin / Event organizer

- Membuat dan mengatur event (metadata dan jadwal pendaftaran).
- Mendesain **form pendaftaran dinamis** per event.
- (Fase lanjutan) Mengirim **siaran email** terkait event mendatang atau perubahan informasi.
- Melihat **daftar peserta** dan statistik kehadiran.
- Melakukan **pemindaian QR** untuk absensi (kamera perangkat melalui web app).

### 3.2 Member / peserta

- Mendaftar ke event yang dibuka.
- Mengisi **form dinamis** sesuai konfigurasi admin.
- Menerima **email konfirmasi** (detail event + QR).
- Menghadiri event dan menunjukkan QR untuk discan.

---

## 4. Lingkup fitur

### 4.1 MVP (wajib untuk rilis pertama)

| Area | Deskripsi |
|------|-----------|
| **Autentikasi & peran** | Multi-peran: Admin dan Member. Alur login, registrasi akun, lupa password. Akses dibatasi middleware berdasarkan peran. |
| **Manajemen event** | Admin membuat event dengan: judul, deskripsi, lokasi, tanggal event, tanggal buka–tutup pendaftaran, kuota peserta. Halaman daftar event, detail event, dan indikator status pendaftaran (buka/tutup/kuota). |
| **Form builder dinamis** | Per event: pengaturan label, nama field internal, jenis field (text, number, date, textarea, select, radio, checkbox, file), urutan field (dapat diurutkan), validasi per field (required, min/max, regex, unique, tipe file diizinkan, ukuran file maksimum). |
| **Pendaftaran (Member)** | Pemilihan event, render form otomatis, penyimpanan jawaban dalam **struktur yang dapat diserialisasi (mis. JSON)** di sisi server, upload file jika ada field file, validasi sesuai aturan admin. |
| **Notifikasi email** | Email sukses pendaftaran berisi detail event, ringkasan data peserta, dan **QR unik** terikat pada submission. Pengiriman melalui **SMTP** (Gmail app password atau penyedia lain). Pengiriman email non-blocking memakai **antrean (queue)**. |
| **Absensi QR** | Satu QR unik per submission; saat discan, sistem mencatat waktu hadir, event, dan **admin yang memproses scan**. |
| **Dasbor & pelaporan** | Admin melihat jumlah pendaftar per event, statistik kehadiran, data peserta, dan log/rekaman absensi (minimal tampilan tabular; ekspor dapat berupa CSV pada MVP). |

### 4.2 Fase lanjutan (di luar MVP inti)

- **Email broadcast:** pengingat event mendatang, pemberitahuan perubahan jadwal/lokasi/deskripsi.
- Pelaporan lanjutan (filter lanjutan, agregasi, unduhan format tambahan).
- Optimasi skala dan observabilitas (metrik, rate limit, dsb.) sesuai kebutuhan operasional.

---

## 5. Kebutuhan non-fungsional

| Aspek | Requirement |
|-------|-------------|
| **Keamanan** | Autentikasi sesi/Laravel; pembatasan route per peran; validasi server-side untuk semua input form dinamis; upload file dibatasi tipe dan ukuran. |
| **Performa** | Pengiriman email tidak menghalangi response HTTP utama (queue). |
| **Audit absensi** | Rekaman mencakup siapa (admin) yang melakukan scan bila relevan untuk akuntabilitas. |
| **Kompatibilitas** | Flow scan QR dapat berjalan pada browser umum di desktop/mobile dengan akses kamera (dengan izin pengguna). |
| **Kualitas kode** | Formatter PHP (Laravel Pint, PSR-12) dan Prettier untuk front-end sesuai konfigurasi proyek. |

---

## 6. Stack teknologi (keputusan disepakati)

Deviasi dari Project Brief PDF bagian Blade/Livewire: **UI aplikasi utama dibangun dengan Vue + Inertia**, bukan Livewire/Volt sebagai lapisan UI utama.

### 6.1 Backend

- Laravel 12, PHP 8.2+.
- Endpoint/halaman melalui **Inertia** (response `Inertia::render`) dan validasi Form Request / controller sesuai pola proyek.
- Basis data: **MySQL / MariaDB**; **Redis** untuk cache/antrean sesuai konfigurasi.
- **Laravel Queue** untuk pengiriman email.
- Pustaka **QR Code** (mis. Simple QrCode atau setara yang disepakati tim) untuk generate gambar/ payload QR.
- Web server: sesuai deployment tim (mis. Frankenphp/Octane seperti yang tercatat di README proyek).

### 6.2 Frontend

- **Vue 3** (Composition API, `<script setup>`) dan **Inertia.js** untuk navigasi dan state halaman.
- **Tailwind CSS** dan komponen UI mengikuti konvensi repositori (mis. pola Shadcn-Vue di `resources/js/components/ui` — lihat [front-end rules](rules/front-end.md)).
- **Bukan** Livewire/Volt untuk fitur-fitur di atas; migrasi dari modul Livewire yang masih ada adalah bagian dari pekerjaan berkelanjutan (lihat [milestone.md](milestone.md)).

### 6.3 Opsional / keputusan tim

- **Filament** (paket ada di `composer.json`) dapat dipakai untuk panel admin terpisah atau dikurangi seiring konsolidasi UI di Vue — keputusan eksplisit disarankan di milestone M0 agar tidak ada duplikasi UX.

### 6.4 Tooling

- Node.js & npm untuk build front-end (Vite).
- Docker untuk lingkungan tim (folder `docker/`).
- Dokumentasi kode: PHPDoc dan Markdown sesuai kebiasaan proyek.

---

## 7. Model data (ringkas)

Entitas utama dan relasi logis (sesuai brief; detail skema mengikuti migrasi di `database/migrations/`):

| Entitas / tabel | Peran |
|-----------------|--------|
| `users` | Akun pengguna; peran Admin/Member terikat ke mekanisme permission (mis. Spatie Laravel Permission). |
| `events` | Metadata event, jendela pendaftaran, kuota. |
| `event_form_fields` | Definisi field form dinamis per event (tipe, validasi, urutan). |
| `event_form_submissions` | Satu baris pendaftaran per user per event; payload jawaban dalam format JSON (+ referensi file jika perlu). |
| `event_schedules` | Jadwal terkait event (jika digunakan untuk multi-sesi atau jadwal turunan). |
| `event_attendances` | Rekaman kehadiran (submission, waktu, event, referensi admin scanner). |
| `email_logs` | Audit pengiriman email (status, korelasi dengan submission/event). |

---

## 8. Kriteria sukses (definisi “selesai” tingkat produk)

- Alur **Admin**: buat event → bangun form → buka pendaftaran → lihat peserta → scan QR → lihat statistik kehadiran berjalan tanpa error kritikal pada jalur utama.
- Alur **Member**: daftar → isi form dinamis → terima email dengan QR → hadir dan tercatat absensi.
- Kebijakan akses: Member tidak mengakses panel admin; Admin tidak mengubah data orang lain tanpa alur yang disengaja (sesuai desain permission).
- Dokumentasi operasional minimal: cara set SMTP, queue worker, dan menjalankan build front-end (mengacu [README](../README.md) dan [instalasi](01-installation.md)).

---

## 9. Luar lingkup (eksplisit)

- Aplikasi mobile native (hanya web yang responsif).
- Pembayaran / gateway (kecuali ditambahkan di PRD revisi).
- Integrasi kalender pihak ketiga (Google Calendar, dsb.) kecuali ditambahkan kemudian.

---

## 10. Referensi

- [Milestone & fase pengiriman](milestone.md)
- [README proyek](../README.md)
- Project Brief D-Form v2 (PDF)
