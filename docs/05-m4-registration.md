# M4 — Member Registration

**Scope:** This document covers the backend implementation for M4 (member form submission) and provides a front-end integration guide so the Vue/Inertia layer can be built without ambiguity.

**Related files:**

| Layer | File |
|-------|------|
| Guard service | `app/Services/Form/FormAccessGuard.php` |
| Access status enum | `app/Enums/FormAccessStatus.php` |
| Fill controller | `app/Http/Controllers/Dashboard/Events/Forms/FormFillController.php` |
| Submit controller | `app/Http/Controllers/Dashboard/Events/Forms/FormSubmissionController.php` |
| Submissions list (admin) | `app/Http/Controllers/Dashboard/Events/Forms/FormSubmissionsController.php` |
| Validation rules builder | `app/Services/Form/RulesBuilder.php` |
| Model | `app/Models/FormAnswer.php` |
| Routes | `routes/web/admin/event.php` |
| Migration (unique constraint) | `database/migrations/2026_05_02_100001_add_unique_to_form_answers.php` |

---

## 1. Routes

| Method | URI | Route name | Controller | Notes |
|--------|-----|-----------|------------|-------|
| `GET` | `/dashboard/events/{event}/forms/{form}/fill` | `dashboard.events.forms.fill` | `FormFillController` | Renders the form fill page |
| `POST` | `/dashboard/events/{event}/forms/{form}/submit` | `dashboard.forms.submission` | `FormSubmissionController` | Processes submission |
| `GET` | `/dashboard/events/{event}/forms/{form}/submissions` | `dashboard.events.forms.submissions` | `FormSubmissionsController` | Admin list of all submissions |

All routes are under the `auth` middleware — unauthenticated users are redirected to the login page.

---

## 2. Access guard (`FormAccessGuard`)

Before any fill or submit action is processed, `FormAccessGuard::check(Form, Event, User)` is called. It returns a `FormAccessStatus` enum value.

### Checks and priority order

| Priority | Status value | Condition | Admin exempt? |
|----------|-------------|-----------|---------------|
| 1 | `not_visible` | `form.visible_for` does not include `public` or `participant`, and user is not admin | No — admins always pass |
| 2 | `form_closed` | `form.closed_at` is set and is in the past | No — even admins see this state |
| 3 | `registration_not_open` | Current time is before `event.registration_start` or after `event.registration_end` | Yes |
| 4 | `quota_full` | `event.registered_count >= event.quota` (and quota is set) | Yes |
| 5 | `unsupported_registration_mode` | `forms.metadata.registration_mode` is `team` or `bundle` (M4b / M4c — not implemented for members yet) | Yes — admins may open the fill page for preview/testing |
| 6 | `already_submitted` | A `form_answers` row exists for this `(user_id, form_id)` pair | No |
| — | `allowed` | All checks passed | — |

Legacy forms with `metadata` null or `registration_mode` omitted are treated as **single** registrant mode.

`FormAccessStatus::isBlocked()` returns `true` for all values except `allowed`.

`FormAccessStatus::message()` returns a user-facing string suitable for a toast notification.

### Visibility rules (`visible_for`)

The `Form.visible_for` column is a JSON array of `EventFormVisibility` enum values:

| Value | Who can fill |
|-------|-------------|
| `public` | Any authenticated user |
| `participant` | Any authenticated user (full participant-gate is deferred to a future milestone) |
| `admin` | Only users with the `events.list` permission |
| *(empty)* | Any authenticated user (treated as public) |

---

## 3. Fill endpoint (`GET /fill`)

### Inertia page props

```ts
{
  event: { id: string; title: string }
  form: {
    id: string
    title: string
    description: string | null
    closed_at: string | null   // ISO-8601 or null
  }
  fields: IFormField[]         // ordered by `order` ascending
  submitUrl: string            // POST URL for the submit endpoint
  accessStatus: FormAccessStatus  // see section 2
  accessMessage: string        // human-readable reason, empty string when allowed
}
```

### Front-end behaviour by `accessStatus`

| `accessStatus` | Recommended UI behaviour |
|----------------|--------------------------|
| `allowed` | Render form normally |
| `already_submitted` | Show "You have already submitted" banner; hide the form |
| `form_closed` | Show "This form is closed" banner; hide the form |
| `registration_not_open` | Show "Registration is not currently open" banner; hide the form |
| `quota_full` | Show "Registration is full" banner; hide the form |
| `unsupported_registration_mode` | Explain that this registration type is not available yet; hide submit |
| `not_visible` | Show generic "You do not have access" banner; hide the form |

> The form fields are **always** returned regardless of `accessStatus` so the page can optionally render a preview. Only submit functionality should be gated.

---

## 4. Submit endpoint (`POST /submit`)

### Request format

The request must be sent as `multipart/form-data` (required when any `fileUpload` field is present). With Inertia's `useForm`, set `forceFormData: true`.

Field values are keyed by the field's `name` property (from `IFormField.name`):

```
field_name_1=value_1
field_name_2[]=value_a          // checkbox or multi-select
file_field=<binary>             // fileUpload
```

### Server-side validation

