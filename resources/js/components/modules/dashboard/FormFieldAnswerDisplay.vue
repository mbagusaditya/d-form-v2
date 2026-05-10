<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue'
import { normalizeBannerSrc } from '@/components/modules/builder/formBanner'
import { getFormFieldOptionRows, formFieldApiType, formFieldBuilderType } from '@/lib/formFieldOptions'
import { readFieldMetadata } from '@/lib/formFieldMetadata'
import { Download, FileText, Maximize2, Image as ImageIcon } from 'lucide-vue-next'

const props = defineProps<{
    /** When null, file detection uses a light heuristic (e.g. form-uploads paths). */
    field: IFormField | null
    value: unknown
}>()

const lightboxUrl = ref<string | null>(null)

function onEscapeClose(e: KeyboardEvent): void {
    if (e.key === 'Escape') {
        closeLightbox()
    }
}

function closeLightbox(): void {
    lightboxUrl.value = null
}

function openLightbox(url: string): void {
    lightboxUrl.value = url
}

watch(lightboxUrl, (v) => {
    if (v) {
        window.addEventListener('keydown', onEscapeClose)
    } else {
        window.removeEventListener('keydown', onEscapeClose)
    }
})

onUnmounted(() => {
    window.removeEventListener('keydown', onEscapeClose)
})

function plainText(): string {
    const v = props.value
    if (v == null || v === '') {
        return '—'
    }
    if (Array.isArray(v)) {
        return v.length ? v.map(String).join(', ') : '—'
    }
    return String(v)
}

const publicFileUrl = computed((): string | null => {
    if (typeof props.value !== 'string') {
        return null
    }
    const t = props.value.trim()
    if (!t) {
        return null
    }
    const norm = normalizeBannerSrc(t)
    if (!norm) {
        return null
    }
    return norm
})

function isImageHref(href: string): boolean {
    const path = (href.split('?')[0] ?? '').toLowerCase()
    return /\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i.test(path)
}

function basenameFromHref(href: string | null): string {
    if (!href) {
        return 'berkas'
    }
    try {
        const path = new URL(href, window.location.origin).pathname
        const last = path.split('/').filter(Boolean).pop() ?? 'berkas'
        return decodeURIComponent(last) || 'berkas'
    } catch {
        const seg = href.split('/').filter(Boolean).pop() ?? 'berkas'
        try {
            return decodeURIComponent(seg) || 'berkas'
        } catch {
            return seg || 'berkas'
        }
    }
}

const storedFileLabel = computed(() => basenameFromHref(publicFileUrl.value))

const treatsAsFile = computed((): boolean => {
    if (!props.field) {
        if (typeof props.value !== 'string') {
            return false
        }
        const s = props.value.trim()
        return s.startsWith('form-uploads/') || /^https?:\/\//i.test(s) || s.startsWith('/storage/')
    }
    if (formFieldApiType(props.field) === 'fileUpload') {
        return true
    }
    const bt = formFieldBuilderType(props.field)
    return bt === 'file_upload' || bt === 'image_upload' || bt === 'fileUpload'
})

const preferImagePreview = computed((): boolean => {
    if (!publicFileUrl.value) {
        return false
    }
    if (!props.field) {
        return isImageHref(publicFileUrl.value)
    }
    const bt = formFieldBuilderType(props.field)
    if (bt === 'image_upload') {
        return true
    }
    return isImageHref(publicFileUrl.value)
})

const isMultipleChoice = computed((): boolean => {
    if (!props.field) {
        return false
    }
    const meta = readFieldMetadata(props.field)
    const bt = formFieldBuilderType(props.field)
    return props.field.type === 'checkbox' || (props.field.type === 'select' && Boolean(meta.is_multiple)) || bt === 'checkbox'
})

const isRadioLike = computed((): boolean => {
    if (!props.field) {
        return false
    }
    const bt = formFieldBuilderType(props.field)
    return props.field.type === 'radio' || bt === 'radio' || bt === 'yes_no'
})

const isSelectSingle = computed((): boolean => {
    if (!props.field) {
        return false
    }
    return props.field.type === 'select' && !isMultipleChoice.value
})

const matchedOptionRows = computed(() => {
    if (!props.field) {
        return []
    }
    const rows = getFormFieldOptionRows(props.field)
    const v = props.value
    if (Array.isArray(v)) {
        const set = new Set(v.map(String))
        return rows.filter((r) => set.has(r.label))
    }
    if (typeof v === 'string' && v !== '') {
        return rows.filter((r) => r.label === v)
    }
    return []
})

const showChoiceMedia = computed(
    () => isMultipleChoice.value || isRadioLike.value || isSelectSingle.value,
)

