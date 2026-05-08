<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion'

const faqs = [
    {
        q: 'Apa itu DForm?',
        a: 'DForm adalah platform web untuk membuat formulir pendaftaran acara, mengumpulkan data peserta, dan mengelola semuanya dari satu dasbor yang sederhana.',
    },
    {
        q: 'Apakah gratis?',
        a: 'Ya. Anda bisa langsung membuat acara dan formulir tanpa biaya apa pun setelah mendaftar.',
    },
    {
        q: 'Bagaimana cara membuat formulir?',
        a: 'Setelah membuat acara, buka Form Builder. Tambahkan field pertanyaan dengan drag & drop, atur mana yang wajib diisi, lalu publikasikan.',
    },
    {
        q: 'Bisa mengelola beberapa acara?',
        a: 'Tentu. Setiap acara memiliki formulir dan pendaftar tersendiri. Beralih antar acara dari satu dasbor.',
    },
    {
        q: 'Apakah data peserta aman?',
        a: 'Data dilindungi autentikasi dan validasi berlapis. Hanya penyelenggara berwenang yang bisa mengakses.',
    },
    {
        q: 'Bisa ekspor data?',
        a: 'Ya. Fitur ekspor tersedia sehingga tim bisa mengolah data pendaftaran sesuai kebutuhan.',
    },
    {
        q: 'Siapa yang cocok pakai DForm?',
        a: 'Panitia kampus, komunitas, organisasi, dan tim internal yang butuh alur pendaftaran simpel dan terstruktur.',
    },
]

const visible = ref(false)
onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => { if (e?.isIntersecting) { visible.value = true; obs.disconnect() } },
        { threshold: 0.08 },
    )
    const el = document.getElementById('section-faq')
    if (el) obs.observe(el)
})
</script>

<template>
    <section id="section-faq" class="border-t border-border/30 py-20 md:py-28">
        <div class="mx-auto max-w-5xl px-5 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:gap-14">
                <!-- Heading — sticky on desktop -->
                <div
                    :class="[
                        'lg:sticky lg:top-28 lg:self-start transition-all duration-500',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0',
                    ]"
                >
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">FAQ</p>
                    <h2 class="mt-2 text-[1.5rem] font-semibold tracking-tight text-foreground sm:text-[1.75rem]">
                        Pertanyaan umum
                    </h2>
                    <p class="mt-2 text-[13px] leading-relaxed text-muted-foreground sm:text-[14px]">
                        Jawaban singkat untuk hal-hal yang paling sering ditanyakan tentang DForm.
                    </p>
                </div>

                <!-- Accordion -->
                <div
                    :class="[
                        'transition-all delay-75 duration-500',
                        visible ? 'translate-y-0 opacity-100' : 'translate-y-5 opacity-0',
                    ]"
                >
                    <Accordion type="single" collapsible class="w-full">
                        <AccordionItem
                            v-for="(faq, i) in faqs"
                            :key="faq.q"
                            :value="`faq-${i}`"
                            class="border-border/50"
                        >
                            <AccordionTrigger
                                class="py-4 text-left text-[13px] font-medium leading-snug hover:no-underline data-[state=open]:text-primary sm:text-[14px]"
                            >
                                {{ faq.q }}
                            </AccordionTrigger>
                            <AccordionContent>
                                <p class="pb-3 text-[12px] leading-relaxed text-muted-foreground sm:text-[13px]">
                                    {{ faq.a }}
                                </p>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </div>
            </div>
        </div>
    </section>
</template>
