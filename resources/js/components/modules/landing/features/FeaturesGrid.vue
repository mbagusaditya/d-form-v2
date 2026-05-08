<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Card, CardContent } from '@/components/ui/card'

const features = [
    { title: 'Form Builder Visual', desc: 'Drag & drop field, atur urutan, dan pratinjau real-time sebelum dipublikasikan.', accent: true },
    { title: 'Dasbor Penyelenggara', desc: 'Ringkasan acara, jumlah pendaftar, dan status pendaftaran dari satu halaman.' },
    { title: 'Multi-Acara', desc: 'Kelola banyak acara sekaligus — masing-masing dengan formulir tersendiri.' },
    { title: 'Ekspor Data', desc: 'Unduh data peserta dalam format yang siap digunakan oleh tim Anda.' },
    { title: 'Responsif', desc: 'Formulir tampil optimal di desktop, tablet, maupun ponsel.' },
    { title: 'Keamanan Data', desc: 'Data peserta dilindungi autentikasi dan validasi berlapis.' },
]

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.08 },
    )
    const el = document.getElementById('features-grid')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="features-grid" class="border-t border-border/30 py-20 md:py-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div
                :class="['mb-10 max-w-md transition-all duration-500', visible ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0']"
            >
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">Keunggulan</p>
                <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">
                    Fitur yang benar-benar penting
                </h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="(f, i) in features"
                    :key="f.title"
                    :class="[
                        'group border-border/40 transition-all duration-400 hover:border-primary/20',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-5 opacity-0',
                    ]"
                    :style="{ transitionDelay: `${80 + i * 50}ms` }"
                >
                    <CardContent class="p-5">
                        <div :class="['mb-3 size-1.5 rounded-full', f.accent ? 'bg-primary' : 'bg-border group-hover:bg-primary/50']" />
                        <h3 class="text-[14px] font-semibold text-foreground">{{ f.title }}</h3>
                        <p class="mt-1.5 text-[12px] leading-relaxed text-muted-foreground">{{ f.desc }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>
</template>
