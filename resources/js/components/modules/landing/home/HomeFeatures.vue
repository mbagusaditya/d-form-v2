<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Card, CardContent } from '@/components/ui/card'

interface Feature {
    title: string
    desc: string
    accent?: boolean
}

const features: Feature[] = [
    {
        title: 'Drag & Drop Builder',
        desc: 'Susun pertanyaan secara visual. Tidak perlu coding — cukup geser dan lepas.',
        accent: true,
    },
    {
        title: 'Dasbor Real-time',
        desc: 'Lihat jumlah pendaftar, status acara, dan ringkasan data dari satu halaman.',
    },
    {
        title: 'Multi-Acara',
        desc: 'Kelola banyak acara sekaligus, masing-masing dengan formulir tersendiri.',
    },
    {
        title: 'Ekspor Data',
        desc: 'Unduh data peserta kapan saja untuk kebutuhan operasional tim Anda.',
    },
    {
        title: 'Responsif Total',
        desc: 'Tampil sempurna di semua perangkat — desktop, tablet, hingga ponsel.',
    },
    {
        title: 'Aman & Terverifikasi',
        desc: 'Data peserta dilindungi autentikasi dan validasi berlapis.',
    },
]

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.08 },
    )
    const el = document.getElementById('section-features')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="section-features" class="border-t border-border/30 py-20 md:py-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div
                :class="[
                    'mb-10 max-w-md transition-all duration-500',
                    visible ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0',
                ]"
            >
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">Keunggulan</p>
                <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">
                    Yang benar-benar Anda butuhkan
                </h2>
                <p class="mt-2 text-[13px] leading-relaxed text-muted-foreground sm:text-[14px]">
                    Bukan fitur berlebihan — hanya yang esensial untuk pendaftaran acara yang efektif.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="(feat, i) in features"
                    :key="feat.title"
                    :class="[
                        'group border-border/40 transition-all duration-400 hover:border-primary/20',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-5 opacity-0',
                    ]"
                    :style="{ transitionDelay: `${80 + i * 50}ms` }"
                >
                    <CardContent class="p-5">
                        <div
                            :class="[
                                'mb-3 size-1.5 rounded-full transition-colors duration-200',
                                feat.accent ? 'bg-primary' : 'bg-border group-hover:bg-primary/50',
                            ]"
                        />
                        <h3 class="text-[14px] font-semibold text-foreground">{{ feat.title }}</h3>
                        <p class="mt-1.5 text-[12px] leading-relaxed text-muted-foreground">{{ feat.desc }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>