async function downloadStoredFile(url: string, suggestedName: string): Promise<void> {
    const name = suggestedName || basenameFromHref(url)
    try {
        const res = await fetch(url, { credentials: 'same-origin' })
        if (!res.ok) {
            throw new Error('fetch failed')
        }
        const blob = await res.blob()
        const href = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = href
        a.download = name
        document.body.appendChild(a)
        a.click()
        a.remove()
        URL.revokeObjectURL(href)
    } catch {
        const a = document.createElement('a')
        a.href = url
        a.download = name
        a.target = '_blank'
        a.rel = 'noopener noreferrer'
        document.body.appendChild(a)
        a.click()
        a.remove()
    }
}
</script>

<template>
    <div class="space-y-3">
        <template v-if="treatsAsFile && publicFileUrl">
            <!-- Gambar: pratinjau + lightbox -->
            <div v-if="preferImagePreview" class="space-y-3">
                <button
                    type="button"
                    class="group relative w-full overflow-hidden rounded-xl border border-border/80 bg-muted/20 text-left ring-offset-background transition hover:border-primary/35 hover:bg-muted/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    @click="openLightbox(publicFileUrl)"
                >
                    <div class="flex items-center justify-center px-2 py-3 sm:py-4">
                        <img
                            :src="publicFileUrl"
                            alt="Pratinjau lampiran"
                            class="max-h-[min(14rem,40vh)] max-w-full rounded-lg object-contain shadow-sm transition group-hover:opacity-[0.97]"
                            loading="lazy"
                        />
                    </div>
                    <div
                        class="flex items-center justify-center gap-2 border-t border-border/60 bg-card/40 px-3 py-2.5 text-[0.8125rem] font-medium text-muted-foreground group-hover:text-foreground"
                    >
                        <Maximize2 class="size-3.5 shrink-0 opacity-70" aria-hidden="true" />
                        <span>Buka ukuran penuh</span>
                    </div>
                </button>
                <p class="text-[0.75rem] leading-relaxed text-muted-foreground">
                    Tampilan memenuhi layar. Tutup dengan tombol silang, klik area gelap, atau tombol
                    <kbd class="rounded border border-border bg-muted px-1 py-px text-[0.65rem]">Esc</kbd>.
                </p>
            </div>

            <!-- Berkas non-gambar: nama file + unduh -->
            <button
                v-else
                type="button"
                class="flex w-full max-w-full items-center gap-3 rounded-xl border border-border/80 bg-card/40 px-3.5 py-3 text-left transition hover:border-primary/30 hover:bg-muted/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                @click="downloadStoredFile(publicFileUrl, storedFileLabel)"
            >
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <FileText class="size-5" aria-hidden="true" />
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block text-[0.6875rem] font-medium text-muted-foreground"> Nama berkas </span>
                    <span class="mt-0.5 block truncate text-[0.875rem] font-semibold text-foreground" :title="storedFileLabel">
                        {{ storedFileLabel }}
                    </span>
                </span>
                <span
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-border/70 bg-background px-2.5 py-1.5 text-[0.75rem] font-medium text-primary"
                >
                    <Download class="size-3.5" aria-hidden="true" />
                    Unduh
                </span>
            </button>
        </template>

        <ul v-else-if="showChoiceMedia && matchedOptionRows.length > 0" class="flex flex-col gap-2.5">
            <li
                v-for="row in matchedOptionRows"
                :key="row.label"
                class="flex items-center gap-3 rounded-xl border border-border/70 bg-card/40 px-3.5 py-2.5"
            >
                <img
                    v-if="row.imageSrc"
                    :src="row.imageSrc"
                    alt=""
                    class="size-11 shrink-0 rounded-lg border border-border object-cover"
                    loading="lazy"
                />
                <span v-else class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-muted/50 text-muted-foreground">
                    <ImageIcon class="size-5" aria-hidden="true" />
                </span>
                <span class="text-sm font-medium leading-snug text-foreground">{{ row.label }}</span>
            </li>
        </ul>

        <p v-else class="whitespace-pre-wrap text-[0.875rem] leading-[1.6] text-foreground">
            {{ plainText() }}
        </p>

        <Teleport to="body">
            <div
                v-if="lightboxUrl"
                class="fixed inset-0 z-[300] flex flex-col items-center justify-center bg-black/88 p-4 backdrop-blur-[2px]"
                role="dialog"
                aria-modal="true"
                aria-label="Pratinjau gambar ukuran penuh"
                @click.self="closeLightbox"
            >
                <button
                    type="button"
                    class="absolute top-3 right-3 z-10 flex size-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-lg font-light text-white backdrop-blur-sm transition hover:bg-white/20"
                    aria-label="Tutup"
                    @click="closeLightbox"
                >
                    ×
                </button>
                <img
                    :src="lightboxUrl"
                    alt="Lampiran gambar"
                    class="max-h-[min(92dvh,900px)] max-w-full rounded-lg object-contain shadow-2xl"
                    @click.stop
                />
            </div>
        </Teleport>
    </div>
</template>
