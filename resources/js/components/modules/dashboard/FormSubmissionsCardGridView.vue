<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Clock, FileText, Eye, CheckCircle2, XCircle } from 'lucide-vue-next'
import { formSubmissionReviewIsPending, submissionAdminAcceptBlocked, submissionReviewBadge, submissionUserInitials } from '@/lib/formSubmissionsUi'

defineProps<{
    submissions: IFormSubmission[]
    answerKeys: string[]
    formatDate: (v: string) => string
    humanizeKey: (v: string) => string
    answerPreview: (v: unknown) => string
    fileUrl: (v: unknown) => string | null
    isSubmissionReviewing: (submissionId: string) => boolean
}>()

defineEmits<{
    openDetail: [submission: IFormSubmission]
    review: [payload: { action: 'accept' | 'reject'; submission: IFormSubmission }]
}>()
</script>

<template>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
            v-for="(submission, idx) in submissions"
            :key="submission.id"
            class="app-surface animate-app-fade-in flex flex-col p-6"
            :style="{ animationDelay: `${idx * 100}ms` }"
        >
            <div class="flex items-start justify-between gap-3 border-b border-border pb-4">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <Avatar class="size-11 shrink-0 rounded-xl border border-border">
                        <AvatarFallback class="rounded-xl bg-primary/10 text-sm font-semibold text-primary">
                            {{ submissionUserInitials(submission.user?.name ?? 'An') }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-display truncate text-base font-bold tracking-[-0.015em] text-foreground">
                            {{ submission.user?.name ?? 'Tanpa nama' }}
                        </h4>
                        <p class="mt-1 truncate text-xs text-muted-foreground">
                            {{ submission.user?.email ?? 'Tidak ada email' }}
                        </p>
                    </div>
                </div>
                <Badge
                    variant="outline"
                    class="shrink-0 font-medium"
                    :class="submissionReviewBadge(submission.review_status).class"
                >
                    {{ submissionReviewBadge(submission.review_status).label }}
                </Badge>
            </div>

            <div class="flex-1 space-y-4 pt-5">
                <div v-for="key in answerKeys" :key="key" class="space-y-1.5">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted-foreground">
                        {{ humanizeKey(key) }}
                    </div>
                    <div class="text-xs leading-relaxed">
                        <div v-if="fileUrl(submission.answers[key])" class="flex items-center gap-2 text-primary">
                            <FileText class="size-4 shrink-0" />
                            <a
                                :href="fileUrl(submission.answers[key]) ?? undefined"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-medium underline underline-offset-4"
                            >
                                Buka lampiran
                            </a>
                        </div>
                        <span v-else class="line-clamp-2 break-words text-foreground/85">
                            {{ answerPreview(submission.answers[key]) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <div class="flex items-center justify-between text-[10px] font-semibold uppercase tracking-[0.14em] text-muted-foreground">
                    <span class="flex items-center gap-1.5"><Clock class="size-3" /> Waktu kirim</span>
                    <span class="text-foreground">{{ formatDate(submission.submitted_at) }}</span>
                </div>
                <div class="mt-6 flex flex-col gap-2">
                    <div v-if="formSubmissionReviewIsPending(submission)" class="grid grid-cols-2 gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            class="h-11 w-full gap-1.5 border-success/30 text-success hover:bg-success/10"
                            :disabled="isSubmissionReviewing(submission.id) || submissionAdminAcceptBlocked(submission)"
                            @click="$emit('review', { action: 'accept', submission })"
                        >
                            <CheckCircle2 class="size-4" />
                            Terima
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            class="h-11 w-full gap-1.5 border-destructive/30 text-destructive hover:bg-destructive/10"
                            :disabled="isSubmissionReviewing(submission.id)"
                            @click="$emit('review', { action: 'reject', submission })"
                        >
                            <XCircle class="size-4" />
                            Tolak
                        </Button>
                    </div>
                    <Button variant="outline" class="h-11 w-full gap-2" @click="$emit('openDetail', submission)">
                        <Eye class="size-4" />
                        Lihat detail
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
