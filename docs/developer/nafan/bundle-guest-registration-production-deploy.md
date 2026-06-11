# Bundle Guest Registration — Panduan Deploy Production

Panduan ini menjelaskan cara menerapkan perubahan **bundle guest registration** di server production (`d-form.doscom.org`) dengan aman. **Jangan** menjalankan perintah destruktif (`migrate:fresh`, `migrate:refresh`, `db:wipe`) di production.

Dokumen perubahan teknis: [`nafanChanges11-06-2026.md`](nafanChanges11-06-2026.md)

---

## Ringkasan risiko migrasi

| Aspek | Aman untuk data existing? | Catatan |
|-------|-------------------------|---------|
| Menambah nullable pada `user_id` | **Ya** | Tidak menghapus atau mengubah nilai baris yang ada |
| Recreate foreign key | **Ya** | Hanya mengubah constraint, bukan data |
| Index guest email | Hanya SQLite/PostgreSQL | Production MariaDB: duplikat dicegah di aplikasi |
| Rollback migrasi | **Tidak disarankan** | Gagal jika sudah ada baris `user_id IS NULL` |

---

## Prasyarat

- Akses SSH ke server production
- Docker & Docker Compose terpasang
- File `docker-compose.prod.yml` dan `.env.production` sudah dikonfigurasi
- Branch/kode yang akan deploy sudah berisi migrasi:
  - `database/migrations/2026_06_11_000001_allow_guest_bundle_members_on_form_answers.php`

---

## Langkah 1 — Backup database (wajib)

Sebelum deploy apa pun, buat backup penuh database.

```bash
# Dari server production — sesuaikan password/container
docker exec d-form-db mysqldump -u root -p \
  --single-transaction --routines --triggers \
  d_form > backup_before_bundle_guest_$(date +%Y%m%d_%H%M%S).sql
```

Simpan file backup di lokasi aman (di luar server jika memungkinkan).

**Verifikasi backup:**

```bash
ls -lh backup_before_bundle_guest_*.sql
# Pastikan ukuran file masuk akal (> 0 byte)
```

---

## Langkah 2 — Deploy kode

```bash
cd /path/to/d-form-v2   # sesuaikan path deploy di server

# Ambil kode terbaru
git fetch origin
git checkout main       # atau branch release yang disepakati
git pull origin main

# Build image production baru
docker compose -f docker-compose.prod.yml build app

# Restart stack (db & redis tidak perlu di-rebuild)
docker compose -f docker-compose.prod.yml up -d
```

**Yang terjadi saat container `d-form-app` start:**

`docker/entrypoint.prod.sh` secara otomatis menjalankan:

1. `php artisan migrate --force`
2. `php artisan config:cache`
3. `php artisan route:cache`
4. `php artisan view:cache`
5. Start Octane (FrankenPHP)

Frontend assets sudah di-build di dalam image (`npm run build` di `Dockerfile.prod`).

---

## Langkah 3 — Verifikasi migrasi

```bash
docker exec d-form-app php artisan migrate:status
```

Pastikan baris berikut berstatus **Ran**:

```
2026_06_11_000001_allow_guest_bundle_members_on_form_answers
```

Jika migrasi gagal, **jangan** ulangi deploy membabi buta. Cek log:

```bash
docker logs d-form-app --tail 100
```

---

## Langkah 4 — Verifikasi skema (tanpa mengubah data)

Jalankan di MariaDB:

```bash
docker exec -it d-form-db mysql -u root -p d_form
```

```sql
-- user_id harus nullable
SHOW CREATE TABLE form_answers\G

-- Hitung baris — harus sama dengan sebelum deploy
SELECT COUNT(*) AS total FROM form_answers;

-- Tidak ada baris yang kehilangan user_id secara tidak sengaja
SELECT COUNT(*) AS should_match_total
FROM form_answers
WHERE user_id IS NOT NULL;

-- Guest baru (setelah deploy) akan muncul di sini
SELECT COUNT(*) AS guest_rows FROM form_answers WHERE user_id IS NULL;
```

Kolom `user_id` pada `form_answers` harus menunjukkan `DEFAULT NULL`.

Foreign key yang diharapkan:

```
CONSTRAINT form_answers_user_id_foreign
  FOREIGN KEY (user_id) REFERENCES users (id)
  ON DELETE SET NULL ON UPDATE CASCADE
```

---

## Langkah 5 — Verifikasi aplikasi

### 5.1 Health check dasar

- Buka https://d-form.doscom.org — halaman utama load normal
- Login sebagai member dan admin — tidak ada error 500

### 5.2 Uji alur bundle guest (staging atau production dengan hati-hati)

