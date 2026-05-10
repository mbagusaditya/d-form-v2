import { normalizeBannerSrc } from '@/components/modules/builder/formBanner'
import { readFieldMetadata, readFieldRules } from '@/lib/formFieldMetadata'
import type { FormFillOptionRow } from '@/types/form'

function listFromCsv(value: unknown): string[] {
    return typeof value === 'string' ? value.split(',').map((item) => item.trim()).filter(Boolean) : []
}

/**
 * API `type` di IFormField, atau `input_type` legacy (kolom DB) bila payload tidak dinormalisasi.
 */
export function formFieldApiType(field: IFormField | null): string | undefined {
    if (!field) {
        return undefined
    }
    const raw = field as IFormField & { input_type?: string }
    if (typeof raw.type === 'string' && raw.type !== '') {
        return raw.type
    }
    if (typeof raw.input_type === 'string' && raw.input_type !== '') {
        return raw.input_type === 'selectInput' ? 'select' : raw.input_type
    }
    return undefined
}

/** Mirrors builder type resolution in {@see useFormFillPage}. */
export function formFieldBuilderType(field: IFormField): string {
    const meta = readFieldMetadata(field)
    const value = meta.builderType
    if (typeof value === 'string') {
        return value
    }
    const api = formFieldApiType(field)
    if (field.name === 'form_banner' || api === 'banner') {
        return 'banner'
    }
    return api ?? ''
}

function fallbackOptionLabels(field: IFormField): string[] {
    const meta = readFieldMetadata(field)
    const direct = meta.options
    if (typeof direct === 'string') {
        return listFromCsv(direct)
    }
    const ruleOptions = readFieldRules(field).in as string | undefined
    return listFromCsv(ruleOptions)
}

/**
 * Options / image-choice rows for select, radio, checkbox — shared by fill UI, invitation review, and read-only display.
 */
export function getFormFieldOptionRows(field: IFormField): FormFillOptionRow[] {
    const oc = readFieldMetadata(field).optionChoices
    if (Array.isArray(oc)) {
        const rows: FormFillOptionRow[] = []
        for (const item of oc) {
            if (item && typeof item === 'object' && item !== null) {
                const typedItem = item as { type?: string; label?: string; imageUrl?: string }
                const type = (typedItem.type === 'image' ? 'image' : 'text') as 'text' | 'image'
                const label = String(typedItem.label ?? '').trim()
                const rawUrl = typeof typedItem.imageUrl === 'string' ? String(typedItem.imageUrl).trim() : ''

                rows.push({
                    type,
                    label: type === 'text' ? label : (label || 'Image Choice'),
                    imageSrc: rawUrl ? normalizeBannerSrc(rawUrl) : undefined,
                })
            }
        }
        if (rows.length > 0) {
            return rows
        }
    }
    return fallbackOptionLabels(field).map((label) => ({ type: 'text' as const, label }))
}
