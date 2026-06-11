# Log perubahan & catatan teknis — 11 Juni 2026

Dokumen ini merangkum perubahan **Bundle Guest Registration**: pendaftaran bundle tidak lagi mewajibkan akun sistem untuk peserta yang diundang. Cukup **email valid**; tiket dan notifikasi dikirim lewat email; ketua bundle dapat melihat QR peserta di halaman detail pendaftaran setelah admin menerima submission.

**Panduan deploy production:** [`bundle-guest-registration-production-deploy.md`](bundle-guest-registration-production-deploy.md)

---

## Ringkasan eksekutif

| Area | Apa yang dilakukan |
|------|--------------------|
| **Alur bundle** | Peserta bundle (guest) tidak perlu punya akun; leader mengisi email + data formulir sekaligus. |
| **Konfirmasi undangan** | Guest bundle **auto-confirmed** saat submit (`member_confirmation_status = accepted`). Tidak ada langkah accept/reject undangan seperti tim. |
| **Email** | Leader + setiap guest menerima `RegistrationConfirmationMail` saat submit; guest menerima `RegistrationAcceptedMail` (dengan QR) setelah admin accept. |
| **Portal ketua** | Halaman `EventRegistration` menampilkan QR per peserta bundle yang sudah di-accept admin. |
| **Form fill** | UI bundle hanya validasi format email (bukan cek akun). Mode **team** tidak berubah. |
| **Database** | `form_answers.user_id` dan `email_logs.user_id` boleh **NULL** untuk guest. |
| **Backward compatibility** | Bundle lama dengan `member_confirmation_status = pending` tetap memakai alur undangan (`TeamInvitationController`). |

---

## Perbandingan alur: sebelum vs sesudah

### Sebelum (bundle lama)

1. Leader submit bundle + email peserta.
2. Backend **wajib** menemukan `User` untuk setiap email.
3. Member row dibuat dengan `member_confirmation_status = pending`.
4. `TeamInvitationMail` dikirim; peserta harus login dan accept undangan.
5. Admin baru bisa review member setelah accept.

### Sesudah (bundle guest)

1. Leader submit bundle + email peserta (cukup format RFC valid).
2. Member row dibuat dengan `user_id` nullable, `invited_email` terisi, `member_confirmation_status = accepted`.
3. `RegistrationConfirmationMail` dikirim ke guest (bukan undangan tim).
4. Admin dapat langsung review member.
5. Setelah admin accept → `RegistrationAcceptedMail` + QR ke email guest; ketua melihat QR di portal.

### Yang tidak berubah

- **Team registration** (`registration_mode = team`) — masih wajib akun + undangan accept/reject.
- **Single registration** — tidak berubah.
- **Admin bundle grouping UI** — tetap pakai `group_token`; member pending lama tetap tampil locked.

---

## File baru

| File | Peran |
|------|-------|
| `database/migrations/2026_06_11_000001_allow_guest_bundle_members_on_form_answers.php` | `user_id` nullable; index guest email (SQLite/PostgreSQL). |
| `app/Services/Registration/FormAnswerRecipientResolver.php` | Resolusi email penerima: `user.email` atau `invited_email`. |
| `app/Services/Registration/BundleGuestDuplicateChecker.php` | Cegah email guest duplikat per form (active submission). |
| `app/Services/Registration/BundleGuestDisplayNameResolver.php` | Nama tampilan guest dari `answers` atau `invited_email`. |

---

## File yang dimodifikasi

### Backend

| File | Perubahan |
|------|-----------|
| `app/Services/Registration/BundleRegistrationSubmitter.php` | Signature `array $memberEmails`; guest auto-accepted; `user_id` opsional jika email cocok dengan user existing. |
| `app/Http/Controllers/Dashboard/Events/Forms/FormSubmissionController.php` | Bundle: hapus lookup User wajib; kirim `SendRegistrationConfirmationJob` ke member (bukan `SendTeamInvitationJob`). |
| `app/Jobs/SendRegistrationConfirmationJob.php` | Pakai `FormAnswerRecipientResolver` — mendukung guest tanpa `user`. |
| `app/Jobs/SendRegistrationAcceptedJob.php` | Sama — email ke `invited_email` jika `user` null. |
| `app/Jobs/SendRegistrationRejectedJob.php` | Sama. |
| `app/Services/BundleSubmissionGrouper.php` | Tambah `display_name` per participant. |
| `app/Http/Controllers/Dashboard/User/UserEventRegistrationController.php` | Prop `bundle_participants` untuk ketua bundle (QR per member). |

### Frontend

| File | Perubahan |
|------|-----------|
| `resources/js/components/modules/dashboard/FormFillParticipantEmailsSection.vue` | Mode bundle: validasi format email saja (`valid` / `invalid`), tanpa API `check-email`. |
| `resources/js/pages/Dashboard/User/EventRegistration.vue` | Kartu "Bundle participants" dengan status + QR per guest. |
| `resources/js/components/modules/dashboard/FormBundleGroupDetailSheet.vue` | Pakai `display_name` jika ada. |
| `resources/js/components/modules/dashboard/FormBundleGroupsCardGridView.vue` | Sama. |
| `resources/js/types/event.d.ts` | `display_name?` pada `IBundleSubmissionMember`. |

