<script setup lang="ts">
import { ref, onMounted } from 'vue'
import LocalLottie from '@/components/core/LocalLottie.vue'
import { Blocks } from 'lucide-vue-next'

const show = ref(false)
onMounted(() => requestAnimationFrame(() => (show.value = true)))
</script>

<template>
    <section class="relative isolate overflow-hidden pt-32 pb-16 md:pt-44 md:pb-24">
        <div
            class="pointer-events-none absolute -top-40 left-1/2 -z-10 size-[800px] -translate-x-1/2 rounded-full bg-primary/[0.06] blur-[140px]"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute inset-0 -z-10 app-noise opacity-30"
            aria-hidden="true"
        />

        <div class="relative mx-auto max-w-7xl px-6 lg:px-10">
            <div class="grid items-center gap-14 lg:grid-cols-2 lg:gap-20">
                <div
                    :class="[
                        'transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]',
                        show ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0',
                    ]"
                >
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/[0.06] px-4 py-1.5 text-xs font-semibold tracking-wide text-primary"
                    >
                        <Blocks class="size-3.5" />
                        Fitur Platform
                    </span>

                    <h1
                        class="mt-7 font-display text-3xl font-bold leading-[1.1] tracking-tight text-foreground sm:text-4xl lg:text-5xl text-balance"
                    >
                        Setiap fitur punya tujuan,
                        <span class="bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent">
                            tidak ada yang berlebihan.
                        </span>
                    </h1>

                    <p class="mt-6 max-w-lg text-base leading-relaxed text-muted-foreground md:text-lg md:leading-relaxed text-pretty">
                        DForm dirancang dengan prinsip kesederhanaan. Setiap fitur hadir
                        untuk menyelesaikan masalah nyata dalam alur pendaftaran acara —
                        bukan sekadar memenuhi daftar panjang.
                    </p>
                </div>

                <div
                    :class="[
                        'transition-all delay-150 duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]',
                        show ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0',
                    ]"
                >
                    <div class="overflow-hidden rounded-2xl border border-border/30 bg-gradient-to-b from-card to-muted/20 p-6 shadow-sm sm:p-8">
                        <LocalLottie name="featuresHero" :height="300" width="100%" :lazy="false" :speed="0.85" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
