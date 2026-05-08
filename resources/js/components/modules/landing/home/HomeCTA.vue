<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import LocalLottie from '@/components/core/LocalLottie.vue'

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.2 },
    )
    const el = document.getElementById('section-cta')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="section-cta" class="pb-20 md:pb-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div
                :class="[
                    'relative overflow-hidden rounded-2xl border border-primary/12 bg-gradient-to-br from-primary/[0.03] to-transparent px-6 py-14 text-center transition-all duration-600 sm:px-10 md:py-18',
                    visible ? 'translate-y-0 opacity-100' : 'translate-y-5 opacity-0',
                ]"
            >
                <div class="relative mx-auto max-w-sm">
                    <LocalLottie name="successCheck" :height="64" :width="64" class="mx-auto mb-5" />

                    <h2 class="text-[1.35rem] font-semibold tracking-tight text-foreground sm:text-[1.6rem]">
                        Siap mencoba?
                    </h2>
                    <p class="mt-2 text-[13px] leading-relaxed text-muted-foreground sm:text-[14px]">
                        Buat akun gratis dan rancang formulir acara pertama Anda sekarang.
                    </p>

                    <div class="mt-7 flex flex-col justify-center gap-2.5 sm:flex-row">
                        <Button as-child size="lg" class="h-10 rounded-lg px-7 text-[13px] font-semibold">
                            <a href="/auth/register">Daftar Gratis</a>
                        </Button>
                        <Button
                            as-child
                            variant="ghost"
                            size="lg"
                            class="h-10 rounded-lg px-5 text-[13px] font-medium text-muted-foreground"
                        >
                            <a href="/docs">Baca Dokumentasi</a>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
