<script setup lang="ts">
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { DatePicker, DateTimePicker } from '@/components/ui/date-picker'
import TipTapEditor from '@/components/modules/dashboard/events/TipTapEditor.vue'
import EventMultiValuePicker from '@/components/modules/dashboard/events/EventMultiValuePicker.vue'
import { Upload, X, Save, Send } from 'lucide-vue-next'
import { store as storeEvent, update as updateEvent } from '@/actions/App/Http/Controllers/Dashboard/Events/EventController'
import { showEventValidationToast } from '@/lib/eventValidationToast'
import { eventHeroBannerContainerClass } from '@/lib/eventBannerAspect'
import {
    formatIntegerId,
    formatPriceId,
    parsePriceInput,
    parseQuotaInput,
    sanitizeQuotaTyping,
} from '@/lib/indonesianNumericInput'

export type EventDashboardFormVariant = 'create' | 'edit'

const props = defineProps<{
    variant: EventDashboardFormVariant
    /** Edit: wajib. Create: tidak dipakai. */
    event?: IEvent
    /** Create: opsional (fallback default). Edit: wajib dari halaman. */
    options?: { categories: { value: string; label: string }[]; sessions: { value: string; label: string }[] }
}>()

const defaultSessions = [
    { value: 'general', label: 'General' },
    { value: 'programming', label: 'Programming' },
    { value: 'network', label: 'Networking' },
    { value: 'media_creative', label: 'Media Creative' },
    { value: 'data', label: 'Data' },
]

const defaultCategories = [
    { value: 'rkt', label: 'RKT' },
    { value: 'non-rkt', label: 'NON RKT' },
    { value: 'recruitment', label: 'Recruitment' },
    { value: 'etc', label: 'Etc' },
]

const sessions = computed(() =>
    props.variant === 'edit' && props.options
        ? props.options.sessions
        : (props.options?.sessions ?? defaultSessions),
)

const categories = computed(() =>
    props.variant === 'edit' && props.options
        ? props.options.categories
        : (props.options?.categories ?? defaultCategories),
)

function toTokenList(v: unknown): string[] {
    if (Array.isArray(v)) return v.map((s) => String(s).trim()).filter(Boolean)
    if (typeof v === 'string') return v.split(',').map((s) => s.trim()).filter(Boolean)
    return []
}

function buildFormPayload():
    | {
          title: string
          description: string
          location: string
          start_date: string
          end_date: string
          registration_start: string
          registration_end: string
          quota: number
          price: number
          session: string
          category: string
          banner: File | null
          publish: boolean
      }
    | {
          _method: 'PUT'
          title: string
          description: string
          location: string
          start_date: string
          end_date: string
          registration_start: string
          registration_end: string
          quota: number
          price: number
          session: string
          category: string
          banner: File | null
          publish: boolean
      } {
    if (props.variant === 'create') {
        return {
            title: '',
            description: '',
            location: '',
            start_date: '',
            end_date: '',
            registration_start: '',
            registration_end: '',
            quota: 100,
            price: 0,
            session: '',
            category: '',
            banner: null,
            publish: false,
        }
    }

    const e = props.event!
    const initialCategories = toTokenList(e.category)
    const initialSessions = toTokenList(e.session)

    return {
        _method: 'PUT' as const,
        title: e.title,
        description: e.description,
        location: e.location,
        start_date: e.start_date,
        end_date: e.end_date,
        registration_start: e.registration_start.replace(/\+.*$/, '').slice(0, 16),
        registration_end: e.registration_end.replace(/\+.*$/, '').slice(0, 16),
        quota: e.quota,
        price: e.price,
        session: initialSessions.join(','),
        category: initialCategories.join(','),
        banner: null,
        publish: e.status === 'published',
    }
}

const form = useForm(buildFormPayload())

const bannerHelpPrimary = computed(() =>
    props.variant === 'create' ? 'Upload banner acara' : 'Ganti banner acara',
)

const bannerEmptyTitle = computed(() =>
    props.variant === 'create' ? 'Unggah atau seret gambar ke sini' : 'Unggah banner baru',
)

const bannerEmptyHint = computed(() =>
    props.variant === 'create' ? 'PNG, JPG, WebP - maks. 10MB' : 'PNG, JPG, WebP - maks. 10MB',
)

const bannerFootnote = computed(() =>
    props.variant === 'create' ? 'Area aman di tengah' : 'Area aman di tengah - simpan untuk menerapkan',
)

