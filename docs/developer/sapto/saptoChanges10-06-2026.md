# Sapto Changes — 10 Juni 2026

Dokumentasi perubahan sejak commit terakhir di `docs/developer/sapto/` (5 Juni 2026). Mencakup **pengelompokan bundle submission**, **peningkatan UI submission**, **reusable routes**, **member dashboard**, **re-registrasi setelah reject**, **password reset**, dan kontribusi kolaborator lainnya.

---

## Ringkasan (TL;DR)

| Area | Perubahan utama |
|------|-----------------|
| **Bundle Submissions** | Sistem pengelompokan submission berdasarkan `group_token` dengan detail view (sheet) dan card grid. |
| **Member Dashboard** | `MemberDashboardController` baru; statistik event participation & upcoming events dari data real. |
| **Re-registration** | User bisa mendaftar ulang setelah submission ditolak; record lama tetap tersimpan untuk audit. |
| **Password Reset** | `PasswordResetService`, `PasswordResetLinkController`, notifikasi custom, sinkronisasi email verification Google OAuth. |
| **Reusable Routes** | `routes.ts` baru sebagai pusat definisi URL frontend; mengganti semua hardcoded path di 50+ file. |
| **UI Submission** | Refactor besar pada `FormBundleGroupsCardGridView`, `DashboardSidebar`, cleanup CSS ~500 baris. |
| **Security** | Hapus info route yang membocorkan konfigurasi server. |
| **Docker** | Update event registration links & conditional routing; penyesuaian `docker-compose.yml`. |

---

## Timeline Perubahan

### 5 Juni 2026

| Waktu | Commit | Author | Deskripsi |
|-------|--------|--------|-----------|
| 18:19 | `cd0e569` | zappto | Tambah dokumentasi requirement submission (docs) |
| 18:46 | `da561db` | Nafunnn | **feat:** bundle submission grouping & detail views |
| 19:24 | `dec712c` | Nafunnn | **feat:** MemberDashboardController & update user dashboard |
| 20:11 | `765816f` | zappto | **enhanced ui submission** — refactor besar UI submission |
| 20:55 | `3b7244f` | Nafunnn | **feat:** re-registration setelah rejection |
| 21:18 | `e63c13d` | Nafunnn | **feat:** password reset & email verification enhancement |
| 21:26 | `462ce23` | zappto | Perbaikan kecil pada `FormBundleGroupsCardGridView` |

### 10 Juni 2026

| Waktu | Commit | Author | Deskripsi |
|-------|--------|--------|-----------|
| 12:09 | `3819170` | Harry_lbi | **feat:** update event registration links & conditional routing |
| 15:18 | `521f2e7` | mbagusaditya | **fix:** hapus info route (security) |
| 17:02 | `dbe9b0d` | zappto | **fix:** hardcoded routes → reusable routes, bug fix & responsive |

---

## Backend (PHP)

### 1. Bundle Submission Grouper — `BundleSubmissionGrouper.php` (baru)

- Service untuk mengelompokkan form submissions berdasarkan `group_token`.
- Menghitung review status per group (accepted, rejected, pending).
- `FormSubmissionsController` mendukung mode **bundle registration** dengan pagination untuk grouped submissions.

### 2. Member Dashboard — `MemberDashboardController.php` (baru)

- Endpoint dashboard user (`/dashboard/user`) dengan data real:
  - Statistik partisipasi event (total joined, accepted, pending).
  - Daftar upcoming events yang diikuti user.
- Route di `routes/web/admin/index.php` diperbarui untuk controller baru.

### 3. Re-registrasi Setelah Rejection

| File | Perubahan |
|------|-----------|
| `FormAnswer.php` | Tambah `scopeExcludeRejectedSubmissions()` untuk filter rejected saat duplicate check. |
| `FormAccessGuard.php` | Exclude rejected submissions dari pengecekan akses. |
| `BundleRegistrationSubmitter.php` | Update logic registrasi untuk mendukung re-registrasi. |
| `TeamRegistrationSubmitter.php` | Sama — exclude rejected dari pengecekan duplikat. |
| `EventRegistrationCounter.php` (baru) | Service penghitung registrasi aktif (exclude rejected). |
| `FormAnswerReviewController.php` | Penyesuaian flow review. |
| `FormSubmissionController.php` | Update logic submit untuk skenario re-registrasi. |

Migration baru: `allow_multiple_form_answers_per_user_after_rejection.php`

### 4. Password Reset Enhancement