Validation rules are built dynamically from each `FormField.metadata.rules` object via `RulesBuilder`. The table below maps field types to the Laravel rules they produce:

| Field type | Possible Laravel rules |
|-----------|----------------------|
| `input` (text/number/tel) | `required\|nullable`, `min`, `max`, `regex` |
| `input` (email subtype) | + `email` |
| `textarea` | `required\|nullable`, `min`, `max` |
| `datePicker` | `required\|nullable`, `after_or_equal`, `before_or_equal` |
| `select` (single) | `required\|nullable` |
| `select` (is_multiple) | `required\|nullable`, `array` |
| `radio` | `required\|nullable`, `in:<options>` |
| `checkbox` | `required\|nullable`, `array`; each item: `in:<options>` |
| `fileUpload` | `required\|nullable`, `file`, `mimes:…`, `max:…` (kilobytes) |

If validation fails the server redirects back to the fill page with errors available via `$page.props.errors` (standard Laravel/Inertia pattern).

### Quota race-condition handling

The `registered_count` increment happens inside a DB transaction that acquires a `SELECT ... FOR UPDATE` lock on the event row. If two concurrent requests both pass the pre-transaction quota check, the second one will be rejected with a `QuotaExceededException`, which returns the user to the previous page with an error toast (`toast.type = 'error'`).

### Success response

On success, [`FormSubmissionController`](app/Http/Controllers/Dashboard/Events/Forms/FormSubmissionController.php) flashes a success toast and redirects:

- Users **with** the `events.view` permission (organizers) → `dashboard.events.show` for the event.
- Everyone else (typical members) → `dashboard.user.events.show` with the event `slug`.

In both cases the session receives:

```json
{
  "toast": { "type": "success", "message": "Your registration has been submitted successfully." }
}
```

### `answers` JSON structure stored in `form_answers.answers`

```jsonc
{
  "full_name": "Jane Doe",                    // input / textarea
  "birth_date": "1995-06-15",                 // datePicker
  "faculty": "Engineering",                   // select (single)
  "interests": ["design", "coding"],          // select (is_multiple) or checkbox
  "gender": "female",                         // radio
  "cv": "form-uploads/{formId}/filename.pdf", // fileUpload — relative path on `public` disk
  "optional_field": null                      // field not filled
}
```

File uploads are stored on the **`public`** disk under `storage/app/public/form-uploads/{formId}/`. The stored value in `answers` is the **relative path** (e.g. `form-uploads/abc123/resume.pdf`). To build a URL: `Storage::disk('public')->url($path)`.

---

## 5. Submissions list endpoint (`GET /submissions`)

### Authorization

Requires `events.view` permission (checked via `EventPolicy::view`). Members will receive a 403.

### Inertia page props

```ts
{
  event: { id: string; title: string }
  form:  { id: string; title: string }
  submissions: {
    data: Array<{
      id: string
      user: { id: string; name: string; email: string } | null
      answers: Record<string, unknown>
      submitted_at: string   // ISO-8601
    }>
    // Standard Laravel paginator meta (current_page, last_page, per_page, total, links, etc.)
  }
}
```

Paginated at **25 per page**. Pass `?page=N` to navigate pages.

### Front-end implementation guide

Render a table with columns: **Name**, **Email**, per-field answer columns (derive from the form's field definitions), and **Submitted at**. Link to a detail view or expandable row for the full `answers` object.

For file fields, build the URL as:
```ts
`/storage/${answer.answers[field.name]}`
// or use the backend URL helper via an extra prop if needed
```

---

## 6. Database schema changes

### `form_answers` table — unique constraint

Migration `2026_05_02_100001_add_unique_to_form_answers.php` adds:

```sql
UNIQUE KEY form_answers_user_form_unique (user_id, form_id)
```

This is a DB-level enforcement of the one-submission-per-user-per-form rule (application-level check already existed in the guard).

---

## 7. Front-end implementation checklist

### Fill page (`Dashboard/Events/Forms/Fill.vue`)

- [x] Uses `accessStatus` / `accessMessage` from the server ([`useFormFillPage`](resources/js/utils/composables/useFormFillPage.ts))
- [x] Submits with `forceFormData: true` for file fields

### Submissions page (`Dashboard/Events/Forms/Submissions.vue`)

- [x] Implemented; dinavigasi dari halaman admin form (`Show.vue`) ke `dashboard.events.forms.submissions`

### Types

- [x] `IFormSubmission` dan tipe terkait ada di [`resources/js/types/event.d.ts`](resources/js/types/event.d.ts) dan modul terkait; `FormAccessStatus` di [`resources/js/types/form.ts`](resources/js/types/form.ts) termasuk `unsupported_registration_mode`.

---

## 8. Error states reference

| Scenario | HTTP behaviour | Inertia / toast |
|----------|---------------|-----------------|
| Access blocked (any status) | Redirect to fill page | `toast.type = 'error'`, `toast.message = accessMessage` |
| Validation fails | Redirect to fill page | `$page.props.errors` populated per field |
| Quota race condition | Redirect back | `toast.type = 'error'`, quota message |
| Form not found / wrong event | 404 | — |
| Submissions page — insufficient permission | 403 | — |
