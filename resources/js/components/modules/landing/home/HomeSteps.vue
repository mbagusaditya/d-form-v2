<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import LocalLottie from '@/components/core/LocalLottie.vue'
import type { LottieName } from '@/lib/lotties'

interface Step {
    num: string
    title: string
    desc: string
    lottie: LottieName
}

const steps: Step[] = [
    {
        num: '01',
        title: 'Buat acara',
        desc: 'Isi nama, tanggal, dan kapasitas. Cukup informasi dasar.',
        lottie: 'eventsLive',
    },
    {
        num: '02',
        title: 'Rancang formulir',
        desc: 'Tambahkan field, atur urutan, cek pratinjau sebelum terbit.',
        lottie: 'featureModules',
    },
    {
        num: '03',
        title: 'Terbitkan & pantau',
        desc: 'Bagikan link, lihat respons masuk secara real-time.',
        lottie: 'analyticsPulse',
    },
]

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.15 },
    )
    const el = document.getElementById('section-steps')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="section-steps" class="py-20 md:py-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div
                :class="[
                    'mb-12 max-w-md transition-all duration-500',
                    visible ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0',
                ]"
            >
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">Cara Kerja</p>
                <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">
                    Tiga langkah untuk memulai
                </h2>
                <p class="mt-2 text-[13px] leading-relaxed text-muted-foreground sm:text-[14px]">
                    Tidak perlu tutorial panjang — ikuti tiga langkah ini dan acara Anda siap menerima pendaftar.
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <Card
                    v-for="(step, i) in steps"
                    :key="step.num"
                    :class="[
                        'group overflow-hidden border-border/50 transition-all duration-500 hover:border-primary/25',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0',
                    ]"
                    :style="{ transitionDelay: `${120 + i * 80}ms` }"
                >
                    <CardContent class="flex flex-col items-start p-5">
                        <span class="text-[32px] font-bold leading-none text-primary/10">{{ step.num }}</span>
                        <div class="my-4 flex w-full justify-center">
                            <LocalLottie :name="step.lottie" :height="120" :width="120" />
                        </div>
                        <h3 class="text-[14px] font-semibold text-foreground">{{ step.title }}</h3>
                        <p class="mt-1.5 text-[12px] leading-relaxed text-muted-foreground">{{ step.desc }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>
