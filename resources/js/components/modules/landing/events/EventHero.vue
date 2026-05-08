<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Badge } from '@/components/ui/badge'
import LocalLottie from '@/components/core/LocalLottie.vue'

const show = ref(false)
onMounted(() => requestAnimationFrame(() => (show.value = true)))
</script>

<template>
    <section class="relative overflow-hidden pt-28 pb-16 md:pt-36 md:pb-24">
        <div
            class="pointer-events-none absolute -top-32 left-1/3 size-[500px] rounded-full bg-primary/[0.04] blur-[120px]"
            aria-hidden="true"
        />
        <div class="relative mx-auto max-w-5xl px-5 lg:px-8">
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
                <div
                    :class="['transition-all duration-700', show ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0']"
                >
                    <Badge variant="outline" class="mb-5 border-primary/20 bg-primary/[0.05] text-[11px] font-medium text-primary">
                        Acara
                    </Badge>
                    <h1 class="text-[1.85rem] font-semibold leading-[1.18] tracking-tight text-foreground sm:text-[2.5rem]">
                        Temukan acara <span class="text-primary">yang tersedia.</span>
                    </h1>
                    <p class="mt-5 max-w-[420px] text-[14px] leading-[1.7] text-muted-foreground">
                        Jelajahi daftar acara yang sedang membuka pendaftaran dan bergabunglah dengan mudah.
                    </p>
                </div>
                <div
                    :class="['transition-all delay-100 duration-700', show ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0']"
                >
                    <div class="rounded-2xl border border-border/40 bg-card/60 p-5 backdrop-blur-sm sm:p-7">
                        <LocalLottie name="eventsLive" :height="260" width="100%" :lazy="false" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
