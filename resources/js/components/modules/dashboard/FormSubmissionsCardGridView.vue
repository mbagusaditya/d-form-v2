<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Clock, Eye, CheckCircle2, XCircle, Inbox } from 'lucide-vue-next'
import { formSubmissionReviewIsPending, submissionAdminAcceptBlocked, submissionReviewBadge } from '@/lib/formSubmissionsUi'
import { userAvatarSeed } from '@/lib/userAvatarFallback'
import UserAvatarFallback from '@/components/modules/user/UserAvatarFallback.vue'

defineProps<{
    submissions: IFormSubmission[]
    formatDate: (v: string) => string
    isSubmissionReviewing: (submissionId: string) => boolean
}>()

defineEmits<{
    openDetail: [submission: IFormSubmission]
    review: [payload: { action: 'accept' | 'reject'; submission: IFormSubmission }]
}>()
</script>

<template>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div
            v-for="(submission, idx) in submissions"
            :key="submission.id"
            class="app-surface animate-app-fade-in flex flex-col rounded-2xl border border-border/80 p-5 shadow-sm"
            :style="{ animationDelay: `${idx * 100}ms` }"
        >
            <div class="flex items-start justify-between gap-3 border-b border-border/70 pb-4">
                <div class="flex min-w-0 flex-1 items-start gap-3.5">
                    <UserAvatarFallback
                        :src="submission.user?.avatar ?? null"
                        :seed="userAvatarSeed(submission.user)"
                        avatar-class="size-12 shrink-0 rounded-2xl border border-border/80 shadow-sm"
                        fallback-round-class="rounded-2xl"
                    />
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h4 class="truncate text-[0.9375rem] font-semibold leading-tight tracking-tight text-foreground">
                            {{ submission.user?.name ?? 'Tanpa nama' }}
                        </h4>
                        <p class="mt-1.5 truncate text-[0.8125rem] leading-snug text-muted-foreground">
                            {{ submission.user?.email ?? 'Tidak ada email' }}
                        </p>
                    </div>
                </div>
                <Badge
                    variant="outline"
                    class="shrink-0 rounded-full px-2.5 py-0.5 text-[0.6875rem] font-medium"
                    :class="submissionReviewBadge(submission.review_status).class"
                >
                    {{ submissionReviewBadge(submission.review_status).label }}
                </Badge>
            </div>

            <div class="mt-4 flex flex-1 flex-col">
                <div
                    class="flex gap-3 rounded-xl border border-dashed border-border/90 bg-muted/25 px-3.5 py-3 text-[0.8125rem] leading-snug text-muted-foreground"
                >
                    <Inbox class="mt-0.5 size-4 shrink-0 text-muted-foreground/80" aria-hidden="true" />
                    <p>
                        <span class="font-medium text-foreground/90">Sudah ada pengiriman</span>
                        dari kontak di atas. Ringkasan isian dan lampiran tidak ditampilkan di kartu ini — gunakan
                        <span class="font-medium text-foreground/90">Lihat detail</span> untuk membuka semua jawaban.
                    </p>
                </div>

                <div
                    class="mt-4 flex items-start justify-between gap-3 rounded-lg bg-muted/15 px-1 py-2 text-[0.8125rem] tabular-nums text-muted-foreground"
                >
                    <span class="flex items-center gap-2 font-medium text-muted-foreground/90">
                        <Clock class="size-3.5 shrink-0 opacity-70" aria-hidden="true" />
                        Dikirim
                    </span>
                    <time class="text-right font-medium leading-snug text-foreground" :datetime="submission.submitted_at">
                        {{ formatDate(submission.submitted_at) }}
                    </time>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-2.5">
                <div v-if="formSubmissionReviewIsPending(submission)" class="grid grid-cols-2 gap-2.5">
                    <Button
                        type="button"
                        variant="outline"
                        class="h-10 gap-1.5 rounded-xl border-success/35 text-[0.8125rem] font-medium text-success hover:bg-success/10"
                        :disabled="isSubmissionReviewing(submission.id) || submissionAdminAcceptBlocked(submission)"
                        @click="$emit('review', { action: 'accept', submission })"
                    >
                        <CheckCircle2 class="size-4 shrink-0" />
                        Terima
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        class="h-10 gap-1.5 rounded-xl border-destructive/35 text-[0.8125rem] font-medium text-destructive hover:bg-destructive/10"
                        :disabled="isSubmissionReviewing(submission.id)"
                        @click="$emit('review', { action: 'reject', submission })"
                    >
                        <XCircle class="size-4 shrink-0" />
                        Tolak
                    </Button>
                </div>
                <Button
                    variant="secondary"
                    class="h-10 gap-2 rounded-xl text-[0.8125rem] font-medium shadow-none"
                    @click="$emit('openDetail', submission)"
                >
                    <Eye class="size-4 shrink-0 opacity-80" />
                    Lihat detail
                </Button>
            </div>
        </div>
    </div>
</template>
