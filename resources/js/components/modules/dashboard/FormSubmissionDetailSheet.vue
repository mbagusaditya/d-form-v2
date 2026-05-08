<script setup lang="ts">
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Clock, FileText, CheckCircle2, XCircle } from 'lucide-vue-next'
import { formSubmissionReviewIsPending, submissionAdminAcceptBlocked, submissionReviewBadge, submissionUserInitials } from '@/lib/formSubmissionsUi'
import { cn } from '@/lib/utils'

defineProps<{
    submission: IFormSubmission | null
    allAnswerKeys: string[]
    formatDate: (v: string) => string
    humanizeKey: (v: string) => string
    answerPreview: (v: unknown) => string
    fileUrl: (v: unknown) => string | null
    isSubmissionReviewing: (submissionId: string) => boolean
}>()

const open = defineModel<boolean>('open', { required: true })

defineEmits<{
    review: [payload: { action: 'accept' | 'reject'; submission: IFormSubmission }]
}>()
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            :class="
                cn(
                    '!flex flex-col gap-0 overflow-hidden p-0 sm:max-w-lg',
                    /* Layar kecil + tablet potret: layar penuh */
                    'max-md:top-0 max-md:right-0 max-md:bottom-0 max-md:left-0 max-md:h-[100dvh] max-md:max-h-[100dvh] max-md:w-full max-md:max-w-none max-md:translate-x-0 max-md:translate-y-0 max-md:rounded-none max-md:border-0',
                    /* Md ke atas: modal tengah (bawaan DialogContent) */
                    'md:top-[50%] md:left-[50%] md:h-auto md:max-h-[min(90vh,56rem)] md:w-full md:translate-x-[-50%] md:translate-y-[-50%] md:rounded-lg md:border',
                )
            "
        >
            <div class="shrink-0 border-b border-border px-5 pb-5 pt-6 pr-14 md:px-6">
                <div class="flex items-start gap-4">
                    <Avatar class="size-14 shrink-0 rounded-2xl border border-border">
                        <AvatarFallback class="rounded-2xl bg-primary/10 text-lg font-semibold text-primary">
                            {{ submissionUserInitials(submission?.user?.name ?? 'An') }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-display text-xl font-bold tracking-[-0.02em] text-foreground md:text-2xl">
                                Detail pengiriman
                            </h2>
                            <Badge
                                v-if="submission"
                                variant="outline"
                                class="font-medium"
                                :class="submissionReviewBadge(submission.review_status).class"
                            >
                                {{ submissionReviewBadge(submission.review_status).label }}
                            </Badge>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Pengirim:
                            <span class="font-medium text-foreground">{{ submission?.user?.name ?? 'Tanpa nama' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 space-y-8 overflow-y-auto px-5 py-6 pb-28 md:px-6 md:pb-24">
                <section class="space-y-4">
                    <h5 class="text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Data pengirim</h5>
                    <div class="app-surface-soft space-y-4 p-5">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-muted-foreground">Email</span>
                            <span class="text-sm font-semibold text-foreground">{{ submission?.user?.email ?? '—' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-muted-foreground">Waktu kirim</span>
                            <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                                <Clock class="size-4 text-muted-foreground" />
                                <span>{{ submission ? formatDate(submission.submitted_at) : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="space-y-4">
                    <h5 class="text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Jawaban formulir</h5>
                    <p class="text-xs leading-relaxed text-muted-foreground">
                        Semua field tercantum di bawah. File dapat dibuka di tab baru.
                    </p>
                    <div class="grid grid-cols-1 gap-3">
                        <div
                            v-for="key in allAnswerKeys"
                            :key="key"
                            class="app-surface flex flex-col gap-2 p-5"
                        >
                            <span class="app-kicker">
                                {{ humanizeKey(key) }}
                            </span>
                            <div class="mt-1">
                                <a
                                    v-if="fileUrl(submission?.answers[key])"
                                    :href="fileUrl(submission?.answers[key]) ?? undefined"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2.5 font-semibold text-primary decoration-primary/30 underline underline-offset-4 transition-colors hover:decoration-primary"
                                >
                                    <div class="grid size-10 place-items-center rounded-lg border border-primary/20 bg-primary/10">
                                        <FileText class="size-5" />
                                    </div>
                                    <span>Unduh / buka lampiran</span>
                                </a>
                                <p v-else class="whitespace-pre-wrap text-sm leading-relaxed text-foreground">
                                    {{ answerPreview(submission?.answers[key]) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="submission" class="space-y-3 border-t border-border pt-6">
                    <h5 class="text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Keputusan review</h5>
                    <template v-if="formSubmissionReviewIsPending(submission)">
                        <p class="text-xs leading-relaxed text-muted-foreground">
                            Pilih terima atau tolak. Perubahan tersimpan di server dan daftar di halaman pengiriman akan ikut
                            diperbarui.
                        </p>
                        <div class="grid grid-cols-2 gap-2 md:flex md:flex-wrap">
                            <Button
                                type="button"
                                class="h-11 w-full gap-2 border-success/30 text-success hover:bg-success/10 md:min-w-[120px] md:flex-1"
                                variant="outline"
                                :disabled="isSubmissionReviewing(submission.id) || submissionAdminAcceptBlocked(submission)"
                                @click="$emit('review', { action: 'accept', submission })"
                            >
                                <CheckCircle2 class="size-4" />
                                Terima
                            </Button>
                            <Button
                                type="button"
                                class="h-11 w-full gap-2 border-destructive/30 text-destructive hover:bg-destructive/10 md:min-w-[120px] md:flex-1"
                                variant="outline"
                                :disabled="isSubmissionReviewing(submission.id)"
                                @click="$emit('review', { action: 'reject', submission })"
                            >
                                <XCircle class="size-4" />
                                Tolak
                            </Button>
                        </div>
                    </template>
                    <div v-else class="space-y-2">
                        <Badge
                            variant="outline"
                            class="font-medium"
                            :class="submissionReviewBadge(submission.review_status).class"
                        >
                            {{ submissionReviewBadge(submission.review_status).label }}
                        </Badge>
                        <p class="text-sm leading-relaxed text-muted-foreground">
                            <template v-if="submission.review_status === 'accepted'">
                                <span v-if="submission.reviewed_at">Ditinjau {{ formatDate(submission.reviewed_at) }}</span>
                                <span v-if="submission.reviewer"> · {{ submission.reviewer.name }}</span>
                            </template>
                            <template v-else-if="submission.review_status === 'rejected'">
                                <span v-if="submission.reviewed_at">Ditinjau {{ formatDate(submission.reviewed_at) }}</span>
                                <span v-if="submission.reviewer"> · {{ submission.reviewer.name }}</span>
                            </template>
                        </p>
                    </div>
                </section>
            </div>
        </DialogContent>
    </Dialog>
</template>
