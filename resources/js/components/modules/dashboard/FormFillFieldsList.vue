<script setup lang="ts">
/* eslint-disable vue/no-mutating-props -- ctx.answerForm is Inertia useForm state; assignments are intended field updates */
import { Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { DatePicker } from '@/components/ui/date-picker'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { ref, watch, type UnwrapNestedRefs } from 'vue'
import { Send, Star, ImagePlus, Upload, X } from 'lucide-vue-next'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import type { FormFillPageContext } from '@/utils/composables/useFormFillPage'

defineProps<{
    fields: IFormField[]
    eventId: string
    ctx: UnwrapNestedRefs<FormFillPageContext>
}>()

defineEmits<{
    submit: []
}>()

function textAnswer(ctx: UnwrapNestedRefs<FormFillPageContext>, name: string): string {
    const v = ctx.answerForm[name]
    return typeof v === 'string' ? v : ''
}

function setTextAnswer(ctx: UnwrapNestedRefs<FormFillPageContext>, name: string, value: string | number): void {
    ctx.answerForm[name] = String(value)
}

function slotStorageKey(
    ctx: UnwrapNestedRefs<FormFillPageContext>,
    field: IFormField,
    slot: { slotIndex: number | null },
): string {
    return ctx.answerKeyForSlot(field, slot.slotIndex)
}

const uploadLightboxOpen = ref(false)
const uploadLightboxSrc = ref<string | null>(null)
const uploadLightboxTitle = ref('')

function openUploadLightbox(src: string | undefined, title: string) {
    if (!src) return
    uploadLightboxSrc.value = src
    uploadLightboxTitle.value = title
    uploadLightboxOpen.value = true
}

watch(uploadLightboxOpen, (open) => {
    if (!open) {
        uploadLightboxSrc.value = null
        uploadLightboxTitle.value = ''
    }
})

function imageUploadFillReady(
    ctx: UnwrapNestedRefs<FormFillPageContext>,
    field: IFormField,
    slot: { slotIndex: number | null },
): boolean {
    const key = slotStorageKey(ctx, field, slot)
    return (
        ctx.builderType(field) === 'image_upload' && !!ctx.answerForm[key] && !!ctx.filePreviewUrls[key]
    )
}
</script>

<template>
    <form class="flex flex-col gap-6" @submit.prevent="$emit('submit')">
        <Card v-if="ctx.memberSlots > 0" class="overflow-hidden border-primary/30 bg-primary/5">
            <CardHeader class="pb-2 pt-4">
                <CardTitle class="text-sm font-semibold tracking-tight">
                    {{ ctx.registrationMode === 'bundle' ? 'Participant emails' : 'Team member emails' }}
                </CardTitle>
                <p class="text-xs leading-relaxed text-muted-foreground">
                    <template v-if="ctx.registrationMode === 'bundle'">
                        {{ ctx.memberSlots }} additional participant(s) must already have accounts. We email each person to
                        confirm their registration.
                    </template>
                    <template v-else>
                        {{ ctx.memberSlots }} teammate(s) must already have accounts. We will email each person to confirm.
                    </template>
                </p>
            </CardHeader>
            <CardContent class="flex flex-col gap-3 pb-4">
                <div v-for="slot in ctx.memberSlots" :key="slot" class="space-y-1.5">
                    <label class="text-xs font-medium text-foreground" :for="`team_member_emails_${slot}`">
                        Teammate {{ slot }} email <span class="text-destructive">*</span>
                    </label>
                    <Input
                        :id="`team_member_emails_${slot}`"
                        type="email"
                        autocomplete="email"
                        class="min-h-11"
                        :model-value="String((ctx.answerForm.team_member_emails as string[] | undefined)?.[slot - 1] ?? '')"
                        @update:model-value="
                            (v: string | number) => {
                                const arr = [...((ctx.answerForm.team_member_emails as string[]) ?? [])]
                                arr[slot - 1] = String(v)
                                ctx.answerForm.team_member_emails = arr
                            }
                        "
                    />
                    <p v-if="ctx.fieldError(`team_member_emails.${slot - 1}`)" class="text-xs font-medium text-destructive">
                        {{ ctx.fieldError(`team_member_emails.${slot - 1}`) }}
                    </p>
                </div>
                <p v-if="ctx.fieldError('team_member_emails')" class="text-xs font-medium text-destructive">
                    {{ ctx.fieldError('team_member_emails') }}
                </p>
            </CardContent>
        </Card>

        <template v-for="field in fields" :key="field.id">
            <div v-if="ctx.builderType(field) === 'heading'" class="rounded-2xl border border-border bg-primary/8 px-5 py-4 shadow-xs">
                <h2 class="font-display text-2xl font-bold tracking-[-0.025em] text-foreground">
                    {{ ctx.metadata(field).content || field.label }}
                </h2>
                <p v-if="field.description" class="mt-1.5 text-sm leading-relaxed text-muted-foreground">{{ field.description }}</p>
            </div>

            <div
                v-else-if="ctx.builderType(field) === 'paragraph'"
                class="rounded-2xl border border-border bg-card px-5 py-4 text-sm leading-relaxed text-muted-foreground shadow-xs"
            >
                {{ ctx.metadata(field).content || field.description || field.label }}
            </div>

            <hr v-else-if="ctx.builderType(field) === 'divider'" class="app-divider my-2" />

            <div v-else-if="ctx.builderType(field) === 'banner'" class="hidden" />

            <Card v-else class="overflow-hidden">
                <template v-for="slot in ctx.participationSlotsForField(field)" :key="`${field.id}-${slot.slotIndex ?? 'lead'}`">
                    <CardHeader
                        class="pb-2 pt-4"
                        :class="{
                            'border-t border-border': ctx.isBundleDuplicatableField(field) && slot.slotIndex !== null,
                        }"
                    >
                        <CardTitle class="flex flex-col items-start gap-0.5 text-sm font-semibold tracking-[-0.005em] text-foreground">
                            <span class="flex items-start gap-1">
                                {{ field.label }}
                                <span v-if="ctx.isRequired(field)" class="text-destructive">*</span>
                            </span>
                            <span v-if="slot.title" class="text-xs font-normal text-muted-foreground">{{ slot.title }}</span>
                        </CardTitle>
                        <p v-if="field.description && slot.slotIndex === null" class="text-xs leading-relaxed text-muted-foreground">
                            {{ field.description }}
                        </p>
                    </CardHeader>
                    <CardContent class="pb-4 pt-0">
                        <Input
                            v-if="['short_text', 'email', 'phone', 'number', 'time', 'input'].includes(ctx.builderType(field)) && !ctx.isMultipleSelect(field)"
                            :id="slotStorageKey(ctx, field, slot)"
                            :type="ctx.getInputSubtype(field)"
                            :placeholder="ctx.getPlaceholder(field)"
                            :model-value="textAnswer(ctx, slotStorageKey(ctx, field, slot))"
                            @update:model-value="setTextAnswer(ctx, slotStorageKey(ctx, field, slot), $event)"
                        />

                        <div v-else-if="ctx.builderType(field) === 'rating'" class="flex flex-col gap-3">
                            <div class="flex gap-1.5">
                                <button
                                    v-for="rating in Number(ctx.metadata(field).maxStars ?? 5)"
                                    :key="rating"
                                    type="button"
                                    class="rounded-lg p-1 transition-transform duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] active:scale-90"
                                    @click="ctx.answerForm[slotStorageKey(ctx, field, slot)] = String(rating)"
                                >
                                    <Star
                                        class="size-7 transition-colors"
                                        :class="
                                            Number(ctx.answerForm[slotStorageKey(ctx, field, slot)] || 0) >= rating
                                                ? 'fill-amber-400 text-amber-400'
                                                : 'text-muted-foreground/50'
                                        "
                                    />
                                </button>
                            </div>
                            <p class="text-xs text-muted-foreground">Choose one rating.</p>
                        </div>

                        <Textarea
                            v-else-if="ctx.builderType(field) === 'long_text' || ctx.builderType(field) === 'address' || field.type === 'textarea'"
                            :id="slotStorageKey(ctx, field, slot)"
                            :placeholder="ctx.getPlaceholder(field)"
                            :model-value="textAnswer(ctx, slotStorageKey(ctx, field, slot))"
                            @update:model-value="setTextAnswer(ctx, slotStorageKey(ctx, field, slot), $event)"
                            rows="4"
                        />

                        <DatePicker
                            v-else-if="ctx.builderType(field) === 'date' || field.type === 'datePicker'"
                            :id="slotStorageKey(ctx, field, slot)"
                            :model-value="textAnswer(ctx, slotStorageKey(ctx, field, slot))"
                            class="min-h-11"
                            @update:model-value="setTextAnswer(ctx, slotStorageKey(ctx, field, slot), $event)"
                        />

                        <div v-else-if="ctx.isCheckboxLike(field)" class="flex flex-col gap-2">
                            <label
                                v-for="row in ctx.getOptionRows(field)"
                                :key="row.label"
                                class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-card px-3 py-2 text-sm font-medium text-foreground shadow-xs transition-[border-color,background-color] duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-primary/30 hover:bg-muted/40"
                            >
                                <Checkbox
                                    :id="`${slotStorageKey(ctx, field, slot)}-${row.label}`"
                                    :checked="
                                        ((ctx.answerForm[slotStorageKey(ctx, field, slot)] as string[]) ?? []).includes(
                                            row.label,
                                        )
                                    "
                                    @update:checked="
                                        (value: boolean | 'indeterminate') =>
                                            ctx.onCheckboxToggle(slotStorageKey(ctx, field, slot), row.label, value === true)
                                    "
                                />
                                <div v-if="row.type === 'image' && row.imageSrc" class="size-16 shrink-0 overflow-hidden rounded-md border border-border">
                                    <img :src="row.imageSrc" alt="" class="size-full object-cover" />
                                </div>
                                <span v-else>{{ row.label }}</span>
                            </label>
                        </div>

                        <div v-else-if="ctx.isRadioLike(field)" class="flex flex-col gap-2">
                            <label
                                v-for="row in ctx.getOptionRows(field)"
                                :key="row.label"
                                class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-card px-3 py-2 text-sm font-medium text-foreground shadow-xs transition-[border-color,background-color] duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-primary/30 hover:bg-muted/40"
                            >
                                <input
                                    type="radio"
                                    :name="`${field.name}-${slot.slotIndex ?? 'lead'}`"
                                    :value="row.label"
                                    :checked="(ctx.answerForm[slotStorageKey(ctx, field, slot)] as string) === row.label"
                                    class="size-4 accent-primary"
                                    @change="() => (ctx.answerForm[slotStorageKey(ctx, field, slot)] = row.label)"
                                />
                                <div v-if="row.type === 'image' && row.imageSrc" class="size-16 shrink-0 overflow-hidden rounded-md border border-border">
                                    <img :src="row.imageSrc" alt="" class="size-full object-cover" />
                                </div>
                                <span v-else>{{ row.label }}</span>
                            </label>
                        </div>

                        <Select
                            v-else-if="ctx.builderType(field) === 'dropdown' || field.type === 'select'"
                            v-model="ctx.answerForm[slotStorageKey(ctx, field, slot)]"
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Select an option">
                                    <template v-if="ctx.answerForm[slotStorageKey(ctx, field, slot)]">
                                        <span class="flex items-center gap-2">
                                            <img
                                                v-if="ctx.getSelectedOptionRow(field, slotStorageKey(ctx, field, slot))?.imageSrc"
                                                :src="ctx.getSelectedOptionRow(field, slotStorageKey(ctx, field, slot))?.imageSrc"
                                                alt=""
                                                class="size-6 rounded border border-border object-cover"
                                            />
                                            {{ ctx.answerForm[slotStorageKey(ctx, field, slot)] }}
                                        </span>
                                    </template>
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="row in ctx.getOptionRows(field)" :key="row.label" :value="row.label">
                                    <span class="flex items-center gap-2">
                                        <img
                                            v-if="row.imageSrc"
                                            :src="row.imageSrc"
                                            alt=""
                                            class="size-7 rounded border border-border object-cover"
                                        />
                                        {{ row.label }}
                                    </span>
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <div
                            v-else-if="['file_upload', 'image_upload', 'fileUpload'].includes(ctx.builderType(field))"
                            class="relative overflow-hidden rounded-xl border border-dashed border-border bg-muted/20 transition-colors hover:border-primary/30"
                            :class="imageUploadFillReady(ctx, field, slot) ? 'p-0' : 'p-6 text-center hover:bg-muted/30'"
                        >
                            <!-- Full-bleed preview: image fills the dashed card -->
                            <div
                                v-if="imageUploadFillReady(ctx, field, slot)"
                                class="relative aspect-[4/3] w-full bg-muted/30"
                            >
                                <button
                                    type="button"
                                    class="absolute inset-0 z-10 flex w-full focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                                    @click="
                                        openUploadLightbox(
                                            ctx.filePreviewUrls[slotStorageKey(ctx, field, slot)],
                                            (ctx.answerForm[slotStorageKey(ctx, field, slot)] as File).name,
                                        )
                                    "
                                >
                                    <img
                                        :src="ctx.filePreviewUrls[slotStorageKey(ctx, field, slot)]"
                                        :alt="(ctx.answerForm[slotStorageKey(ctx, field, slot)] as File).name"
                                        class="h-full w-full object-cover"
                                    />
                                    <span class="sr-only">View full size</span>
                                </button>
                                <button
                                    type="button"
                                    class="absolute right-2 top-2 z-20 grid size-9 place-items-center rounded-full border border-border/80 bg-background/95 text-destructive shadow-md backdrop-blur-sm transition-transform hover:scale-105"
                                    :aria-label="`Remove ${(ctx.answerForm[slotStorageKey(ctx, field, slot)] as File).name}`"
                                    @click.stop="ctx.clearFileUpload(slotStorageKey(ctx, field, slot))"
                                >
                                    <X class="size-4" />
                                </button>
                                <div
                                    class="pointer-events-none absolute inset-x-0 bottom-0 z-20 flex flex-col items-center gap-1 bg-gradient-to-t from-black/60 via-black/25 to-transparent px-3 pb-3 pt-14 text-center"
                                >
                                    <label
                                        class="pointer-events-auto cursor-pointer rounded-full border border-white/20 bg-background/95 px-3 py-1.5 text-xs font-medium text-foreground shadow-sm backdrop-blur-sm transition-colors hover:bg-background"
                                    >
                                        Choose another file
                                        <input
                                            type="file"
                                            :accept="ctx.acceptValue(field)"
                                            class="sr-only"
                                            @change="ctx.onFileChange(slotStorageKey(ctx, field, slot), $event)"
                                            @click.stop
                                        />
                                    </label>
                                    <p class="text-[10px] font-medium text-white drop-shadow">Click image to view full size</p>
                                </div>
                            </div>

                            <!-- Empty upload dropzone -->
                            <div v-else class="flex flex-col items-center gap-2">
                                <div class="grid size-12 place-items-center rounded-full border border-border bg-card shadow-xs">
                                    <component
                                        :is="ctx.builderType(field) === 'image_upload' ? ImagePlus : Upload"
                                        class="size-5 text-muted-foreground"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-foreground">Click to upload</p>
                                    <p class="mt-0.5 text-[10px] text-muted-foreground">{{ ctx.fileHint(field) }}</p>
                                </div>
                                <input
                                    type="file"
                                    :accept="ctx.acceptValue(field)"
                                    class="absolute inset-0 cursor-pointer opacity-0"
                                    @change="ctx.onFileChange(slotStorageKey(ctx, field, slot), $event)"
                                />
                            </div>

                            <!-- Generic file name chip (non-image or no preview yet) -->
                            <div
                                v-if="
                                    ctx.answerForm[slotStorageKey(ctx, field, slot)] &&
                                    !imageUploadFillReady(ctx, field, slot)
                                "
                                class="relative z-10 mt-4 flex items-center justify-center gap-2 rounded-xl border border-border bg-card p-3 shadow-xs"
                            >
                                <span class="max-w-[200px] truncate text-xs font-medium text-foreground">{{
                                    (ctx.answerForm[slotStorageKey(ctx, field, slot)] as File).name
                                }}</span>
                                <button
                                    type="button"
                                    class="text-destructive transition-transform hover:scale-110"
                                    @click="ctx.clearFileUpload(slotStorageKey(ctx, field, slot))"
                                >
                                    <X class="size-4" />
                                </button>
                            </div>
                        </div>

                        <p
                            v-if="ctx.fieldError(slotStorageKey(ctx, field, slot))"
                            class="mt-2 text-xs font-medium text-destructive"
                        >
                            {{ ctx.fieldError(slotStorageKey(ctx, field, slot)) }}
                        </p>
                    </CardContent>
                </template>
            </Card>
        </template>

        <div class="mb-20 flex items-center justify-end gap-3 border-t border-border pt-6">
            <Button variant="ghost" size="lg" as-child>
                <Link :href="`/user/dashboard/events/${eventId}`">Cancel</Link>
            </Button>
            <Button type="submit" size="lg" :disabled="ctx.answerForm.processing" class="gap-2">
                <Send class="size-4" />
                Submit registration
            </Button>
        </div>

        <Dialog v-model:open="uploadLightboxOpen">
            <DialogContent
                :show-close-button="false"
                overlay-class="bg-black/45 backdrop-blur-sm"
                class="w-auto max-w-[min(calc(100vw-2rem),96rem)] gap-0 rounded-none border-0 bg-transparent p-0 shadow-none outline-none sm:max-w-[min(calc(100vw-2rem),96rem)]"
            >
                <div v-if="uploadLightboxSrc" class="flex flex-col items-center gap-3 px-1 pb-1 pt-1">
                    <img
                        :src="uploadLightboxSrc"
                        :alt="uploadLightboxTitle"
                        class="max-h-[min(85vh,calc(100dvh-4rem))] w-auto max-w-[min(calc(100vw-3rem),96rem)] object-contain"
                    />
                    <p class="max-w-[min(calc(100vw-3rem),96rem)] truncate text-center text-sm font-medium text-white">
                        {{ uploadLightboxTitle }}
                    </p>
                </div>
            </DialogContent>
        </Dialog>
    </form>
</template>