const classificationDescription = computed(() =>
    props.variant === 'create'
        ? 'Sesi (divisi) dan kategori dipakai untuk filter di dashboard. Bisa lebih dari satu.'
        : 'Sesi dan kategori untuk filter internal. Kombinasikan beberapa nilai bila perlu.',
)

const sessionPickerId = computed(() => (props.variant === 'create' ? 'field-session' : 'edit-field-session'))

const categoryPickerId = computed(() => (props.variant === 'create' ? 'field-category' : 'edit-field-category'))

/** Sesi & kategori: perilaku input sama (multi, daftar + ketik). */
const multiValueFieldHint = 'Pilih dari daftar atau ketik; boleh lebih dari satu.'

const primaryActionLabel = computed(() => (props.variant === 'create' ? 'Terbitkan' : 'Simpan & terbitkan'))

const secondaryActionLabel = computed(() => (props.variant === 'create' ? 'Simpan draf' : 'Simpan perubahan'))

const bannerPreview = ref<string | null>(
    props.variant === 'edit' && props.event?.banner_url ? props.event.banner_url : null,
)

const isDragging = ref(false)

const quotaDisplay = ref(
    form.quota > 0 ? formatIntegerId(form.quota) : '',
)

const priceDisplay = ref(Number(form.price) > 0 ? formatPriceId(Number(form.price)) : '')

function onQuotaInput(v: string | number): void {
    const s = sanitizeQuotaTyping(String(v))
    quotaDisplay.value = s
    form.quota = parseQuotaInput(s)
}

function onQuotaBlur(): void {
    const q = parseQuotaInput(quotaDisplay.value)
    form.quota = q
    quotaDisplay.value = q > 0 ? formatIntegerId(q) : ''
}

function onPriceInput(v: string | number): void {
    const s = String(v).replace(/[^\d.,]/g, '')
    priceDisplay.value = s
    form.price = parsePriceInput(s)
}

function onPriceBlur(): void {
    const p = parsePriceInput(priceDisplay.value)
    form.price = p
    priceDisplay.value = p > 0 ? formatPriceId(p) : ''
}

function commitQuotaPriceFromFields(): void {
    form.quota = parseQuotaInput(quotaDisplay.value)
    form.price = parsePriceInput(priceDisplay.value)
}

function handleBannerChange(e: Event): void {
    const input = e.target as HTMLInputElement
    if (input.files?.[0]) {
        form.banner = input.files[0]
        bannerPreview.value = URL.createObjectURL(input.files[0])
    }
}

function handleDrop(e: DragEvent): void {
    isDragging.value = false
    const file = e.dataTransfer?.files[0]
    if (file && file.type.startsWith('image/')) {
        form.banner = file
        bannerPreview.value = URL.createObjectURL(file)
    }
}

function removeBanner(): void {
    form.banner = null
    bannerPreview.value = null
}

