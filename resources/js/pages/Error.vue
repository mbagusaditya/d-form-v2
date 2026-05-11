<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    status: number
}>()

const RICKROLL_EMBED_SRC =
    'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&mute=0&loop=1&playlist=dQw4w9WgXcQ&controls=1&playsinline=1&rel=0&modestbranding=1'
</script>

<template>
    <Head title="404" />

    <main class="fixed inset-0 z-[2147483647] m-0 h-[100dvh] w-full bg-black p-0">
        <p class="sr-only">Error {{ props.status }}</p>

        <iframe
            class="absolute inset-0 h-full w-full border-0"
            :src="RICKROLL_EMBED_SRC"
            title="Never Gonna Give You Up"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            referrerpolicy="strict-origin-when-cross-origin"
        />

        <!-- Kanan bawah: panel kecil, responsif + safe-area -->
        <div
            class="pointer-events-none absolute inset-x-0 bottom-0 z-10 flex justify-end px-4 pb-[max(1rem,env(safe-area-inset-bottom))] pt-16 sm:px-5 sm:pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:pt-20 md:px-6 md:pb-6"
        >
            <p
                class="pointer-events-auto max-w-[min(100%,19rem)] text-pretty rounded-2xl border border-white/15 bg-black/70 px-4 py-3 text-right shadow-2xl ring-1 ring-white/5 backdrop-blur-md sm:max-w-[20rem] sm:px-5 sm:py-3.5 md:max-w-[22rem]"
                role="status"
            >
                <span class="block font-display text-2xl font-bold leading-none tracking-tight text-white sm:text-3xl md:text-4xl">404</span>
                <span
                    class="mt-2 block border-t border-white/20 pt-2 text-xs font-medium leading-snug text-white/90 sm:text-sm md:text-[0.9375rem]"
                >
                    Halaman tidak ditemukan
                </span>
            </p>
        </div>
    </main>
</template>