1. Buat / gunakan event dengan form `registration_mode: bundle`.
2. Login sebagai member (ketua).
3. Isi formulir bundle dengan email peserta yang **belum** punya akun di sistem.
4. Submit — harus sukses; toast: konfirmasi email dikirim ke semua peserta.
5. Cek inbox email guest — harus menerima email "registration received".
6. Login admin → submissions bundle → review & **accept** guest member.
7. Guest menerima email acceptance + QR; ketua membuka **Detail pendaftaran** → QR guest tampil.

### 5.3 Uji backward compatibility (jika ada data bundle lama)

Jika production punya bundle dengan member `pending` invitation:

- Admin masih melihat member sebagai locked sampai undangan diterima
- Setelah invitee accept (login + halaman undangan), admin dapat review

---

## Langkah 6 — Queue & email

Pastikan job email berjalan. Cek `.env.production`:

```env
QUEUE_CONNECTION=redis   # atau sync/database sesuai setup
MAIL_MAILER=...
```

Jika memakai queue worker terpisah, restart worker setelah deploy:

```bash
# Contoh jika worker dijalankan manual di container
docker exec d-form-app php artisan queue:restart
```

Cek log email gagal:

```sql
SELECT id, recipient_email, notification_type, status, error_message, created_at
FROM email_logs
ORDER BY created_at DESC
LIMIT 20;
```

---

## Troubleshooting

### Migrasi gagal: foreign key error

Kemungkinan constraint name berbeda. Cek:

```sql
SELECT CONSTRAINT_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_NAME = 'form_answers' AND CONSTRAINT_TYPE = 'FOREIGN KEY';
```

Restore dari backup jika perlu; jangan pakai `migrate:rollback` tanpa memahami dampaknya.

### Submit bundle gagal: "participant with this email is already registered"

Duplikat aktif pada form yang sama. Ini perilaku yang diharapkan. Guest harus memakai email lain atau admin harus reject submission lama.

### Email guest tidak terkirim

1. Cek `email_logs` untuk `form_answer_id` terkait
2. Pastikan `invited_email` terisi pada baris member
3. Pastikan queue worker jalan jika `QUEUE_CONNECTION=redis`

### QR tidak muncul di portal ketua

- Guest harus `review_status = accepted` dan punya `registration_code`
- Hanya **ketua** bundle (`registration_role = leader`) yang melihat `bundle_participants`
- Buka halaman **Detail pendaftaran**, bukan hanya sidebar event

---

## Yang TIDAK boleh dilakukan di production

| Perintah | Alasan |
|----------|--------|
| `php artisan migrate:fresh` | Menghapus seluruh data |
| `php artisan migrate:refresh` | Rollback semua + migrate ulang |
| `php artisan db:wipe` | Menghapus semua tabel |
| `migrate:rollback` setelah ada guest | Rollback gagal / merusak data jika `user_id` NULL ada |

---

## Rollback darurat

Hanya jika deploy menyebabkan error kritis **dan belum ada** submission guest baru (`user_id IS NULL`):

```bash
# Cek dulu — harus 0
docker exec d-form-db mysql -u root -p d_form \
  -e "SELECT COUNT(*) FROM form_answers WHERE user_id IS NULL;"

# Jika 0, rollback satu batch (hati-hati)
docker exec d-form-app php artisan migrate:rollback --step=1 --force
```

Kemudian deploy image/kode versi sebelumnya:

```bash
git checkout <commit-sebelumnya>
docker compose -f docker-compose.prod.yml build app
docker compose -f docker-compose.prod.yml up -d
```

Jika sudah ada data guest, **restore dari backup** lebih aman daripada rollback.

---

## Checklist deploy production

```
[ ] Backup database selesai dan diverifikasi
[ ] Kode terbaru di-pull / di-deploy
[ ] docker compose -f docker-compose.prod.yml build app
[ ] docker compose -f docker-compose.prod.yml up -d
[ ] migrate:status — migrasi 2026_06_11 sudah Ran
[ ] SHOW CREATE TABLE form_answers — user_id nullable
[ ] COUNT form_answers sama dengan sebelum deploy
[ ] Uji submit bundle dengan email guest
[ ] Uji admin accept + email QR
[ ] Uji portal ketua — QR guest di detail pendaftaran
[ ] Monitor log container 15–30 menit pertama
```

---

## Kontak & referensi

- Perubahan teknis lengkap: [`nafanChanges11-06-2026.md`](nafanChanges11-06-2026.md)
- Stack production: `docker-compose.prod.yml`, `docker/Dockerfile.prod`, `docker/entrypoint.prod.sh`
- Domain production: `d-form.doscom.org` (Traefik)