function submitForm(publish: boolean): void {
    form.publish = publish
    commitQuotaPriceFromFields()
    if (typeof form.start_date === 'string') form.start_date = form.start_date.trim()
    if (typeof form.end_date === 'string') form.end_date = form.end_date.trim()
    if (typeof form.registration_start === 'string') form.registration_start = form.registration_start.trim()
    if (typeof form.registration_end === 'string') form.registration_end = form.registration_end.trim()
    form.transform((data) => ({
        ...data,
        category: toTokenList(data.category),
        session: toTokenList(data.session),
    }))

    const url = props.variant === 'create' ? storeEvent().url : updateEvent(props.event!.id).url

    form.post(url, {
        forceFormData: true,
        onSuccess: () => {
            if (publish) {
                toast.success(
                    props.variant === 'create'
                        ? 'Acara berhasil dipublikasikan.'
                        : 'Acara diperbarui dan dipublikasikan.',
                )
            } else {
                toast.success(props.variant === 'create' ? 'Disimpan sebagai draf.' : 'Perubahan disimpan.')
            }
        },
        onError: (errors) => showEventValidationToast(errors),
    })
}
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-12 lg:items-start">
        <div class="flex flex-col gap-6 lg:col-span-7">
            <Card class="rounded-xl border shadow-xs">
                <CardHeader class="pb-4">
                    <CardTitle class="text-base">Informasi utama</CardTitle>
                    <CardDescription>Judul, deskripsi, banner, dan lokasi yang terlihat peserta.</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        <Label for="title" class="text-sm font-medium">Judul acara</Label>
                        <Input id="title" v-model="form.title" placeholder="Contoh: Bootcamp Web 2026" />
                        <p v-if="form.errors.title" class="text-destructive text-xs">{{ form.errors.title }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label class="text-sm font-medium">Deskripsi</Label>
                        <TipTapEditor v-model="form.description" />
                        <p v-if="form.errors.description" class="text-destructive text-xs">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label class="text-sm font-medium">Banner</Label>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="rounded-full border border-primary/20 bg-primary/8 px-2.5 py-1 text-[10px] font-semibold text-primary">
                                Target 3:1
                            </span>
                            <span class="rounded-full border border-border bg-muted/50 px-2.5 py-1 text-[10px] font-semibold text-muted-foreground">
                                1920 x 640 px
                            </span>
                            <span class="rounded-full border border-amber-500/25 bg-amber-500/10 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:text-amber-300">
                                Safe center
                            </span>
                        </div>
                        <div class="overflow-hidden rounded-xl border-2 border-border bg-muted/25 shadow-sm">
                            <div :class="eventHeroBannerContainerClass()">
                                <template v-if="bannerPreview">
                                    <img
                                        :src="bannerPreview"
                                        alt="Pratinjau banner"
                                        class="absolute inset-0 size-full object-cover"
                                    />
                                    <div
                                        class="pointer-events-none absolute inset-x-0 top-0 h-[45%] bg-gradient-to-b from-black/40 to-transparent"
                                    />
                                    <span
                                        class="absolute top-3 left-3 rounded-md bg-background/92 px-2 py-1 text-[10px] font-semibold tracking-wide text-foreground uppercase shadow-sm backdrop-blur-md"
                                    >
                                        {{ bannerHelpPrimary }}
                                    </span>
                                    <div
                                        class="pointer-events-none absolute inset-y-[18%] left-[18%] right-[18%] rounded-xl border border-white/80 shadow-[0_0_0_999px_rgba(0,0,0,0.18)]"
                                        aria-hidden="true"
                                    />
                                    <div class="absolute top-3 right-3 flex gap-2">
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            class="h-9 text-xs shadow-md"
                                            type="button"
                                            @click="($refs.bannerInput as HTMLInputElement)?.click()"
                                        >
                                            Ganti
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="icon"
                                            class="size-9 shadow-md"
                                            type="button"
                                            @click="removeBanner"
                                        >
                                            <X class="size-4" />
                                        </Button>
                                    </div>
                                </template>
                                <div
                                    v-else
                                    :class="[
                                        'absolute inset-0 flex cursor-pointer flex-col items-center justify-center px-5 py-8 text-center transition-colors',
                                        isDragging ? 'border-primary bg-primary/8' : 'hover:bg-muted/40',
                                    ]"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave="isDragging = false"
                                    @drop.prevent="handleDrop"
                                    @click="($refs.bannerInput as HTMLInputElement)?.click()"
                                >
                                    <div class="bg-muted flex size-12 items-center justify-center rounded-xl shadow-inner">
                                        <Upload class="text-muted-foreground size-5" />
                                    </div>
                                    <p class="mt-3 text-sm font-medium">{{ bannerEmptyTitle }}</p>
                                    <p class="text-muted-foreground mt-1 max-w-xs text-xs">
                                        {{ bannerEmptyHint }}
                                    </p>
                                    <div class="mt-4 w-full max-w-[260px] rounded-lg border border-border bg-background/70 p-2">
                                        <div class="relative aspect-[3/1] overflow-hidden rounded-md border border-dashed border-primary/45 bg-primary/8">
                                            <div class="absolute inset-y-[18%] left-[18%] right-[18%] rounded border border-primary/70 bg-primary/10" />
                                        </div>
                                        <div class="mt-1.5 flex items-center justify-between text-[10px] font-medium text-muted-foreground">
                                            <span>3:1 banner</span>
                                            <span>safe center</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input
                                ref="bannerInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleBannerChange"
                            />
                            <p class="border-t border-border bg-muted/20 px-3 py-2 text-xs font-medium text-muted-foreground">
                                {{ bannerFootnote }}
                            </p>
                        </div>
                        <p v-if="form.errors.banner" class="text-destructive text-xs">{{ form.errors.banner }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label for="location" class="text-sm font-medium">Lokasi</Label>
                        <Input
                            id="location"
                            v-model="form.location"
                            placeholder="Mis. Online — Zoom, atau Semarang — Auditorium A"
                        />
                        <p v-if="form.errors.location" class="text-destructive text-xs">{{ form.errors.location }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="flex flex-col gap-6 lg:col-span-5">
            <Card class="rounded-xl border shadow-xs">
                <CardHeader class="pb-4">
                    <CardTitle class="text-base font-semibold tracking-tight">Jadwal acara & kapasitas</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-5">
                    <div class="space-y-3 rounded-xl border border-border/80 bg-muted/20 p-4">
                        <p class="text-xs font-semibold leading-snug tracking-tight text-foreground">
                            Tanggal pelaksanaan acara
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <Label for="start_date" class="text-xs font-medium text-foreground">Mulai acara</Label>
                                <DatePicker id="start_date" v-model="form.start_date" />
                                <p v-if="form.errors.start_date" class="text-destructive text-xs">
                                    {{ form.errors.start_date }}
                                </p>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <Label for="end_date" class="text-xs font-medium text-foreground">Selesai acara</Label>
                                <DatePicker id="end_date" v-model="form.end_date" />
                                <p v-if="form.errors.end_date" class="text-destructive text-xs">
                                    {{ form.errors.end_date }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 rounded-xl border border-border/80 bg-muted/20 p-4">
                        <p class="text-xs font-semibold leading-snug tracking-tight text-foreground">
                            Buka & tutup pendaftaran
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <Label for="registration_start" class="text-xs font-medium text-foreground">
                                    Pendaftaran dibuka
                                </Label>
                                <DateTimePicker id="registration_start" v-model="form.registration_start" />
                                <p v-if="form.errors.registration_start" class="text-destructive text-xs">
                                    {{ form.errors.registration_start }}
                                </p>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <Label for="registration_end" class="text-xs font-medium text-foreground">
                                    Pendaftaran ditutup
                                </Label>
                                <DateTimePicker id="registration_end" v-model="form.registration_end" />
                                <p v-if="form.errors.registration_end" class="text-destructive text-xs">
                                    {{ form.errors.registration_end }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <Label for="quota" class="text-xs font-medium">Kuota</Label>
                            <Input
                                id="quota"
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                class="h-9 text-xs tabular-nums"
                                :model-value="quotaDisplay"
                                placeholder="contoh: 500"
                                @update:model-value="onQuotaInput"
                                @blur="onQuotaBlur"
                            />
                            <p v-if="form.errors.quota" class="text-destructive text-xs">{{ form.errors.quota }}</p>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <Label for="price" class="text-xs font-medium">Harga (Rp)</Label>
                            <Input
                                id="price"
                                type="text"
                                inputmode="decimal"
                                autocomplete="off"
                                class="h-9 text-xs tabular-nums"
                                :model-value="priceDisplay"
                                placeholder="contoh: 1.500.000"
                                @update:model-value="onPriceInput"
                                @blur="onPriceBlur"
                            />
                            <p v-if="form.errors.price" class="text-destructive text-xs">{{ form.errors.price }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="rounded-xl border shadow-xs">
                <CardHeader class="pb-4">
                    <CardTitle class="text-base">Klasifikasi</CardTitle>
                    <CardDescription>
                        {{ classificationDescription }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-6">
                    <div class="grid gap-6 md:grid-cols-2 md:gap-8">
                        <EventMultiValuePicker
                            :id="sessionPickerId"
                            v-model="form.session"
                            :options="sessions"
                            label="Sesi / divisi"
                            :description="multiValueFieldHint"
                            :error="form.errors.session"
                        />
                        <EventMultiValuePicker
                            :id="categoryPickerId"
                            v-model="form.category"
                            :options="categories"
                            label="Kategori"
                            :description="multiValueFieldHint"
                            :error="form.errors.category"
                        />
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
                <Button type="button" class="sm:flex-1" :disabled="form.processing" @click="submitForm(true)">
                    <Send class="mr-2 size-4" />
                    {{ primaryActionLabel }}
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    class="sm:flex-1"
                    :disabled="form.processing"
                    @click="submitForm(false)"
                >
                    <Save class="mr-2 size-4" />
                    {{ secondaryActionLabel }}
                </Button>
            </div>
        </div>
    </div>
</template>
