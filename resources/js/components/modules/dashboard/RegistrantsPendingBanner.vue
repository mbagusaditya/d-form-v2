<script setup lang="ts">
import { Clock } from 'lucide-vue-next'

defineProps<{
    pendingCount: number
    activeStatusTab: 'all' | 'pending' | 'accepted' | 'rejected'
}>()
</script>

<template>
    <div
        v-if="pendingCount > 0 && activeStatusTab !== 'pending'"
        class="flex items-start gap-3 rounded-2xl border border-warning/30 bg-warning/5 px-4 py-4 shadow-sm"
        role="status"
    >
        <div
            class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg border border-warning/25 bg-warning/10 text-warning-foreground"
        >
            <Clock class="size-4" aria-hidden="true" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-foreground">
                {{ pendingCount === 1 ? 'Satu pendaftar menunggu' : `${pendingCount} pendaftar menunggu` }} keputusan review.
            </p>
            <p class="mt-1 text-sm leading-relaxed text-muted-foreground">
                Gunakan tab <span class="font-medium text-foreground">Menunggu</span> di bagian filter untuk hanya menampilkan entri tersebut.
            </p>
        </div>
    </div>
</template>
