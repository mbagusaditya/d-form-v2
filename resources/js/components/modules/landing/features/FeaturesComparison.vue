<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Card, CardContent } from '@/components/ui/card'

const rows = [
    { feature: 'Form Builder Visual', dform: true, manual: false },
    { feature: 'Dasbor Real-time', dform: true, manual: false },
    { feature: 'Multi-Acara', dform: true, manual: false },
    { feature: 'Ekspor Data', dform: true, manual: true },
    { feature: 'Responsif Otomatis', dform: true, manual: false },
    { feature: 'Keamanan Berlapis', dform: true, manual: false },
]

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.1 },
    )
    const el = document.getElementById('features-compare')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="features-compare" class="py-20 md:py-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div :class="['mb-10 max-w-md transition-all duration-500', visible ? 'opacity-100' : 'opacity-0']">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">Perbandingan</p>
                <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">DForm vs cara manual</h2>
            </div>

            <Card :class="['overflow-hidden border-border/40 transition-all duration-500', visible ? 'opacity-100' : 'opacity-0']">
                <CardContent class="p-0">
                    <div class="grid grid-cols-3 border-b border-border/40 bg-muted/20 px-5 py-3 text-[12px] font-semibold text-muted-foreground">
                        <span>Fitur</span>
                        <span class="text-center text-primary">DForm</span>
                        <span class="text-center">Manual</span>
                    </div>
                    <div
                        v-for="(row, i) in rows"
                        :key="row.feature"
                        :class="['grid grid-cols-3 px-5 py-3 text-[13px]', i < rows.length - 1 ? 'border-b border-border/30' : '']"
                    >
                        <span class="text-foreground/90">{{ row.feature }}</span>
                        <span class="text-center text-primary">{{ row.dform ? '✓' : '—' }}</span>
                        <span class="text-center text-muted-foreground">{{ row.manual ? '✓' : '—' }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </section>
</template>