### Tests

| File | Perubahan |
|------|-----------|
| `tests/Feature/Forms/FormRegistrationTest.php` | Test guest submit, duplicate email, admin accept guest, legacy pending block, team regression. |
| `tests/Feature/Dashboard/User/UserEventRegistrationPortalTest.php` | Test `bundle_participants` + QR di portal ketua. |

---

## Skema database

### Migrasi `2026_06_11_000001_allow_guest_bundle_members_on_form_answers`

**MySQL / MariaDB (production):**

- `ALTER TABLE form_answers MODIFY user_id CHAR(36) NULL`
- `ALTER TABLE email_logs MODIFY user_id CHAR(36) NULL`
- Foreign key `form_answers_user_id_foreign` → `ON DELETE SET NULL`
- **Tidak** menghapus baris atau kolom data lain.

**SQLite (PHPUnit):**

- Drop/recreate kolom `user_id` + rebuild partial unique index `form_answers_active_user_form_unique` dengan kondisi `user_id IS NOT NULL`.

**Partial unique index guest email** (SQLite/PostgreSQL saja):

```sql
CREATE UNIQUE INDEX form_answers_active_guest_email_form_unique
ON form_answers (form_id, invited_email)
WHERE user_id IS NULL AND invited_email IS NOT NULL ...
```

Pada **MySQL/MariaDB**, index ini tidak dibuat; duplikat dicegah di aplikasi (`BundleGuestDuplicateChecker`).

### Baris `form_answers` untuk guest bundle

| Kolom | Leader | Guest member |
|-------|--------|--------------|
| `user_id` | ID leader | `NULL` (atau ID user jika email sudah terdaftar) |
| `invited_email` | `NULL` | email guest (lowercase) |
| `registration_role` | `leader` | `member` |
| `member_confirmation_status` | `accepted` | `accepted` (baru) |
| `invitation_token` | `NULL` | `NULL` (baru) |
| `group_token` | sama untuk seluruh group | sama |

---

## Kontrak API / props Inertia

### Submit bundle (`POST` form submission)

Payload tidak berubah:

```json
{
  "full_name": "Leader name",
  "bundle__full_name__0": "Guest name",
  "team_member_emails": ["guest@example.com"]
}
```

Validasi baru:

- Email harus RFC valid dan unik dalam bundle.
- Email tidak boleh sama dengan email leader.
- Email tidak boleh sudah dipakai submission aktif pada form yang sama.

### Portal ketua — `Dashboard/User/EventRegistration`

Prop baru:

```ts
bundle_participants?: Array<{
  invited_email: string
  display_name: string
  review_status: 'pending' | 'accepted' | 'rejected'
  registration_code: string | null
  qr_base64: string | null  // hanya jika accepted
}>
```

Hanya dikirim jika `registration_mode === 'bundle'` dan `registration_role === 'leader'`.

---

## Email

| Event | Job | Penerima |
|-------|-----|----------|
| Bundle submit | `SendRegistrationConfirmationJob` | Leader + setiap guest |
| Admin accept | `SendRegistrationAcceptedJob` | User atau `invited_email` |
| Admin reject | `SendRegistrationRejectedJob` | User atau `invited_email` |

`FormAnswerRecipientResolver::email()`:

```php
return $submission->user?->email ?: $submission->invited_email;
```

`email_logs.user_id` boleh `NULL` untuk log email guest.

---

## Backward compatibility (data production lama)

| Kondisi data lama | Perilaku setelah deploy |
|-------------------|-------------------------|
| Bundle member `pending` + `invitation_token` | Tetap locked di admin sampai accept undangan; `FormAnswerReviewController` menolak accept. |
| Bundle member `accepted` (undangan) | Admin review seperti biasa. |
| Semua row dengan `user_id` terisi | Tidak terpengaruh migrasi nullable. |

Test: `test_legacy_bundle_pending_member_still_blocked_from_admin_review`

---

## Cara uji lokal (Docker dev)

```bash
# Pastikan container jalan
docker compose ps

# Cek status migrasi
docker compose exec app php artisan migrate:status

# Jalankan test terkait bundle/guest
docker compose exec app php vendor/bin/phpunit --filter "bundle|guest|legacy_bundle|team_submission_still"
```

Hasil yang diharapkan: semua test lulus (14+ assertions).

---

## Checklist sebelum merge / deploy

- [ ] Migrasi `2026_06_11_000001_...` ada di branch deploy
- [ ] Backup database production **sebelum** deploy (lihat panduan production)
- [ ] `php artisan migrate --force` di production (otomatis lewat `entrypoint.prod.sh` saat container restart)
- [ ] Queue worker sync (production memakai `QUEUE_CONNECTION` di `.env.production`) — job email harus jalan
- [ ] Uji manual: submit bundle dengan email yang **belum** punya akun
- [ ] Uji manual: admin accept guest → email + QR ketua di detail pendaftaran

---

## Referensi

- Admin bundle UI (requirement asli): [`../sapto/requires/admin-bundling-submissions-requirements.md`](../sapto/requires/admin-bundling-submissions-requirements.md)
- Deploy production: [`bundle-guest-registration-production-deploy.md`](bundle-guest-registration-production-deploy.md)