| File | Perubahan |
|------|-----------|
| `PasswordResetService.php` (baru) | Handle password reset link requests. |
| `PasswordResetLinkController.php` (baru) | Endpoint baru untuk password reset link. |
| `ForgotPasswordController.php` | Refactor untuk pakai `PasswordResetService`. |
| `OAuthController.php` | Sinkronisasi `email_verified_at` untuk user Google OAuth. |
| `ResetPasswordNotification.php` (baru) | Custom notification email reset password. |
| `User.php` | Tambah `email_verified_at` timestamp. |
| `AppServiceProvider.php` | Konfigurasi tambahan untuk reset password. |
| `.env.example` | Tambah variable `PASSWORD_RESET_TOKEN_EXPIRY`. |
| `config/auth.php` | Konfigurasi token expiry dari env. |

### 5. Reusable Routes — Backend

| File | Perubahan |
|------|-----------|
| `LoginController.php` | Redirect menggunakan route constants, bukan hardcoded path. |
| `OAuthController.php` | Redirect disesuaikan dengan reusable routes. |
| `RegisterController.php` | Redirect ke route yang konsisten. |
| `EnsureMemberPortalAccess.php` | Middleware pakai route constant. |
| `EnsureOrganizerDashboardAccess.php` | Middleware pakai route constant. |
| `bootstrap/app.php` | Penyesuaian routing configuration. |
| `routes/web/admin/index.php` | Refactor 45+ baris route dari hardcoded ke reusable. |

### 6. Security Fix

- Hapus route `/info` (phpinfo) di `routes/web/index.php` untuk mencegah kebocoran konfigurasi server. (oleh mbagusaditya)

---

## Frontend (Vue / TS)

### 1. `routes.ts` — Pusat Definisi URL (baru)

File baru `resources/js/lib/routes.ts` menjadi **single source of truth** untuk semua URL frontend:

- Landing pages (`/`, `/features`, `/docs`, `/events`)
- Auth (`/auth/login`, `/auth/register`, `/auth/forgot-password`, dll.)
- Admin dashboard (`/admin/dashboard/events`, `/admin/dashboard/events/:slug`, dll.)
- Member area (`/events/joined`, `/events/joined/:slug`, dll.)
- Form fill, builder, scan, registrants, submissions, dll.

Mengganti hardcoded path di **50+ file** Vue/TS.

File dihapus: `dashboardProfileRoutes.ts` (digantikan `routes.ts`).

### 2. Bundle Submission UI

| Komponen | Perubahan |
|----------|-----------|
| `FormBundleGroupsCardGridView.vue` (baru) | Card grid view untuk menampilkan grouped submissions dengan status badge. |
| `FormBundleGroupDetailSheet.vue` (baru) | Sheet detail untuk melihat member dalam satu bundle group. |
| `FormSubmissionDetailSheet.vue` | Update untuk handle individual submissions dan bundle members. |
| `Submissions.vue` | Refactor besar: mendukung bundle mode, improved filtering dan layout. |
| `useFormSubmissionsPage.ts` | Composable diperbarui untuk bundle functionality. |
| `formSubmissionsUi.ts` | Utility functions baru untuk UI submission. |

### 3. Enhanced UI Submission (zappto)

- **DashboardSidebar.vue**: Refactor besar ~200 baris, perbaikan navigasi dan layout.
- **app.css**: Cleanup ~500 baris CSS yang tidak terpakai.
- **Dashboard/Index.vue**: Penyederhanaan dari ~225 baris; lebih clean dan maintainable.
- **DashboardFocusLayout.vue** & **DashboardLayout.vue**: Minor adjustments.
- **Sidebar components**: Perbaikan `Sidebar.vue` dan `SidebarContent.vue`.

### 4. Member Dashboard UI

- `Dashboard/User/Index.vue`: Refactor dari dummy data ke props real dari `MemberDashboardController`.
- `EventCalendar.vue`: Menerima events sebagai props, perbaikan type definitions.
- `dummyData.ts`: Tambah deprecation warning, hanya untuk development.

### 5. Password Reset UI

- `AuthForgotPasswordForm.vue`: UI form forgot password diperbarui, UX lebih baik.

### 6. Responsive & Bug Fixes (zappto — 10 Juni)

Perbaikan responsivitas dan bug di banyak komponen:

