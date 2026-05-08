export function formatSubmissionDate(value: string): string {
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

export function submissionUserInitials(name: string): string {
    return name
        .split(' ')
        .map((w) => w[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
}

export function humanizeSubmissionKey(fieldLabelMap: Record<string, string>, value: string): string {
    return fieldLabelMap[value] || value.replace(/^field_/, '').replace(/_/g, ' ')
}

export function answerPreview(value: unknown): string {
    if (Array.isArray(value)) return value.map(String).join(', ')
    if (typeof value === 'string') return value
    if (value === null || value === undefined) return '—'
    if (typeof value === 'number' || typeof value === 'boolean') return String(value)
    return 'Jawaban terstruktur'
}

export function submissionFileUrl(value: unknown): string | null {
    return typeof value === 'string' && value.includes('/') ? `/storage/${value}` : null
}

export function submissionPaginationLabel(value: string): string {
    return value
        .replace('&laquo;', 'Sebelumnya')
        .replace('&raquo;', 'Berikutnya')
        .replace('Previous', 'Sebelumnya')
        .replace('Next', 'Berikutnya')
}

/** True when admin must not accept until the member confirms (team / bundle). */
export function submissionAdminAcceptBlocked(sub: {
    registration_role?: string | null
    member_confirmation_status?: string | null
}): boolean {
    return sub.registration_role === 'member' && sub.member_confirmation_status !== 'accepted'
}

/** Matches {@see App\Enums\FormAnswerReviewStatus} — pending answers can be reviewed. */
export function formSubmissionReviewIsPending(submission: {
    review_status?: 'pending' | 'accepted' | 'rejected' | null
}): boolean {
    return submission.review_status == null || submission.review_status === 'pending'
}

/** Label + kelas untuk badge status review (Bahasa Indonesia). */
export function submissionReviewBadge(
    status: 'pending' | 'accepted' | 'rejected' | null | undefined,
): { label: string; class: string } {
    if (status === 'accepted') {
        return {
            label: 'Diterima',
            class: 'border-success/30 bg-success/10 text-success',
        }
    }
    if (status === 'rejected') {
        return {
            label: 'Ditolak',
            class: 'border-destructive/25 bg-destructive/10 text-destructive',
        }
    }
    return {
        label: 'Menunggu review',
        class: 'border-warning/30 bg-warning/10 text-warning',
    }
}
