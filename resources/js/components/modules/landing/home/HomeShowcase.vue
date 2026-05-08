<script setup lang="ts">
import { ref, onMounted } from 'vue'
import LocalLottie from '@/components/core/LocalLottie.vue'

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.12 },
    )
    const el = document.getElementById('section-showcase')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="section-showcase" class="py-20 md:py-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-14">
                <!-- Illustration -->
                <div
                    :class="[
                        'order-2 lg:order-1 transition-all duration-600',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0',
                    ]"
                >
                    <div class="rounded-xl border border-border/40 bg-card/50 p-5 sm:p-7">
                        <LocalLottie name="docsFlow" :height="220" width="100%" />
                    </div>
                </div>

                <!-- Text -->
                <div
                    :class="[
                        'order-1 lg:order-2 transition-all delay-75 duration-600',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0',
                    ]"
                >
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">Mengapa DForm?</p>
                    <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">
                        Semua kebutuhan pendaftaran, satu platform
                    </h2>
                    <p class="mt-3 text-[13px] leading-relaxed text-muted-foreground sm:text-[14px]">
                        DForm dibuat khusus untuk penyelenggara yang menginginkan alur sederhana —
                        dari pembuatan formulir hingga pengelolaan peserta.
                    </p>

                    <ul class="mt-5 flex flex-col gap-2.5">
                        <li
                            v-for="point in [
                                'Tanpa coding, langsung pakai',
                                'Pantau pendaftar secara real-time',
                                'Ekspor data kapan saja dibutuhkan',
                                'Formulir responsif di semua perangkat',
                            ]"
                            :key="point"
                            class="flex items-start gap-2.5 text-[13px] text-foreground/85"
                        >
                            <span class="mt-[7px] size-1 shrink-0 rounded-full bg-primary" />
                            {{ point }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>