| Area | File yang terpengaruh |
|------|----------------------|
| Dashboard | `DashboardNavbar`, `DashboardSidebar`, `EventCalendar`, `EventShowAsideRail`, `RecentEventsCard` |
| Landing | `Footer`, `Navbar`, `EventHighlight`, `EventList`, `HomeCTA`, `HomeHero` |
| Layout | `AuthLayout`, `DashboardFocusLayout`, `FormFillLayout` |
| Pages | `Auth`, `Index`, `Event`, `EventDetail`, `Features`, `Docs`, `Error` |
| Dashboard Pages | Semua halaman Events (Index, Create, Edit, Show, Scan, Laporan, Registrants), Forms (Create, Index, Submissions), Profile, Recruitment |
| User Pages | EventDetail, EventRegistration, EventRegistrationPickForm, Events, Index, TeamInvitation |

### 7. Docker & Routing Tambahan (Harry_lbi)

- `EventDetail.vue`: Conditional routing untuk event registration links.
- `DashboardSidebar.vue`: Update link navigasi.
- `docker-compose.yml`: Penyesuaian konfigurasi.

---

## Testing

### Test baru yang ditambahkan

| File | Cakupan |
|------|---------|
| `tests/Feature/Forms/FormRegistrationTest.php` | 453+ baris test untuk skenario re-registrasi: single, bundle, team; rejection + re-submit; edge cases. |
| `tests/Feature/Auth/PasswordResetTest.php` | 102+ baris test untuk flow password reset lengkap. |
| `tests/Feature/Auth/OAuthGoogleEmailVerificationTest.php` | 98 baris test untuk sinkronisasi email verification Google OAuth. |

---

## File Baru

| File | Tipe |
|------|------|
| `app/Services/BundleSubmissionGrouper.php` | Backend service |
| `app/Http/Controllers/Dashboard/User/MemberDashboardController.php` | Controller |
| `app/Services/Registration/EventRegistrationCounter.php` | Backend service |
| `app/Services/Auth/PasswordResetService.php` | Backend service |
| `app/Http/Controllers/Auth/PasswordResetLinkController.php` | Controller |
| `app/Notifications/ResetPasswordNotification.php` | Notification |
| `resources/js/lib/routes.ts` | Frontend utility |
| `resources/js/components/modules/dashboard/FormBundleGroupsCardGridView.vue` | Vue component |
| `resources/js/components/modules/dashboard/FormBundleGroupDetailSheet.vue` | Vue component |
| `resources/js/types/event.d.ts` | TypeScript types |
| `IMPLEMENTATION_SUMMARY.md` | Dokumentasi |
| `MANUAL_TESTING_GUIDE.md` | Dokumentasi |

## File Dihapus

| File | Alasan |
|------|--------|
| `resources/js/lib/dashboardProfileRoutes.ts` | Digantikan `routes.ts` |

---

## Statistik Perubahan

- **100 file** termodifikasi
- **~4.020 baris** ditambahkan
- **~1.263 baris** dihapus
- **6 PR** di-merge (#53–#60, #62)
- **3 kontributor** aktif: zappto, Nafunnn, Harry_lbi, mbagusaditya

---

## Perbandingan dengan [`saptoChanges04-05-2026.md`](saptoChanges04-05-2026.md)

| Aspek | 4 Mei 2026 | 10 Juni 2026 (sesi ini) |
|-------|------------|--------------------------|
| Fokus | Konsolidasi M1–M4 + hapus Livewire/Filament | Bundle submissions, member dashboard, reusable routes |
| Submissions | Tombol Accept/Reject (stub) | Sistem bundle grouping lengkap + UI card grid + detail sheet |
| Dashboard user | Placeholder / dummy data | Data real dari `MemberDashboardController` |
| Routing frontend | Hardcoded path di masing-masing file | Sentralisasi di `routes.ts` (50+ file direfactor) |
| Auth | Redirect dasar | Password reset service, email verification sync, re-registration flow |
| Testing | Minimal | 650+ baris test baru (registration, password reset, OAuth) |

---

## Yang harus dijalankan developer setelah pull

1. **`composer install`** — ada service dan controller baru.
2. **`php artisan migrate`** — migration baru untuk re-registration.
3. **`npm install`** lalu **`npm run build`** — komponen dan routes baru.
4. **`php artisan optimize:clear`**
5. **`php artisan test`** — pastikan test suite hijau.

---

## Catatan untuk Tim

- **`routes.ts`** adalah satu-satunya tempat mendefinisikan URL frontend. Jangan hardcode path di komponen.
- **Bundle submission** sudah fungsional — bisa dipakai untuk review dan testing flow registrasi bundling.
- **Re-registrasi** menyimpan record rejected lama; pastikan reporting/export memperhitungkan ini.
- Jika menambahkan route baru, update `routes.ts` dan regenerate Wayfinder.

---

*Dokumen ini ditulis untuk kolaborasi tim; sumber kebenaran akhir tetap diff Git.*
