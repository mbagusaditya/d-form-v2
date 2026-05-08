<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import LocalLottie from '@/components/core/LocalLottie.vue'

const show = ref(false)
onMounted(() => requestAnimationFrame(() => (show.value = true)))
</script>

<template>
    <section class="relative overflow-hidden pt-28 pb-20 md:pt-36 md:pb-28">
        <!-- Background accent -->
        <div
            class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 size-[600px] rounded-full bg-primary/[0.04] blur-[120px]"
            aria-hidden="true"
        />

        <div class="relative mx-auto max-w-5xl px-5 lg:px-8">
            <div class="grid items-center gap-14 lg:grid-cols-[1.1fr_0.9fr] lg:gap-16">
                <!-- Copy -->
                <div
                    :class="[
                        'transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]',
                        show ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0',
                    ]"
                >
                    <Badge
                        variant="outline"
                        class="mb-5 border-primary/20 bg-primary/[0.05] text-[11px] font-medium text-primary"
                    >
                        Formulir Pendaftaran Acara
                    </Badge>

                    <h1
                        class="text-[1.85rem] font-semibold leading-[1.18] tracking-tight text-foreground sm:text-[2.5rem] lg:text-[2.8rem]"
                    >
                        Pendaftaran acara,<br class="hidden sm:block" />
                        <span class="text-primary">jadi lebih simpel.</span>
                    </h1>

                    <p class="mt-5 max-w-[420px] text-[14px] leading-[1.7] text-muted-foreground sm:text-[15px]">
                        Buat formulir, kumpulkan data peserta, dan kelola semuanya
                        dari satu dasbor — dalam hitungan menit, tanpa kerumitan.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Button as-child size="lg" class="h-10 rounded-lg px-6 text-[13px] font-semibold">
                            <a href="/auth/register">Mulai Sekarang</a>
                        </Button>
                        <Button
                            as-child
                            variant="outline"
                            size="lg"
                            class="h-10 rounded-lg border-border/60 px-6 text-[13px] font-medium"
                        >
                            <a href="/docs">Lihat Dokumentasi</a>
                        </Button>
                    </div>
                </div>

                <!-- Illustration -->
                <div
                    :class="[
                        'transition-all delay-100 duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]',
                        show ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0',
                    ]"
                >
                    <div class="rounded-2xl border border-border/40 bg-card/60 p-5 backdrop-blur-sm sm:p-7">
                        <LocalLottie name="stepsFlow" :height="260" width="100%" :lazy="false" />
                    </div>
                </div>
            </div>

            <!-- Trust strip -->
            <div
                :class="[
                    'mt-16 flex flex-wrap items-center gap-8 border-t border-border/40 pt-8 transition-all delay-200 duration-600',
                    show ? 'opacity-100' : 'opacity-0',
                ]"
            >
                <div v-for="stat in [
                    { num: '500+', label: 'Acara dibuat' },
                    { num: '10K+', label: 'Respons dikumpulkan' },
                    { num: '99.9%', label: 'Uptime' },
                ]" :key="stat.label" class="flex items-baseline gap-2">
                    <span class="text-lg font-bold tabular-nums tracking-tight text-foreground">{{ stat.num }}</span>
                    <span class="text-[12px] text-muted-foreground">{{ stat.label }}</span>
                </div>
            </div>
        </div>
    </section>
</template>
