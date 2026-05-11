import type { FormFieldMetadataBag, FormFieldRules } from '@/types/form'

/**
 * Baca flag boolean dari metadata API/Laravel tanpa jebakan `Boolean("false") === true`.
 */
export function readMetaBoolean(meta: FormFieldMetadataBag, key: string): boolean {
    const v = meta[key]
    if (v === true || v === 1) return true
    if (v === false || v === 0 || v === null || v === undefined) return false
    if (typeof v === 'string') {
        const s = v.trim().toLowerCase()
        if (s === '' || s === 'false' || s === '0' || s === 'no' || s === 'off') return false
        if (s === 'true' || s === '1' || s === 'yes' || s === 'on') return true
    }
    return Boolean(v)
}

function isPlainObject(value: unknown): value is Record<string, unknown> {
    return Boolean(value) && typeof value === 'object' && !Array.isArray(value)
}

export function readFieldMetadata(field: IFormField): FormFieldMetadataBag {
    let m: unknown = field.metadata
    if (typeof m === 'string' && m.trim()) {
        try { m = JSON.parse(m) } catch { /* not valid JSON, keep as-is */ }
    }
    if (Array.isArray(m)) {
        m = m.find((item) => isPlainObject(item)) ?? {}
    }
    return isPlainObject(m) ? m : {}
}

export function readFieldRules(field: IFormField): FormFieldRules {
    const raw = readFieldMetadata(field).rules
    return isPlainObject(raw) ? (raw as FormFieldRules) : {}
}
