<script setup lang="ts">
import { Separator } from '@/components/ui/separator'
import { MapPin, Mail, Globe } from 'lucide-vue-next'

const linkGroups = [
    {
        title: 'Platform',
        items: [
            { label: 'Fitur', href: '/features' },
            { label: 'Acara', href: '/events' },
            { label: 'Dokumentasi', href: '/docs' },
        ],
    },
    {
        title: 'Akses',
        items: [
            { label: 'Masuk', href: '/auth/login' },
            { label: 'Buat akun', href: '/auth/register' },
            { label: 'Dasbor', href: '/user/dashboard' },
        ],
    },
    {
        title: 'Organisasi',
        items: [
            { label: 'Doscom Udinus', href: 'https://doscom.org', external: true },
            { label: 'Universitas Dian Nuswantoro', href: 'https://dinus.ac.id', external: true },
        ],
    },
]

const mapEmbedUrl =
    'https://maps.google.com/maps?q=Gedung+D+Universitas+Dian+Nuswantoro,+Jl.+Nakula+I+No.5-11,+Semarang&t=&z=17&ie=UTF8&iwloc=B&output=embed'
</script>

<template>
    <footer class="border-t border-border/20 bg-muted/20">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">
            <div class="grid gap-12 py-16 md:py-20 lg:grid-cols-[1fr_1.1fr] lg:gap-16">
                <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-1 lg:gap-8">
                    <div>
                        <a href="/" class="inline-flex items-center gap-2.5">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-primary text-sm font-bold text-primary-foreground">D</span>
                            <span class="text-lg font-semibold tracking-tight text-foreground">DForm</span>
                        </a>
                        <p class="mt-4 max-w-xs text-sm leading-relaxed text-muted-foreground">
                            Platform sederhana untuk mengelola pendaftaran acara dari satu tempat. Dibangun oleh Doscom, Universitas Dian Nuswantoro.
                        </p>

                        <div class="mt-6 flex flex-col gap-3">
                            <div class="flex items-start gap-2.5 text-sm text-muted-foreground">
                                <MapPin class="mt-0.5 size-4 shrink-0 text-primary/70" />
                                <span>Gedung D, Universitas Dian Nuswantoro, Jl. Nakula I No.5-11, Semarang</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-sm text-muted-foreground">
                                <Mail class="size-4 shrink-0 text-primary/70" />
                                <a href="mailto:doscom@dsn.dinus.ac.id" class="transition-colors hover:text-primary">doscom@dsn.dinus.ac.id</a>
                            </div>
                            <div class="flex items-center gap-2.5 text-sm text-muted-foreground">
                                <Globe class="size-4 shrink-0 text-primary/70" />
                                <a href="https://doscom.org" target="_blank" rel="noopener" class="transition-colors hover:text-primary">doscom.org</a>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:grid-cols-3">
                        <div v-for="group in linkGroups" :key="group.title">
                            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.15em] text-foreground/50">
                                {{ group.title }}
                            </p>
                            <ul class="flex flex-col gap-2.5">
                                <li v-for="item in group.items" :key="item.label">
                                    <a
                                        :href="item.href"
                                        :target="'external' in item ? '_blank' : undefined"
                                        :rel="'external' in item ? 'noopener' : undefined"
                                        class="text-sm text-muted-foreground transition-colors duration-150 hover:text-primary"
                                    >
                                        {{ item.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-border/30 shadow-sm">
                    <iframe
                        :src="mapEmbedUrl"
                        width="100%"
                        height="100%"
                        class="min-h-[280px] lg:min-h-full"
                        style="border: 0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi Gedung D Universitas Dian Nuswantoro Semarang"
                    />
                </div>
            </div>

            <Separator class="opacity-30" />

            <div class="flex flex-col items-start justify-between gap-3 py-7 sm:flex-row sm:items-center">
                <p class="text-xs text-muted-foreground">
                    &copy; {{ new Date().getFullYear() }} DForm — Doscom, Universitas Dian Nuswantoro
                </p>
                <p class="text-xs text-muted-foreground/60">
                    Semarang, Indonesia
                </p>
            </div>
        </div>
    </footer>
</template>
